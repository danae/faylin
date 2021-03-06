<?php
namespace Danae\Faylin\Model;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;


// Interface that defines a store for the contents of images
interface ImageStoreInterface
{
  // Return if an image exists
  public function has(Image $image, ServerRequestInterface $request): bool;

  // Read the contents of an image to a stream
  public function read(Image $image, ServerRequestInterface $request): StreamInterface;

  // Read the contents of an image to a response
  public function readResponse(Image $image, ServerRequestInterface $request, bool $attachment = false): ResponseInterface;

  // Write the contents of an image from a stream
  public function write(Image $image, ServerRequestInterface $request, StreamInterface $stream): void;

  // Write the contents of an image from an uploaded file
  public function writeUploadedFile(Image $image, ServerRequestInterface $request, UploadedFileInterface $file): void;

  // Delete the contents of an image
  public function delete(Image $image, ServerRequestInterface $request): void;

  // Return the checksum of a stream
  public function calculateChecksum(StreamInterface $stream): string;
}
