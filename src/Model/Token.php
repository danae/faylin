<?php
namespace Danae\Faylin\Model;

use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Normalizer\DenormalizableInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

use Danae\Faylin\Model\Traits\EntityTrait;
use Danae\Faylin\Model\Traits\UserOwnedEntityTrait;


// Class that defines an authorization token object
final class Token implements NormalizableInterface, DenormalizableInterface
{
  use EntityTrait;
  use UserOwnedEntityTrait;


  // The issuer of the token
  private $issuer;

  // The audience of the token
  private $audience;

  // The date when the token was issued
  private $issuedAt;

  // The date when the token will become valid
  private $validAt;

  // The date when the token will become expired
  private $expiresAt;


  // Constructor
  public function __construct()
  {
    $this->id = null;
    $this->userId = null;
    $this->issuer = null;
    $this->audience = null;
    $this->issuedAt = new \DateTime();
    $this->validAt = null;
    $this->expiresAt = null;
  }

  // Get the issuer of the token
  public function getIssuer(): ?string
  {
    return $this->issuer;
  }

  // Set the issuer of the token
  public function setIssuer(?string $issuer): self
  {
    $this->issuer = $issuer;
    return $this;
  }

  // Get the audience of the token
  public function getAudience(): ?array
  {
    return $this->audience;
  }

  // Set the audience of the token
  public function setAudience(?array $audience): self
  {
    $this->audience = $audience;
    return $this;
  }

  // Get the date when the token was issued
  public function getIssuedAt(): ?\DateTime
  {
    return $this->issuedAt;
  }

  // Set the date when the token was issued
  public function setIssuedAt(?\DateTime $issuedAt): self
  {
    $this->issuedAt = $issuedAt;
    return $this;
  }

  // Get the date when the token will become valid
  public function getValidAt(): ?\DateTime
  {
    return $this->validAt;
  }

  // Set the date when the token will become valid
  public function setValidAt(?\DateTime $validAt): self
  {
    $this->validAt = $validAt;
    return $this;
  }

  // Get the date when the token will become expired
  public function getExpiresAt(): ?\DateTime
  {
    return $this->expiresAt;
  }

  // Set the date when the token will become expired
  public function setExpiresAt(?\DateTime $expiresAt): self
  {
    $this->expiresAt = $expiresAt;
    return $this;
  }


  // Normalize a token and return the normalized array
  public function normalize(NormalizerInterface $normalizer, string $format = null, array $context = []): array
  {
    $data = [];

    if ($this->getId() !== null)
      $data['jti'] = $this->getId();
    if ($this->getUserId() !== null)
      $data['sub'] = $this->getUserId();
    if ($this->getIssuer() != null)
      $data['iss'] = $this->getIssuer();
    if ($this->getAudience() !== null)
      $data['aud'] = $this->getAudience();
    if ($this->getIssuedAt() !== null)
      $data['iat'] = $this->getIssuedAt()->getTimestamp();
    if ($this->getValidAt() !== null)
      $data['nbf'] = $this->getValidAt()->getTimestamp();
    if ($this->getExpiresAt() !== null)
      $data['exp'] = $this->getExpiresAt()->getTimestamp();

    return $data;
  }

  // Denormalize an array into this token
  public function denormalize(DenormalizerInterface $denormalizer, $data, string $format = null, array $context = [])
  {
    if (!is_array($data))
      throw new NotNormalizableValueException("Data must be an array");

    if (isset($data['jti']))
      $this->setId($data['jti']);
    if (isset($data['sub']))
      $this->setUserId($data['sub']);
    if (isset($data['iss']))
      $this->setIssuer($data['iss']);
    if (isset($data['aud']))
      $this->setAudience($data['aud']);
    if (isset($data['iat']))
      $this->setIssuedAt((new \DateTime())->setTimestamp($data['iat']));
    if (isset($data['nbf']))
      $this->setValidAt((new \DateTime())->setTimestamp($data['nbf']));
    if (isset($data['exp']))
      $this->setExpiresAt((new \DateTime())->setTimestamp($data['exp']));
  }
}
