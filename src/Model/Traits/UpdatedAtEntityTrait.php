<?php
namespace Danae\Faylin\Model\Traits;


// Trait that defines an entity with an updated date
trait UpdatedAtEntityTrait
{
  // The date when the entity was updated
  private $updatedAt;


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
