<?php
namespace Danae\Faylin\Store;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\StreamInterface;

use Danae\Faylin\Model\Image;


// Class that defines a reponse with the contents of an image
final class ImageContentResponse
{
  // The image contained in the response
  private $image;

  // The stream contained in the response
  private $stream;

  // The content type of the response
  private $contentType;

  // The content length of the response
  private $contentLength;

  // The checksum of the response to use in the ETag header
  private $contentChecksum;


  // Constructor
  public function __construct(Image $image, StreamInterface $stream, string $contentType, int $contentLength, string $contentChecksum)
  {
    $this->image = $image;
    $this->stream = $stream;
    $this->contentType = $contentType;
    $this->contentLength = $contentLength;
    $this->contentChecksum = $contentChecksum;
  }


  // Fill a PSR-7 response with the content response
  public function respond(Response $response, bool $attachment = false): Response
  {
    return $response
      ->withHeader('Content-Disposition', $attachment ? 'attachment' : 'inline')
      ->withHeader('Content-Type', $this->contentType)
      ->withHeader('Content-Length', $this->contentLength)
      ->withHeader('ETag', "\"{$this->contentChecksum}\"")
      ->withBody($this->stream);
  }

  // Create a PSR-7 response with the content response
  public function create(ResponseFactoryInterface $responseFactory, bool $attachment = false): Response
  {
    $response = $responseFactory->createResponse();
    return $this->respond($response);
  }
}
