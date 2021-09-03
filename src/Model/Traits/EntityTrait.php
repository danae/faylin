<?php
namespace Danae\Faylin\Model\Traits;

use Danae\Faylin\Model\Snowflake;
use Danae\Faylin\Model\SnowflakeGenerator;


// Trait that defines an entity
trait EntityTrait
{
  // The identifier of the entity
  private $id;


  // Get the identifier of the entity
  public function getId(): ?Snowflake
  {
    return $this->id;
  }

  // Set the identifier of the entity
  public function setId(Snowflake $id): self
  {
    $this->id = $id;
    return $this;
  }

  // Generate the identifier of the entity
  public function generateId(SnowflakeGenerator $generator, ?int $timestamp = null, ?int $sequence = null): self
  {
    $this->id = $generator->generate($timestamp, $sequence);
    return $this;
  }
}
