<?php
namespace Danae\Faylin\Model;

use League\OAuth2\Server\Entities\ClientEntityInterface;

use Danae\Faylin\Model\Traits\EntityTrait;


// Class that defines a client object
final class Client implements ClientEntityInterface
{
  use EntityTrait;


  // The name of the client
  private $name;

  // The secret of the client
  private $secret;

  // The redirect URI of the client
  private $redirectUri;

  // The comfidential state of the client
  private $confidential;


  // Constructor
  public function __construct()
  {
    $this->id = null;
    $this->name = "";
    $this->secret = "";
    $this->redirectUri = "";
    $this->confidential = false;
  }

  // Get the name of the client
  public function getName(): string
  {
    return $this->name;
  }

  // Set the name of the client
  public function setName(string $name): self
  {
    $this->name = $name;
    return $this;
  }

  // Get the secret of the client
  public function getSecret(): string
  {
    return $this->secret;
  }

  // Set the secret of the client
  public function setSecret(string $secret): self
  {
    $this->secret = $secret;
    return $this;
  }

  // Get the redirect URI of the client
  public function getRedirectUri(): string
  {
    return $this->redirectUri;
  }

  // Set the redirect URI of the client
  public function setRedirectUri(string $redirectUri)
  {
    $this->redirectUri = $redirectUri;
    return $this;
  }

  // Get the confidential state of the client
  public function isConfidential(): bool
  {
    return $this->confidential;
  }

  // Set the confidential state of the client
  public function setConfidential(bool $confidential): self
  {
    $this->confidential = $confidential;
    return $this;
  }
}
