<?php
namespace Danae\Faylin\Model;

use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;


// Class that defines a collection object
final class Collection implements NormalizableInterface
{
  use Traits\EntityTrait;
  use Traits\NamedEntityTrait;
  use Traits\DatedEntityTrait;
  use Traits\UserOwnedEntityTrait;


  // The images of the collection
  private $images;

  // The title of the collection (read-write)
  private $title;

  // The description of the collection (read-write)
  private $description;

  // The public state of the collection (read-write)
  private $public;


  // Constructor
  public function __construct()
  {
    $this->id = null;
    $this->name = "";
    $this->createdAt = new \DateTime();
    $this->updatedAt = new \DateTime();
    $this->user = null;
    $this->images = [];
    $this->title = "";
    $this->description = "";
    $this->public = true;
  }

  // Get the images of the collection
  public function getImages(): array
  {
    return $this->images;
  }

  // Set the images of the collection
  public function setImages(array $images): self
  {
    $this->images = $images;
    return $this;
  }

  // Add an image to the collection
  public function addImage(Image $imageToAdd): self
  {
    $this->images[] = $imageToAdd;
    return $this;
  }

  // Remove an image from the collection
  public function removeImage(Image $imageToRemove): self
  {
    $this->images = array_filter($this->images, fn($image) => $image->getId() != $imageToRemove->getId());
    return $this;
  }

  // Get the title of the collection
  public function getTitle(): string
  {
    return $this->title;
  }

  // Set the title of the collection
  public function setTitle(string $title): self
  {
    $this->title = $title;
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
      // Entity fields
      'id' => $this->getId()->toString(),
      'name' => $this->getName(),
      'createdAt' => $normalizer->normalize($this->getCreatedAt(), $format, $context),
      'updatedAt' => $normalizer->normalize($this->getUpdatedAt(), $format, $context),
      'user' => $normalizer->normalize($this->getUser(), $format, $context),

      // Read-write class fields
      'images' => $normalizer->normalize($this->getImages(), $format, $context),
      'title' => $this->getTitle(),
      'description' => $this->getDescription(),
      'public' => $this->getPublic(),
    ];
  }
}
