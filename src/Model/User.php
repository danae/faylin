<?php
namespace Danae\Faylin\Model;

use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

use Danae\Faylin\Utils\Traits\RouteContextTrait;

// Class that defines a user object
final class User implements NormalizableInterface
{
  use Traits\EntityTrait;
  use Traits\NamedEntityTrait;
  use Traits\DatedEntityTrait;
  use RouteContextTrait;


  // The email address of the user (internal)
  private $email;

  // The password hash of the user (internal)
  private $passwordHash;

  // The title of the user (read-write)
  private $title;

  // The description of the user (read-write)
  private $description;

  // The public state of the user (read-write)
  private $public;

  // The avatar of the user (read-write)
  private $avatar;


  // Constructor
  public function __construct()
  {
    $this->id = null;
    $this->name = "";
    $this->createdAt = new \DateTime();
    $this->updatedAt = new \DateTime();
    $this->email = "";
    $this->passwordHash = "";
    $this->title = "";
    $this->description = "";
    $this->public = true;
    $this->avatar = null;
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

  // Get the title of the user
  public function getTitle(): string
  {
    return $this->title;
  }

  // Set the title of the user
  public function setTitle(string $title): self
  {
    $this->title = $title;
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

  // Get the avatar of the user
  public function getAvatar(): ?string
  {
    return $this->avatar;
  }

  // Set the avatar of the user
  public function setAvatar(?string $avatar): self
  {
    $this->avatar = $avatar;
    return $this;
  }


  // Normalize a user and return the normalized array
  public function normalize(NormalizerInterface $normalizer, string $format = null, array $context = []): array
  {
    $avatarImage = $this->getAvatar() !== null ? $context['imageRepository']->find(Snowflake::fromString($this->getAvatar())) : null;

    return [
      // Entity fields
      'id' => $this->getId()->toString(),
      'name' => $this->getName(),
      'createdAt' => $normalizer->normalize($this->getCreatedAt(), $format, $context),
      'updatedAt' => $normalizer->normalize($this->getUpdatedAt(), $format, $context),

      // Internal class fields
      'email' => $this->getEmail(),

      // Read-write class fields
      'title' => $this->getTitle(),
      'description' => $this->getDescription(),
      'public' => $this->getPublic(),
      'avatar' => $this->getAvatar(),

      // Additional fields
      'avatarUrl' => $avatarImage !== null ? $this->fullUrlFor($context['request'], 'images.download', ['imageName' => $avatarImage->getName(), 'format' => 'png'], ['transform' => 'crop:128,128']) : null,
    ];
  }
}
