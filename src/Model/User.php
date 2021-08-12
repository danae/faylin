<?php
namespace Danae\Faylin\Model;

use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

use Danae\Faylin\Model\Traits\EntityTrait;
use Danae\Faylin\Model\Traits\DatedEntityTrait;


// Class that defines a user object
final class User implements NormalizableInterface
{
  use EntityTrait;
  use DatedEntityTrait;


  // The email address of the user (internal)
  private $email;

  // The password hash of the user (internal)
  private $passwordHash;

  // The name of the user (read-write)
  private $name;

  // The description of the user (read-write)
  private $description;

  // The public state of the user (read-write)
  private $public;

  // The avatar image identifier of the user (read-write)
  private $avatarId;


  // Constructor
  public function __construct()
  {
    $this->id = null;
    $this->email = "";
    $this->passwordHash = "";
    $this->name = "";
    $this->description = "";
    $this->public = true;
    $this->avatarId = null;
    $this->createdAt = new \DateTime();
    $this->updatedAt = new \DateTime();
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

  // Get the name of the user
  public function getName(): string
  {
    return $this->name;
  }

  // Set the name of the user
  public function setName(string $name): self
  {
    $this->name = $name;
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

  // Get the avatar image identifier of the user
  public function getAvatarId(): ?string
  {
    return $this->avatarId;
  }

  // Set the avatar image identifier of the user
  public function setAvatarId(?string $avatarId): self
  {
    $this->avatarId = $avatarId;
    return $this;
  }

  // Normalize a user and return the normalized array
  public function normalize(NormalizerInterface $normalizer, string $format = null, array $context = []): array
  {
    return [
      // Identifier
      'id' => $this->getId(),

      // Read-write class fields
      'name' => $this->getName(),
      'description' => $this->getDescription(),
      'public' => $this->getPublic(),
      'avatar' => $this->getAvatarId() !== null ? $normalizer->normalize($context['imageRepository']->get($this->getAvatarId()), $format, $context) : null,

      // Entity fields
      'createdAt' => $normalizer->normalize($this->getCreatedAt(), $format, $context),
      'updatedAt' => $normalizer->normalize($this->getUpdatedAt(), $format, $context),
    ];
  }
}
