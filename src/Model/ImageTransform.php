<?php
namespace Danae\Faylin\Model;


// Class that defines an image transform
final class ImageTransform
{
  // The transformations to apply to the image
  private $transformations;

  // The content type of the transform
  private $contentType;


  // Constructor
  public function __construct(?string $transformations, string $contentType)
  {
    $this->transformations= $transformations;
    $this->contentType = $contentType;
  }

  // Return the transformation to apply to the image
  public function getTransformations(): ?string
  {
    return $this->transformations;
  }

  // Return the content type of the transform
  public function getContentType(): string
  {
    return $this->contentType;
  }
}
