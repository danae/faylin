<?php
namespace Danae\Faylin\Model\Traits;


// Trait that defines an entity
trait EntityTrait
{
  // The identifier of the entity
  private $id;


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
}
