<?php
namespace Danae\Faylin\Implementation\Flysystem;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use Slim\Exception\HttpInternalServerErrorException;

use Danae\Faylin\Model\Image;
use Danae\Faylin\Model\ImageTransform;
use Danae\Faylin\Model\ImageTransformStoreInterface;


// Class that defines a store for cached image transforms
final class ImageTransformStore implements ImageTransformStoreInterface
{
  // The filesystem to use with the store
  private $filesystem;

  // The stream factory to use with the store
  private $streamFactory;

  // The file name format to use with the store
  private $fileNameFormat;


  // Constructor
  public function __construct(Filesystem $filesystem, StreamFactoryInterface $streamFactory, string $fileNameFormat)
  {
    $this->filesystem = $filesystem;
    $this->streamFactory = $streamFactory;
    $this->fileNameFormat = $fileNameFormat;
  }

  // Return the store name for an image
  public function name(Image $image, ImageTransform $transform): string
  {
    return sprintf($this->fileNameFormat, sprintf('%s.%s.%s', $image->getId(), $image->getChecksum(), hash('md5', "{$transform->getTransformations()}.{$transform->getContentType()}")));
  }

  // Return if a cached image transform exists
  public function has(Image $image, ImageTransform $transform, ServerRequestInterface $request): bool
  {
    try
    {
      // Check if the file exists
      return $this->filesystem->fileExists($this->name($image, $transform));
    }
    catch (FilesystemException $ex)
    {
      throw new HttpInternalServerErrorException($request, "Could not check the transform for the image with id \"{$image->getId()}\": {$ex->getMessage()}", $ex);
    }
  }

  // Read the contents of a cached image transform to a stream
  public function read(Image $image, ImageTransform $transform, ServerRequestInterface $request): StreamInterface
  {
    try
    {
      // Read the file contents and decompress them
      $contents = $this->filesystem->read($this->name($image, $transform));
      $contents = gzdecode($contents);

      // Create and return a stream containing the contents
      return $this->streamFactory->createStream($contents);
    }
    catch (FilesystemException $ex)
    {
      throw new HttpInternalServerErrorException($request, "Could not read the transform for the image with id \"{$image->getId()}\": {$ex->getMessage()}", $ex);
    }
  }

  // Write the contents of a cached image transform from a stream
  public function write(Image $image, ImageTransform $transform, ServerRequestInterface $request, StreamInterface $stream): void
  {
    try
    {
      // Rewind the stream
      $stream->rewind();

      // Read the contents from the stream and compress them
      $contents = $stream->getContents();
      $contents = gzencode($contents);

      // Write the file contents
      $this->filesystem->write($this->name($image, $transform), $contents);
    }
    catch (FilesystemException $ex)
    {
      throw new HttpInternalServerErrorException($request, "Could not write the transform for the image with id \"{$image->getId()}\": {$ex->getMessage()}", $ex);
    }
  }

  // Delete the contents of a cached image transform
  public function delete(Image $image, ImageTransform $transform, ServerRequestInterface $request): void
  {
    try
    {
      // Delete the file
      $this->filesystem->delete($this->name($image, $transform));
    }
    catch (FilesystemException $ex)
    {
      throw new HttpInternalServerErrorException($request, "Could not delete the transform for the image with id \"{$image->getId()}\": {$ex->getMessage()}", $ex);
    }
  }
}
