<?php
namespace Danae\Faylin\Store;

use Imagecow\Image as ImagecowImage;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\StreamInterface;
use Slim\Exception\HttpBadRequestException;

use Danae\Faylin\Model\Image;


// Class that defines an image manupulation
final class ImageManipulation
{
  // The file name format for a cached manipulation file
  private const CACHE_FILE_FORMAT = 'cache/%s.gz';


  // The image to execute the manipulation on
  private $image;

  // The stream to execute the manipulation on
  private $stream;

  // The transform to use with the manipulation
  private $transform;

  // The format to use with the manipulation
  private $format;

  // The store to use with the manipulation
  private $store;


  // Constructor
  public function __construct(Image $image, StreamInterface $stream, ?string $transform, ?string $format, Store $store)
  {
    $this->image = $image;
    $this->stream = $stream;
    $this->transform = $transform;
    $this->format = $format;
    $this->store = $store;

    if ($this->format !== null)
      $this->format = strtolower($this->format);
    else
      $this->format = $this->store->convertContentTypeToFormat($this->image->getContentType());
  }

  // Return the hash for this manipulation
  public function hash(): string
  {
    $transformHash = hash('sha256', $this->transform);
    return sprintf('%s.%s.%s.%s', $this->image->getId(), $this->image->getChecksum(), $transformHash, $this->format);
  }

  // Manipulate the image and return a content response
  public function manipulate(Request $request, Response $response): ImageContentResponse
  {
    // Get the actual format
    if (empty($this->format) || !$this->store->isContentFormatSupported($this->format))
      throw new HttpBadRequestException($request, "The requested type is not supported, the supported types are " . implode(', ', $this->store->getSupportedContentTypes()));

    // Get the stream
    $stream = $this->stream;

    // Get the content metadata
    $name = $this->image->getName();

    $contentName = preg_match("/\.{$this->format}\$/i", $name) ?$name : "{$name}.{$this->format}";
    $contentType = $this->image->getContentType();
    $contentLength = $this->image->getContentLength();
    $contentChecksum = $this->hash();

    // Check if a cached version of the manipulation exists
    $cacheFile = sprintf(self::CACHE_FILE_FORMAT, $this->hash());
    if ($this->store->has($cacheFile))
    {
      // Adjust the stream to the cached version of the manipulation
      $stream = $this->store->read($cacheFile);

      // Adjust the content metadata
      $contentType = $this->store->convertContentFormatToType($this->format);
      $contentLength = $stream->getSize();
    }

    // Check if the image needs to be manipulated
    else if ($this->transform !== null || $this->format !== $this->store->convertContentTypeToFormat($this->image->getContentType()))
    {
      // Create the image
      $imagecow = ImagecowImage::fromString((string)$this->stream, ImagecowImage::LIB_IMAGICK);

      // Convert SVG images to PNG first if they must be converted
      if ($this->image->getContentType() === 'image/svg+xml' && $format !== null && $format !== 'svg')
        $imagecow->format('png');

      // Transform the image if applicable
      if ($this->transform !== null)
        $imagecow->transform($this->transform);

      // Convert the image if applicable
      if ($this->format !== $this->store->convertContentTypeToFormat($this->image->getContentType()))
        $imagecow->format($this->format);

      // Adjust the stream and save it in the cache
      $stream = $this->store->getStreamFactory()->createStream($imagecow->getString());
      $this->store->write($cacheFile, $stream);

      // Adjust the content metadata
      $contentType = $this->store->convertContentFormatToType($this->format);
      $contentLength = $stream->getSize();
    }

    // Create and return the response
    return new ImageContentResponse($this->image, $stream, $contentType, $contentLength, $contentChecksum);
  }
}
