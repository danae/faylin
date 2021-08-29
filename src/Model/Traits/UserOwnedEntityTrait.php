<?php
namespace Danae\Faylin\Model\Traits;

use Danae\Faylin\Model\User;


// Trait that defines an entity that is owned by a user
trait UserOwnedEntityTrait
{
  // The user that owns the entity
  private $user;


  // Get the user identifier of the entity
  public function getUser(): ?User
  {
    return $this->user;
  }

  // Set the user identifier of the entity
  public function setUser(User $user): self
  {
    $this->user = $user;
    return $this;
  }
}
