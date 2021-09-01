<?php
namespace Danae\Faylin\Model;

use Psr\Http\Message\StreamInterface;


// Interface that defines an image transform
interface ImageTransformInterface
{
  // Return the transformation to apply to the image
  public function getTransformations(): ?string;

  // Return the content type of the transform
  public function getContentType(): string;

  // Execute the image transform and return the result
  public function execute(): ImageTransformResult;
}
