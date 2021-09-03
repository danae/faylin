<?php
namespace Danae\Faylin\Model;


// Class that defines a Twitter snowflake identifier
final class Snowflake
{
  // Constants for the lengths of fields
  public const TIMESTAMP_LENGTH = 41;
  public const TIMESTAMP_MAX = (1 << self::TIMESTAMP_LENGTH) - 1;
  public const DATACENTER_ID_LENGTH = 5;
  public const DATACENTER_ID_MAX = (1 << self::DATACENTER_ID_LENGTH) - 1;
  public const WORKER_ID_LENGTH = 5;
  public const WORKER_ID_MAX = (1 << self::WORKER_ID_LENGTH) - 1;
  public const SEQUENCE_LENGTH = 12;
  public const SEQUENCE_MAX = (1 << self::SEQUENCE_LENGTH) - 1;

  # Constants for the bit shifts of fields
  public const SEQUENCE_SHIFT = 0;
  public const WORKER_ID_SHIFT = self::SEQUENCE_SHIFT + self::SEQUENCE_LENGTH;
  public const DATACENTER_ID_SHIFT = self::WORKER_ID_SHIFT + self::WORKER_ID_LENGTH;
  public const TIMESTAMP_SHIFT = self::DATACENTER_ID_SHIFT + self::DATACENTER_ID_LENGTH;


  // Constructor
  public function __construct(int $id)
  {
    $this->id = $id;
  }

  // Get the identifier of the snowflake
  public function getId(): int
  {
    return $this->id;
  }

  // Get the timestamp of the snowflake
  public function getTimestamp(): int
  {
    return ($this->id >> self::TIMESTAMP_SHIFT) & self::TIMESTAMP_MAX;
  }

  // Get the timestamp of the snowflake as a DateTime
  public function getTimestampAsDateTime(): \DateTime
  {
    return self::convertMillisToDateTime($this->getTimestamp());
  }

  // Get the datacenter identifier of the snowflake
  public function getDatacenterId(): int
  {
    return ($this->id >> self::DATACENTER_ID_MAX) & self::DATACENTER_ID_MAX;
  }

  // Get the worker identifier of the snowflake
  public function getWorkerId(): int
  {
    return ($this->id >> self::WORKER_ID_SHIFT) & self::WORKER_ID_MAX;
  }

  // Get the sequence of the snowflake
  public function getSequence(): int
  {
    return ($this->id >> self::SEQUENCE_SHIFT) & self::SEQUENCE_MAX;
  }

  // Convert the snowflake to a string
  public function toString(): string
  {
    return strval($this->id);
  }

  // Convert the snowflake to a base36 encoded string
  public function toBase36(): string
  {
    return base_convert($this->id, 10, 36);
  }

  // Convert the snowflake to a base64 encoded string
  public function toBase64(): string
  {
    $snowflake = pack('J*', $this->id);
    $snowflake = rtrim(strtr(base64_encode($snowflake), '+/', '-_'), '=');
    return $snowflake;
  }

  // Return the string representation of the snowflake
  public function __toString(): string
  {
    return $this->asString();
  }


  // Create a snowflake
  public static function create(int $timestamp, int $datacenterId, int $workerId, int $sequence): Snowflake
  {
    if ($timestamp < 0 || $timestamp > self::TIMESTAMP_MAX)
      throw new \InvalidArgumentException("\$timestamp must be between 0 and " . self::TIMESTAMP_MAX);
    if ($datacenterId < 0 || $datacenterId > self::DATACENTER_ID_MAX)
      throw new \InvalidArgumentException("\$datacenterId must be between 0 and " . self::DATACENTER_ID_MAX);
    if ($workerId < 0 || $workerId > self::WORKER_ID_MAX)
      throw new \InvalidArgumentException("\$workerId must be between 0 and " . self::WORKER_ID_MAX);
    if ($sequence < 0 || $sequence > self::SEQUENCE_MAX)
      throw new \InvalidArgumentException("\$sequence must be between 0 and " . self::SEQUENCE_MAX);

    $id = ($timestamp << self::TIMESTAMP_SHIFT)
      | ($datacenterId << self::DATACENTER_ID_SHIFT)
      | ($workerId << self::WORKER_ID_SHIFT)
      | ($sequence << self::SEQUENCE_SHIFT);

    return new Snowflake($id);
  }

  // Decode a snowflake from a string
  public static function fromString(string $snowflake): Snowflake
  {
    return new Snowflake(intval($snowflake));
  }

  // Decode a snowflake from a Base36-encoded string
  public static function fromBase36(string $snowflake): Snowflake
  {
    return new Snowflake(base_convert($snowflake, 36, 10));
  }

  // Decode a snowflake from a Base64URL-encoded string
  public static function fromBase64(string $snowflake): Snowflake
  {
    $snowflake = base64_decode(str_pad(strtr($snowflake, '-_', '+/'), strlen($snowflake) % 4, '=', STR_PAD_RIGHT));
    $snowflake = array_shift(unpack('J*', self::urlDecode($id)));
    return new Snowflake($snowflake);
  }

  // Convert a DateTime to milliseconds since the Unix epoch
  public static function convertDateTimeToMillis(\DateTime $dateTime): int
  {
    return $dateTime->getTimestamp() * 1000;
  }

  // Convert milliseconds since the Unix epoch to a DateTime
  public static function convertMillisToDateTime(int $millis): int
  {
    $dateTime = new \DateTime();
    $dateTime->setTimestamp((int)floor($millis / 1000));
    return $dateTime;
  }
}
