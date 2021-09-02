<?php
namespace Danae\Faylin\Model;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;


// Interface that defines a store for the contents of cached image transforms
interface ImageTransformStoreInterface
{
  // Return if a cached image transform exists
  public function has(Image $image, ImageTransformInterface $transform, ServerRequestInterface $request): bool;

  // Read the contents of a cached image transform to a stream
  public function read(Image $image, ImageTransformInterface $transform, ServerRequestInterface $request): StreamInterface;

  // Write the contents of a cached image transform from a stream
  public function write(Image $image, ImageTransformInterface $transform, ServerRequestInterface $request, StreamInterface $stream): void;

  // Delete the contents of a cached image transform
  public function delete(Image $image, ImageTransformInterface $transform, ServerRequestInterface $request): void;
}
