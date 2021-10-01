<?php
namespace Danae\Faylin\Implementation\Flysystem;

use Imagecow\Image as ImagecowImage;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

use Danae\Faylin\App\Capabilities;
use Danae\Faylin\Model\Image;
use Danae\Faylin\Model\ImageStoreInterface;
use Danae\Faylin\Model\ImageTransform;
use Danae\Faylin\Model\ImageTransformExecutorInterface;
use Danae\Faylin\Model\ImageTransformResponse;
use Danae\Faylin\Model\ImageTransformStoreInterface;


// Class that defines an executor for image transforms
final class ImageTransformExecutor implements ImageTransformExecutorInterface
{
  // The image store to use with the executor
  private $imageStore;

  // The image transform store to use with the executor
  private $imageTransformStore;

  // The stream factory interface to use with the executor
  private $streamFactory;

  // The response factory interface to use with the executor
  private $responseFactory;

  // The capabilities to use with the executor
  private $capabilities;


  // Constructor
  public function __construct(ImageStoreInterface $imageStore, ImageTransformStoreInterface $imageTransformStore, StreamFactoryInterface $streamFactory, ResponseFactoryInterface $responseFactory, Capabilities $capabilities)
  {
    $this->imageStore = $imageStore;
    $this->imageTransformStore = $imageTransformStore;
    $this->streamFactory = $streamFactory;
    $this->responseFactory = $responseFactory;
    $this->capabilities = $capabilities;
  }

  // Return a transformed image as a transform result
  public function transform(Image $image, ImageTransform $transform, ServerRequestInterface $request): ImageTransformResponse
  {
    // Check if a cached version of the transformed image exists
    if ($this->imageTransformStore->has($image, $transform, $request))
    {
      // Get the contents of the transformed image from the cache
      $stream = $this->imageTransformStore->read($image, $transform, $request);
    }
    else
    {
      // Get the contents of the image as a stream and transform it
      $stream = $this->execute($this->imageStore->read($image, $request), $image, $transform);

      // Write the stream to the cache
      $this->imageTransformStore->write($image, $transform, $request, $stream);
    }

    // Return the result
    return new ImageTransformResponse($stream, $transform->getContentType(), $stream->getSize(), $this->imageStore->calculateChecksum($stream));
  }

  // Return a transformed image as a transform result
  public function transformResponse(Image $image, ImageTransform $transform, ServerRequestInterface $request, bool $attachment = false): ResponseInterface
  {
    // Get the transform result
    $result = $this->transform($image, $transform, $request);

    // Get the file name for the content disposition
    $fileName = $image->getTitle();
    $fileNameExtension = $this->capabilities->convertContentTypeToFormat($result->getContentType());
    if (!preg_match("/\.{$fileNameExtension}\$/i", $fileName))
      $fileName .= ".{$fileNameExtension}";

    // Create and return the response
    return $this->responseFactory->createResponse()
      ->withHeader('Content-Type', $result->getContentType())
      ->withHeader('Content-Length', $result->getContentLength())
      ->withHeader('Content-Disposition', ($attachment ? 'attachment' : 'inline') . "; filename=\"{$fileName}\"")
      ->withHeader('ETag', "\"{$result->getChecksum()}\"")
      ->withBody($result->getStream());
  }


  // Execute the image transformation
  private function execute(StreamInterface $stream, Image $image, ImageTransform $transform): StreamInterface
  {
    // Create the Imagecow image and convert SVG images to PNG first if they must be converted
    $imagecow = ImagecowImage::fromString($stream->getContents(), ImagecowImage::LIB_IMAGICK);
    if ($image->getContentType() === 'image/svg+xml' && $transform->getContentType() !== 'image/svg+xml')
      $imagecow->format('png');

    // Convert the image to the appropritate format if applicable
    if ($transform->getContentType() != $image->getContentType())
      $imagecow->format($this->capabilities->convertContentTypeToFormat($transform->getContentType()));

    // Parse and iterate over the transformations
    $transformations = explode('|', $transform->getTransformations());
    foreach ($transformations as $transformation)
    {
      // Parse the transformation
      [$transformation, $params] = explode(':', $transformation, 2);
      if (!empty($params))
        $params = explode(',', $params);

      // Execute a resize transformation
      if ($transformation === 'resize')
      {
        if (count($params) < 1 || count($params) > 3)
          throw new \InvalidArgumentException("The resize transformation needs between 1 and 3 arguments");

        $width = $params[0];
        $height = $params[1] ?? 0;
        $cover = $params[2] ?? false;

        $imagecow->resize($width, $height, $cover);
      }

      // Execute a crop transformation
      else if ($transformation === 'crop')
      {
        if (count($params) < 2 || count($params) > 4)
          throw new \InvalidArgumentException("The resize transformation needs between 2 and 4 arguments");

        $width = $params[0];
        $height = $params[1];
        $x = $params[2] ?? 'entropy';
        $y = $params[3] ?? 'middle';

        if ($x === 'entropy')
          $x = ImagecowImage::CROP_ENTROPY;
        else if ($x === 'balanced')
          $x = ImagecowImage::CROP_BALANCED;
        else if ($x === 'face')
          $x = ImagecowImage::CROP_FACE;

        $imagecow->resizeCrop($width, $height, $x, $y);
      }

      // Execute a rotate transformation
      else if ($transformation === 'rotate')
      {
        if (count($params) < 1 || count($params) > 1)
          throw new \InvalidArgumentException("The rotate transformation needs exactly 1 argument");

        $angle = $params[0];

        $imagecow->rotate($angle);
      }

      // Execute a fliphorizontal transformation
      else if ($transformation === 'fliphorizontal')
      {
        if (count($params) > 0)
          throw new \InvalidArgumentException("The fliphorizontal transformation needs no arguments");

        $imagecow->flop();
      }

      // Execute a flipvertical transformation
      else if ($transformation === 'flipvertical')
      {
        if (count($params) > 0)
          throw new \InvalidArgumentException("The flipvertical transformation needs no arguments");

        $imagecow->flip();
      }

      // Execute a blur transformation
      else if ($transformation === 'blur')
      {
        $loops = $params[0] ?? 4;

        $imagecow->blur($loops);
      }

      // Undefined transformation
      else
        throw new \InvalidArgumentException("\"{$transformation}\" is not a valid transformation");
    }

    // Return the contents of the transformed image as a stream
    return $this->streamFactory->createStream($imagecow->getString());
  }
}
