<?php
namespace Danae\Faylin\Model\Traits;


// Trait that defines an entity that is owned by a client
trait ClientOwnedEntityTrait
{
  // The identifier of the client that owns the entity
  private $clientId;


  // Get the client identifier of the entity
  public function getClientId(): ?string
  {
    return $this->clientId;
  }

  // Set the client identifier of the entity
  public function setClientId(string $clientId): self
  {
    $this->clientId = $clientId;
    return $this;
  }
}
