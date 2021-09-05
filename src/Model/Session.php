<?php
namespace Danae\Faylin\Model;

use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use WhichBrowser\Parser;

use Danae\Faylin\Model\Authorization\Token;


// Class that defines a session object
final class Session implements NormalizableInterface
{
  use Traits\EntityTrait;
  use Traits\DatedEntityTrait;
  use Traits\UserOwnedEntityTrait;


  // The user agent of the session
  private $userAgent;

  // The user address of the session
  private $userAddress;

  // The access token of the session
  private $accessToken;


  // Constructor
  public function __construct()
  {
    $this->id = null;
    $this->createdAt = new \DateTime();
    $this->updatedAt = new \DateTime();
    $this->user = null;
    $this->userAgent = "";
    $this->userAddress = "";
    $this->accessToken = "";
  }

  // Get the user agent of the session
  public function getUserAgent(): string
  {
    return $this->userAgent;
  }

  // Set the user agent of the session
  public function setUserAgent(string $userAgent): self
  {
    $this->userAgent = $userAgent;
    return $this;
  }

  // Get the user address of the session
  public function getUserAddress(): string
  {
    return $this->userAddress;
  }

  // Set the user address of the session
  public function setUserAddress(string $userAddress): self
  {
    $this->userAddress = $userAddress;
    return $this;
  }

  // Get the access token of the session
  public function getAccessToken(): string
  {
    return $this->accessToken;
  }

  // Set the access token of the session
  public function setAccessToken(string $accessToken): self
  {
    $this->accessToken = $accessToken;
    return $this;
  }


  // Normalize a user and return the normalized array
  public function normalize(NormalizerInterface $normalizer, string $format = null, array $context = []): array
  {
    $browser = new Parser($this->getUserAgent());

    return [
      // Entity fields
      'id' => $this->getId()->toString(),
      'createdAt' => $normalizer->normalize($this->getCreatedAt(), $format, $context),
      'updatedAt' => $normalizer->normalize($this->getUpdatedAt(), $format, $context),
      'user' => $normalizer->normalize($this->getUser(), $format, $context),

      // Read-only class fields
      'userAgent' => $this->getUserAgent(),
      'userAddress' => $this->getUserAddress(),

      // Additional fields
      'current' => $this->getAccessToken() == $this->context['request']->getAttribute('authToken'),
      'browser' => $browser->browser->toString(),
      'engine' => $browser->engine->toString(),
      'os' => $browser->os->toString(),
      'device' => $browser->device->toString(),
    ];
  }
}
