<?php
namespace Danae\Faylin\Model;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;


// Interface that defines a store for the contents of images
interface ImageStoreInterface
{
  // Return if an image exists
  public function has(Image $image): bool;

  // Read the contents of an image to a stream
  public function read(Image $image): StreamInterface;

  // Read the contents of an image to a stream after transforming them
  public function readAndTransform(Image $image, ImageTransformInterface $transform): StreamInterface;

  // Read the contents of an image to a response
  public function readResponse(Image $image, bool $attachment = false): ResponseInterface;

  // Read the contents of an image to a response after transforming them
  public function readAndTransformResponse(Image $image, ImageTransformInterface $transform, bool $attachment = false): ResponseInterface;

  // Write the contents of an image from a stream
  public function write(Image $image, StreamInterface $stream): void;

  // Write the contents of an image from an uploaded file
  public function writeUploadedFile(Image $image, UploadedFileInterface $file): void;

  // Delete the contents of an image
  public function delete(Image $image): void;
}
