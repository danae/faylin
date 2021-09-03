<?php
namespace Danae\Faylin\Model;

use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;


// Class that defines a user object
final class User implements NormalizableInterface
{
  use Traits\EntityTrait;
  use Traits\NamedEntityTrait;
  use Traits\DatedEntityTrait;


  // The email address of the user (internal)
  private $email;

  // The password hash of the user (internal)
  private $passwordHash;


  // The description of the user (read-write)
  private $description;

  // The public state of the user (read-write)
  private $public;


  // Constructor
  public function __construct()
  {
    $this->id = null;
    $this->name = "";
    $this->createdAt = new \DateTime();
    $this->updatedAt = new \DateTime();
    $this->email = "";
    $this->passwordHash = "";
    $this->description = "";
    $this->public = true;
  }

  // Get the email address of the user
  public function getEmail(): string
  {
    return $this->email;
  }

  // Set the email address of the user
  public function setEmail(string $email): self
  {
    $this->email = $email;
    return $this;
  }

  // Get the password hash of the user
  public function getPasswordHash(): string
  {
    return $this->passwordHash;
  }

  // Set the password hash of the user
  public function setPasswordHash(string $passwordHash): self
  {
    $this->passwordHash = $passwordHash;
    return $this;
  }

  // Verify a password against the password hash of the user
  public function verifyPassword(string $password): bool
  {
    return password_verify($password, $this->getPasswordHash());
  }

  // Hash a password and set it as the password hash of the user
  public function hashPassword(string $password): self
  {
    $this->setPasswordHash(password_hash($password, \PASSWORD_DEFAULT));
    return $this;
  }

  // Get the description of the user
  public function getDescription(): string
  {
    return $this->description;
  }

  // Set the description of the user
  public function setDescription(string $description): self
  {
    $this->description = $description;
    return $this;
  }

  // Get the public state of the user
  public function getPublic(): bool
  {
    return $this->public;
  }

  // Set the public state of the user
  public function setPublic(bool $public): self
  {
    $this->public = $public;
    return $this;
  }


  // Normalize a user and return the normalized array
  public function normalize(NormalizerInterface $normalizer, string $format = null, array $context = []): array
  {
    return [
      // Entity fields
      'id' => $this->getId(),
      'name' => $this->getName(),
      'createdAt' => $normalizer->normalize($this->getCreatedAt(), $format, $context),
      'updatedAt' => $normalizer->normalize($this->getUpdatedAt(), $format, $context),

      // Internal class fields
      'email' => $this->getEmail(),

      // Read-write class fields
      'description' => $this->getDescription(),
      'public' => $this->getPublic(),
    ];
  }
}
