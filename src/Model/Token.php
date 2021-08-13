<?php
namespace Danae\Faylin\Model;

use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Normalizer\DenormalizableInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

use Danae\Faylin\Model\Traits\CreatedAtEntityTrait;
use Danae\Faylin\Model\Traits\EntityTrait;
use Danae\Faylin\Model\Traits\UserOwnedEntityTrait;


// Class that defines an authorization token object
final class Token implements NormalizableInterface, DenormalizableInterface
{
  use EntityTrait;
  use UserOwnedEntityTrait;
  use CreatedAtEntityTrait;


  // The date when the token will become expired
  private $expiresAt;


  // Constructor
  public function __construct()
  {
    $this->id = null;
    $this->userId = null;
    $this->createdAt = new \DateTime();
    $this->expiresAt = null;
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

    $data['jti'] = $this->getId();
    $data['sub'] = $this->getUserId();

    if ($this->getCreatedAt() !== null)
      $data['iat'] = $this->getCreatedAt()->getTimestamp();
    if ($this->getExpiresAt() !== null)
      $data['exp'] = $this->getExpiresAt()->getTimestamp();

    return $data;
  }

  // Denormalize an array into this token
  public function denormalize(DenormalizerInterface $denormalizer, $data, string $format = null, array $context = [])
  {
    if (!is_array($data))
      throw new NotNormalizableValueException("Data must be an array");

    $this->setId($data['jti']);
    $this->setUserId($data['sub']);

    if (isset($data['iat']))
      $this->setCreatedAt((new \DateTime())->setTimestamp($data['iat']));
    if (isset($data['exp']))
      $this->setExpiresAt((new \DateTime())->setTimestamp($data['exp']));
  }
}
