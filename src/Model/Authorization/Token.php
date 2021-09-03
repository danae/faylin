<?php
namespace Danae\Faylin\Model\Authorization;

use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Normalizer\DenormalizableInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

use Danae\Faylin\Model\Snowflake;
use Danae\Faylin\Model\Traits\EntityTrait;
use Danae\Faylin\Model\Traits\DatedEntityTrait;
use Danae\Faylin\Model\Traits\UserOwnedEntityTrait;


// Class that defines an authorization token object
final class Token implements NormalizableInterface, DenormalizableInterface
{
  use EntityTrait;
  use DatedEntityTrait;
  use UserOwnedEntityTrait;


  // The date when the token will become expired
  private $expiresAt;


  // Constructor
  public function __construct()
  {
    $this->id = null;
    $this->createdAt = new \DateTime();
    $this->updatedAt = new \DateTime();
    $this->user = null;
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

    $data['jti'] = $this->getId()->toString();
    $data['sub'] = $this->getUser()->getId()->toString();

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

    $this->setId(Snowflake::fromString($data['jti']));
    $this->setUser($context['userRepository']->find(Snowflake::fromString($data['sub'])));

    if (isset($data['iat']))
      $this->setCreatedAt((new \DateTime())->setTimestamp($data['iat']));
    if (isset($data['exp']))
      $this->setExpiresAt((new \DateTime())->setTimestamp($data['exp']));
  }
}
