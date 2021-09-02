<?php
namespace Danae\Faylin\Model;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;


// Interface that defines an executor of image transforms
interface ImageTransformExecutorInterface
{
  // Return a transformed image as a transform result
  public function transform(Image $image, ImageTransform $transform, ServerRequestInterface $request): ImageTransformResponse;

  // Return a transformed image as a transform result
  public function transformResponse(Image $image, ImageTransform $transform, ServerRequestInterface $request, bool $attachment = false): ResponseInterface;
}
