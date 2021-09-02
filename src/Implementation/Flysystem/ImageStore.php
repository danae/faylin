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

use Danae\Faylin\App\Capabilities;
use Danae\Faylin\Model\Image;
use Danae\Faylin\Model\ImageStoreInterface;
use Danae\Faylin\Model\ImageTransformInterface;


// Class that defines a store for images
final class ImageStore implements ImageStoreInterface
{
  // The filesystem to use with the store
  private $filesystem;

  // The stream factory to use with the store
  private $streamFactory;

  // The response factory to use with the store
  private $responseFactory;

  // The image transform executor to use with the store
  private $capabilities;

  // The file name format to use with the store
  private $fileNameFormat;


  // Constructor
  public function __construct(Filesystem $filesystem, StreamFactoryInterface $streamFactory, ResponseFactoryInterface $responseFactory, Capabilities $capabilities, string $fileNameFormat)
  {
    $this->filesystem = $filesystem;
    $this->streamFactory = $streamFactory;
    $this->responseFactory = $responseFactory;
    $this->capabilities = $capabilities;
    $this->fileNameFormat = $fileNameFormat;
  }

  // Return the store name for an image
  public function name(Image $image): string
  {
    return sprintf($this->fileNameFormat, $image->getId());
  }

  // Return if an image exists
  public function has(Image $image, ServerRequestInterface $request): bool
  {
    try
    {
      // Check if the file exists
      return $this->filesystem->fileExists($this->name($image));
    }
    catch (FilesystemException $ex)
    {
      throw new HttpInternalServerErrorException($request, "Could not check the image with id \"{$image->getId()}\": {$ex->getMessage()}", $ex);
    }
  }

  // Read the contents of an image to a stream
  public function read(Image $image, ServerRequestInterface $request): StreamInterface
  {
    try
    {
      // Read the file contents and decompress them
      $contents = $this->filesystem->read($this->name($image));
      $contents = gzdecode($contents);

      // Create and return a stream containing the contents
      return $this->streamFactory->createStream($contents);
    }
    catch (FilesystemException $ex)
    {
      throw new HttpInternalServerErrorException($request, "Could not read the image with id \"{$image->getId()}\": {$ex->getMessage()}", $ex);
    }
  }

  // Read the contents of an image to a response
  public function readResponse(Image $image, ServerRequestInterface $request, bool $attachment = false): ResponseInterface
  {
    // Get the file stream
    $stream = $this->read($image, $request);

    // Get the file name for the content disposition
    $fileName = $image->getName();
    $fileNameExtension = $this->capabilities->convertContentTypeToFormat($image->getContentType());
    if (!preg_match("/\.{$fileNameExtension}\$/i", $fileName))
      $fileName .= ".{$fileNameExtension}";

    // Create and return the response
    return $this->responseFactory->createResponse()
      ->withHeader('Content-Type', $image->getContentType())
      ->withHeader('Content-Length', $image->getContentLength())
      ->withHeader('Content-Disposition', ($attachment ? 'attachment' : 'inline') . "; filename=\"{$fileName}\"")
      ->withHeader('ETag', "\"{$image->getChecksum()}\"")
      ->withBody($stream);
  }

  // Write the contents of an image from a stream
  public function write(Image $image, ServerRequestInterface $request, StreamInterface $stream): void
  {
    try
    {
      // Rewind the stream
      $stream->rewind();

      // Read the contents from the stream and compress them
      $contents = $stream->getContents();
      $contents = gzencode($contents);

      // Write the file contents
      $this->filesystem->write($this->name($image), $contents);
    }
    catch (FilesystemException $ex)
    {
      throw new HttpInternalServerErrorException($request, "Could not write the image with id \"{$image->getId()}\": {$ex->getMessage()}", $ex);
    }
  }

  // Write the contents of an image from an uploaded file
  public function writeUploadedFile(Image $image, ServerRequestInterface $request, UploadedFileInterface $file): void
  {
    // Check if the upload succeeded
    if ($file->getError() === UPLOAD_ERR_INI_SIZE)
      throw new HttpException($request, "The size of the uploaded file is not supported based on the directive in the PHP configuration", 413);
    else if ($file->getError() === UPLOAD_ERR_FORM_SIZE)
      throw new HttpException($request, "The size of the uploaded file is not supported based on the directive in the upload form", 413);
    else if ($file->getError() === UPLOAD_ERR_PARTIAL)
      throw new HttpBadRequestException($request, "The uploaded file contains only partial data");
    else if ($file->getError() === UPLOAD_ERR_NO_FILE)
      throw new HttpBadRequestException($request, "The uploaded file does not contain any data");
    else if ($file->getError() === UPLOAD_ERR_NO_TMP_DIR)
      throw new HttpInternalServerErrorException($request, "Missing a temporary folder");
    else if ($file->getError() === UPLOAD_ERR_CANT_WRITE)
      throw new HttpInternalServerErrorException($request, "Failed to write the file to disk");
    else if ($file->getError() === UPLOAD_ERR_EXTENSION)
      throw new HttpInternalServerErrorException($request, "A PHP extension stopped the file upload");

    // Check if the content type of the file is supported
    if (!$this->capabilities->isContentTypeSupported($file->getClientMediaType()))
      throw new HttpException($request, "The type of the uploaded file is not supported, the supported types are " . implode(', ',$this->getSupportedContentTypes()), 415);

    // Check the size of the file is supported
    if (!$this->capabilities->isSizeSupported($file->getSize()))
      throw new HttpException($request, "The size of the uploaded file is not supported, the maximal supported size is {$this->getSupportedSize()} bytes", 413);

    // Get the stream of the uploaded file
    $stream = $file->getStream();

    // Set the image content data
    $image->setContentType($file->getClientMediaType());
    $image->setContentLength($file->getSize());
    $image->setChecksum($this->calculateChecksum($stream));

    // Write the file stream to the store
    $this->write($image, $request, $stream);
  }

  // Delete the contents of an image
  public function delete(Image $image, ServerRequestInterface $request): void
  {
    try
    {
      // Delete the file
      $this->filesystem->delete($this->name($image));
    }
    catch (FilesystemException $ex)
    {
      throw new HttpInternalServerErrorException($request, "Could not delete the image with id \"{$image->getId()}\": {$ex->getMessage()}", $ex);
    }
  }

  // Return the checksum of a stream
  public function calculateChecksum(StreamInterface $stream): string
  {
    // Rewind the stream
    $stream->rewind();

    // Calculate and return the SHA-256 hash as checksum
    return hash('sha256', $stream->getContents());
  }
}
