<?php
namespace Danae\Faylin\Model;


// Interface that defines the result of an image transform
interface ImageTransformResultInterface
{
  // Return the image that was used
  public function getImage(): Image;

  // Return the image transform that was used
  public function getTransform(): ImageTransformInterface;

  // Return the resulting contents of the transform as a stream
  public function getStream(): StreamInterface;

  // Return the resulting content type
  public function getContentType(): string;

  // Return the resulting content length
  public function getContentLength(): string;

  // Return the resulting checksum
  public function getChecksum(): string;
}
