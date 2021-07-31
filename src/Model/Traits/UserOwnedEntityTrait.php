<?php
namespace Danae\Faylin\Model\Traits;


// Trait that defines an entity that is owned by a user
trait UserOwnedEntityTrait
{
  // The identifier of the user that owns the entity
  private $userId;


  // Get the user identifier of the entity
  public function getUserId(): ?string
  {
    return $this->userId;
  }

  // Set the user identifier of the entity
  public function setUserId(string $userId): self
  {
    $this->userId = $userId;
    return $this;
  }
}
