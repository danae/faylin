<?php
namespace Danae\Faylin\Model;

use Psr\Http\Message\StreamInterface;


// Class that defines a response containing a transformed image
final class ImageTransformResponse
{
  // The contents of the response as a stream
  private $stream;

  // The content type of the response
  private $contentType;

  // The content length of the response
  private $contentLength;

  // The checksum of the response
  private $checksum;


  // Constructor
  public function __construct(StreamInterface $stream, string $contentType, int $contentLength, string $checksum)
  {
    $this->stream = $stream;
    $this->contentType = $contentType;
    $this->contentLength = $contentLength;
    $this->checksum = $checksum;
  }

  // Return the contents of the response as a stream
  public function getStream(): StreamInterface
  {
    return $this->stream;
  }

  // Return the content type of the response
  public function getContentType(): string
  {
    return $this->contentType;
  }

  // Return content length of the response
  public function getContentLength(): string
  {
    return $this->contentLength;
  }

  // Return checksum of the response
  public function getChecksum(): string
  {
    return $this->checksum;
  }
}
