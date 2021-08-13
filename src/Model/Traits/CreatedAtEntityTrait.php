<?php
namespace Danae\Faylin\Model\Traits;


// Trait that defines an entity with a created date
trait CreatedAtEntityTrait
{
  // The date when the entity was created
  private $createdAt;


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
}
