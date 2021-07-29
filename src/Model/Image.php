<?php
namespace Danae\Faylin\Model;

use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

use Danae\Faylin\Model\Traits\EntityTrait;
use Danae\Faylin\Model\Traits\UserOwnedEntityTrait;
use Danae\Faylin\Model\Traits\DatedEntityTrait;
use Danae\Faylin\Utils\Traits\RouteContextTrait;


// Class that defines an image object
final class Image implements NormalizableInterface
{
  use EntityTrait;
  use UserOwnedEntityTrait;
  use DatedEntityTrait;
  use RouteContextTrait;


  // The name of the image (read-write)
  private $name;

  // The content type of the image (read-only)
  private $contentType;

  // The content length of the image (read-only)
  private $contentLength;


  // Constructor
  public function __construct()
  {
    $this->id = null;
    $this->name = "";
    $this->contentType = "";
    $this->contentLength = 0;
    $this->userId = null;
    $this->createdAt = new \DateTime();
    $this->updatedAt = new \DateTime();
  }

  // Get the name of the image
  public function getName(): string
  {
    return $this->name;
  }

  // Set the name of the image
  public function setName(string $name): self
  {
    $this->name = $name;
    return $this;
  }

  // Get the content type of the image
  public function getContentType(): string
  {
    return $this->contentType;
  }

  // Set the content type of the image
  public function setContentType(string $contentType): self
  {
    $this->contentType = $contentType;
    return $this;
  }

  // Get the content length of the image
  public function getContentLength(): int
  {
    return $this->contentLength;
  }

  // Set the content length of the image
  public function setContentLength(int $contentLength): self
  {
    $this->contentLength = $contentLength;
    return $this;
  }

  // Normalize an image and return the normalized array
  public function normalize(NormalizerInterface $normalizer, string $format = null, array $context = []): array
  {
    return [
      // Identifier
      'id' => $this->getId(),

      // Read-write class fields
      'name' => $this->getName(),

      // Read-only class fields
      'contentType' => $this->getContentType(),
      'contentLength' => $this->getContentLength(),

      // Entity fields
      'user' => $normalizer->normalize($this->fetchUserFrom($context['userRepository']), $format, $context),
      'createdAt' => $normalizer->normalize($this->getCreatedAt(), $format, $context),
      'updatedAt' => $normalizer->normalize($this->getUpdatedAt(), $format, $context),

      // Additional fields
      'downloadUrl' => $this->fullUrlFor($context['request'], 'images.download', ['id' => $this->getId()]),
      'downloadAttachmentUrl' => $this->fullUrlFor($context['request'], 'images.download', ['id' => $this->getId()], ['attachment' => true]),
    ];
  }
}
