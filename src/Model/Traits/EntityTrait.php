<?php
namespace Danae\Faylin\Model\Traits;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;


// Trait that defines an entity
trait EntityTrait
{
  // The identifier of the entity
  private $id;

  // The date when the entity was created
  private $createdAt;

  // The date when the entity was updated
  private $updatedAt;


  // Get the identifier of the entity
  public function getId(): ?string
  {
    return $this->id;
  }

  // Set the identifier of the entity
  public function setId(string $id): self
  {
    $this->id = $id;
    return $this;
  }

  // Get the date when the entity was created
  public function getCreatedAt(): \DateTime
  {
    return $this->createdAt;
  }

  // Set the date when the entity was created
  public function setCreatedAt(\DateTime $createdAt): self
  {
    $this->createdAt = $createdAt;
    return $this;
  }

  // Get the date when the entity was updated
  public function getUpdatedAt(): \DateTime
  {
    return $this->updatedAt;
  }

  // Set the date when the entity was updated
  public function setUpdatedAt(\DateTime $updatedAt): self
  {
    $this->updatedAt = $updatedAt;
    return $this;
  }
}
