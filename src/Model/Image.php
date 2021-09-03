<?php
namespace Danae\Faylin\Model;

use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

use Danae\Faylin\Utils\Traits\RouteContextTrait;


// Class that defines an image object
final class Image implements NormalizableInterface
{
  use Traits\EntityTrait;
  use Traits\NamedEntityTrait;
  use Traits\DatedEntityTrait;
  use Traits\UserOwnedEntityTrait;
  use RouteContextTrait;


  // The title of the image (read-write)
  private $title;

  // The description of the image (read-write)
  private $description;

  // The public state of the image (read-write)
  private $public;

  // The NSFW state of the image (read-write)
  private $nsfw;

  // The content type of the image (read-only)
  private $contentType;

  // The content length of the image (read-only)
  private $contentLength;

  // The checksum of the image (read-only)
  private $checksum;


  // Constructor
  public function __construct()
  {
    $this->id = null;
    $this->name = "";
    $this->createdAt = new \DateTime();
    $this->updatedAt = new \DateTime();
    $this->userId = null;
    $this->title = "";
    $this->description = "";
    $this->public = true;
    $this->nsfw = false;
    $this->contentType = "";
    $this->contentLength = 0;
    $this->checksum = "";
  }

  // Get the title of the image
  public function getTitle(): string
  {
    return $this->title;
  }

  // Set the title of the image
  public function setTitle(string $title): self
  {
    $this->title = $title;
    return $this;
  }

  // Get the description of the image
  public function getDescription(): string
  {
    return $this->description;
  }

  // Set the description of the image
  public function setDescription(string $description): self
  {
    $this->description = $description;
    return $this;
  }

  // Get the public state of the image
  public function getPublic(): bool
  {
    return $this->public;
  }

  // Set the public state of the image
  public function setPublic(bool $public): self
  {
    $this->public = $public;
    return $this;
  }

  // Get the NSFW state of the image
  public function getNsfw(): bool
  {
    return $this->nsfw;
  }

  // Set the NSFW state of the image
  public function setNsfw(bool $nsfw): self
  {
    $this->nsfw = $nsfw;
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

  // Get the checksum of the image
  public function getChecksum(): string
  {
    return $this->checksum;
  }

  // Set the checksum of the image
  public function setChecksum(string $checksum): self
  {
    $this->checksum = $checksum;
    return $this;
  }


  // Normalize an image and return the normalized array
  public function normalize(NormalizerInterface $normalizer, string $format = null, array $context = []): array
  {
    return [
      // Entity fields
      'id' => $this->getId()->toString(),
      'name' => $this->getName(),
      'createdAt' => $normalizer->normalize($this->getCreatedAt(), $format, $context),
      'updatedAt' => $normalizer->normalize($this->getUpdatedAt(), $format, $context),
      'user' => $normalizer->normalize($this->getUser(), $format, $context),

      // Read-write class fields
      'title' => $this->getTitle(),
      'description' => $this->getDescription(),
      'public' => $this->getPublic(),
      'nsfw' => $this->getNsfw(),

      // Read-only class fields
      'contentType' => $this->getContentType(),
      'contentLength' => $this->getContentLength(),
      'checksum' => $this->getChecksum(),

      // Additional fields
      'downloadUrl' => $this->fullUrlFor($context['request'], 'images.download', ['imageId' => $this->getId(), 'format' => $context['capabilities']->convertContentTypeToFormat($this->getContentType())]),
      'thumbnailUrl' => $this->fullUrlFor($context['request'], 'images.download', ['imageId' => $this->getId(), 'format' => 'png'], ['transform' => 'crop:200,200']),
    ];
  }
}
