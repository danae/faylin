<?php
namespace Danae\Faylin\Model;

use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

use Danae\Faylin\Model\Traits\CreatedAtEntityTrait;
use Danae\Faylin\Model\Traits\EntityTrait;
use Danae\Faylin\Model\Traits\UpdatedAtEntityTrait;
use Danae\Faylin\Model\Traits\UserOwnedEntityTrait;
use Danae\Faylin\Utils\Traits\RouteContextTrait;


// Class that defines an image object
final class Image implements NormalizableInterface
{
  use EntityTrait;
  use UserOwnedEntityTrait;
  use CreatedAtEntityTrait;
  use UpdatedAtEntityTrait;
  use RouteContextTrait;


  // The name of the image (read-write)
  private $name;

  // The description of the image (read-write)
  private $description;

  // The tags of the image (read-write)
  private $tags;

  // The public state of the image (read-write)
  private $public;

  // The NSFW state of the image (read-write)
  private $nsfw;

  // The content type of the image (read-only)
  private $contentType;

  // The content length of the image (read-only)
  private $contentLength;


  // Constructor
  public function __construct()
  {
    $this->id = null;
    $this->name = "";
    $this->description = "";
    $this->tags = [];
    $this->public = true;
    $this->nsfw = false;
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

  // Get the tags of the image
  public function getTags(): array
  {
    return $this->tags;
  }

  // Set the tags of the image
  public function setTags(array $tags): self
  {
    $this->tags = $tags;
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

  // Normalize an image and return the normalized array
  public function normalize(NormalizerInterface $normalizer, string $format = null, array $context = []): array
  {
    return [
      // Identifier
      'id' => $this->getId(),

      // Read-write class fields
      'name' => $this->getName(),
      'description' => $this->getDescription(),
      'tags' => $this->getTags(),
      'public' => $this->getPublic(),
      'nsfw' => $this->getNsfw(),

      // Read-only class fields
      'contentType' => $this->getContentType(),
      'contentLength' => $this->getContentLength(),

      // Entity fields
      'user' => $normalizer->normalize($context['userRepository']->get($this->getUserId()), $format, $context),
      'createdAt' => $normalizer->normalize($this->getCreatedAt(), $format, $context),
      'updatedAt' => $normalizer->normalize($this->getUpdatedAt(), $format, $context),

      // Additional fields
      'downloadUrl' => $this->fullUrlFor($context['request'], 'images.download', ['id' => $this->getId(), 'extension' => $context['supportedContentTypes'][$this->getContentType()]]),
    ];
  }
}
