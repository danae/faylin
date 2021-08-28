<?php
namespace Danae\Faylin\Model;

use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

use Danae\Faylin\Model\Traits\CreatedAtEntityTrait;
use Danae\Faylin\Model\Traits\EntityTrait;
use Danae\Faylin\Model\Traits\UpdatedAtEntityTrait;
use Danae\Faylin\Model\Traits\UserOwnedEntityTrait;
use Danae\Faylin\Utils\Traits\RouteContextTrait;


// Class that defines a collection object
final class Collection implements NormalizableInterface
{
  use EntityTrait;
  use UserOwnedEntityTrait;
  use CreatedAtEntityTrait;
  use UpdatedAtEntityTrait;
  use RouteContextTrait;


  // The name of the collection (read-write)
  private $name;

  // The description of the collection (read-write)
  private $description;

  // The tags of the collection (read-write)
  private $tags;

  // The public state of the collection (read-write)
  private $public;


  // Constructor
  public function __construct()
  {
    $this->id = null;
    $this->name = "";
    $this->description = "";
    $this->tags = [];
    $this->public = true;
    $this->nsfw = false;
    $this->userId = null;
    $this->createdAt = new \DateTime();
    $this->updatedAt = new \DateTime();
  }

  // Get the name of the collection
  public function getName(): string
  {
    return $this->name;
  }

  // Set the name of the collection
  public function setName(string $name): self
  {
    $this->name = $name;
    return $this;
  }

  // Get the description of the collection
  public function getDescription(): string
  {
    return $this->description;
  }

  // Set the description of the collection
  public function setDescription(string $description): self
  {
    $this->description = $description;
    return $this;
  }

  // Get the public state of the collection
  public function getPublic(): bool
  {
    return $this->public;
  }

  // Set the public state of the collection
  public function setPublic(bool $public): self
  {
    $this->public = $public;
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
      'public' => $this->getPublic(),

      // Entity fields
      'user' => $normalizer->normalize($context['userRepository']->get($this->getUserId()), $format, $context),
      'createdAt' => $normalizer->normalize($this->getCreatedAt(), $format, $context),
      'updatedAt' => $normalizer->normalize($this->getUpdatedAt(), $format, $context),
    ];
  }
}
