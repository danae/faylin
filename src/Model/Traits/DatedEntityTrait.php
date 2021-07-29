<?php
namespace Danae\Faylin\Model\Traits;


// Trait that defines an entity with created and updated dates
trait DatedEntityTrait
{
  // The date when the entity was created
  private $createdAt;

  // The date when the entity was updated
  private $updatedAt;


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
