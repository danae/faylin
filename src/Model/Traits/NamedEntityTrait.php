<?php
namespace Danae\Faylin\Model\Traits;


// Trait that defines an entity that has a name
trait NamedEntityTrait
{
  // The name of the entity
  private $name;


  // Get the name of the entity
  public function getName(): ?string
  {
    return $this->name;
  }

  // Set the name of the entity
  public function setName(string $name): self
  {
    $this->name = $name;
    return $this;
  }
}
