<?php
namespace Danae\Faylin\Model;


// Class that generates Twitter snowflake IDs
final class SnowflakeGenerator
{
  // Constant that defines the defualt epoch for generated snowflakes
  public const DEFAULT_EPOCH = 1288834974657;


  // The datacenter identifier for generated snowflakes
  private $datacenterId;

  // The worker identifier for generated snowflakes
  private $workerId;

  // The epoch for generated snowflakes
  private $epoch;


  // Constructor
  public function __construct(?int $datacenterId = null, ?int $workerId = null, ?int $epoch = null)
  {
    $this->datacenterId = 0;
    $this->workerId = 0;
    $this->epoch = self::DEFAULT_EPOCH;

    if ($datacenterId !== null)
      $this->setDatacenterId($datacenterId);
    if ($workerId !== null)
      $this->setWorkerId($workerId);
    if ($epoch !== null)
      $this->setEpoch($epoch);
  }

  // Get the datacenter identifier for generated snowflakes
  public function getDatacenterId(): int
  {
    return $this->datacenterId;
  }

  // Set the datacenter identifier for generated snowflakes
  public function setDatacenterId(int $datacenterId): self
  {
    if ($datacenterId < 0 || $datacenterId > Snowflake::DATACENTER_ID_MAX)
      throw \InvalidArgumentException("\$datacenterId mus be between 0 and " . Snowflake::DATACENTER_ID_MAX);

    $this->datacenterId = $datacenterId;
    return $this;
  }

  // Get the worker identifier for generated snowflakes
  public function getWorkerId(): int
  {
    return $this->workerId;
  }

  // Set the worker identifier for generated snowflakes
  public function setWorkerId(int $workerId): self
  {
    if ($workerId < 0 || $workerId > Snowflake::WORKER_ID_MAX)
      throw \InvalidArgumentException("\$workerId must be between 0 and " . Snowflake::WORKER_ID_MAX);

    $this->workerId = $workerId;
    return $this;
  }

  // Get the epoch for generated snowflakes
  public function getEpoch(): int
  {
    return $this->epoch;
  }

  // Get the epoch for generated snowflakes as a DateTime
  public function getEpochAsDateTime(): \DateTime
  {
    return Snowflake::convertMillisToDateTime($this->epoch);
  }

  // Set the epoch for generated snowflakes
  public function setEpoch(int $epoch): self
  {
    if ($epoch < 0 || $epoch > Snowflake::TIMESTAMP_MAX)
      throw \InvalidArgumentException("\$epoch must be between 0 and " . Snowflake::WORKER_ID_MAX . " or null");

    $this->epoch = $epoch;
    return $this;
  }

  // Set the epoch for generated snowflakes as a DateTime
  public function setEpochAsDateTime(\DateTime $epoch): self
  {
    return $this->setEpoch(Snowflake::convertDateTimeToMillis($epoch));
  }

  // Generate a snowflake
  public function generate(?int $timestamp = null, ?int $sequence = null): Snowflake
  {
    $timestamp ??= (int)(microtime(true) * 1000) - $this->epoch;
    $sequence ??= mt_rand(0, Snowflake::SEQUENCE_MAX);

    if ($timestamp < 0 || $timestamp > Snowflake::TIMESTAMP_MAX)
      throw new \InvalidArgumentException("\$timestamp must be between 0 and " . self::TIMESTAMP_MAX);
    if ($sequence < 0 || $sequence > Snowflake::SEQUENCE_MAX)
      throw new \InvalidArgumentException("\$sequence must be between 0 and " . self::SEQUENCE_MAX);

    return Snowflake::create($timestamp, $this->getDatacenterId(), $this->getWorkerId(), $sequence);
  }
}
