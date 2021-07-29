<?php
namespace Danae\Faylin\Utils;


// Class that generates and handles Twitter snowflake IDs
final class Snowflake
{
  // Constant that defines the defualt epoch for generated snowflakes
  private const DEFAULT_EPOCH = 1288834974657;

  // Constants for the lengths of fields
  private const TIMESTAMP_LENGTH = 41;
  private const TIMESTAMP_MAX = (1 << self::TIMESTAMP_LENGTH) - 1;
  private const DATACENTER_ID_LENGTH = 5;
  private const DATACENTER_ID_MAX = (1 << self::DATACENTER_ID_LENGTH) - 1;
  private const WORKER_ID_LENGTH = 5;
  private const WORKER_ID_MAX = (1 << self::WORKER_ID_LENGTH) - 1;
  private const SEQUENCE_LENGTH = 12;
  private const SEQUENCE_MAX = (1 << self::SEQUENCE_LENGTH) - 1;

  # Constants for the bit shifts of fields
  private const SEQUENCE_SHIFT = 0;
  private const WORKER_ID_SHIFT = self::SEQUENCE_SHIFT + self::SEQUENCE_LENGTH;
  private const DATACENTER_ID_SHIFT = self::WORKER_ID_SHIFT + self::WORKER_ID_LENGTH;
  private const TIMESTAMP_SHIFT = self::DATACENTER_ID_SHIFT + self::DATACENTER_ID_LENGTH;


  // The datacenter identifier for generated snowflakes
  private $datacenterId;

  // The worker identifier for generated snowflakes
  private $workedId;

  // The epoch for generated snowflakes
  private $epoch;


  // Constructor
  public function __construct(int $datacenterId, int $workerId, ?int $epoch = null)
  {
    if ($datacenterId < 0 || $datacenterId > self::DATACENTER_ID_MAX)
      throw \InvalidArgumentException("\$datacenterId is outside the allowed range");
    if ($workerId < 0 || $workerId > self::WORKER_ID_MAX)
      throw \InvalidArgumentException("\$workerId is outside the allowed range");

    $this->datacenterId = $datacenterId;
    $this->workerId = $workerId;
    $this->epoch = $epoch ?? self::DEFAULT_EPOCH;
  }

  // Get the datacenter identifier for generated snowflakes
  public function getDatacenterId(): int
  {
    return $this->datacenterId;
  }

  // Get the worker identifier for generated snowflakes
  public function getWorkerId(): int
  {
    return $this->workedId;
  }

  // Get the epoch for generated snowflakes
  public function getEpoch(): int
  {
    return $this->epoch;
  }

  // Generate a snowflake
  public function generate(): int
  {
    // Generate the timestamp
    $now = (int)(microtime(true) * 1000);
    $timestamp = $now - $this->epoch;

    // Generate the sequence
    $sequence = mt_rand(0, self::SEQUENCE_MAX);

    // Create the snowflake
    return ($timestamp << self::TIMESTAMP_SHIFT)
      | ($this->datacenterId << self::DATACENTER_ID_SHIFT)
      | ($this->workerId << self::WORKER_ID_SHIFT)
      | ($sequence << self::SEQUENCE_SHIFT);
  }

  // Generate a snowflake as string
  public function generateString(): string
  {
    return strval($this->generate());
  }

  // Generate a snowflake as base36 encoded string
  public function generateBase36String(): string
  {
    return base_convert($this->generateString(), 10, 36);
  }

  // Generate a snowflake as base64url encoded string
  public function generateBase64String(): string
  {
    return self::urlEncodeInteger($this->generate());
  }


  // Encode a string using the base64url encoding
  public static function urlEncode(string $data): string
  {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
  }

  // Encode an integer using the base64url encoding
  public static function urlEncodeInteger(int $data): string
  {
    return self::urlEncode(pack('J*', $data));
  }

  // Decode a string using the base64url encoding
  public static function urlDecode(string $data): string
  {
    return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
  }

  // Decode an integer  using the base64url encoding
  public static function urlDecodeInteger(string $data): int
  {
    $array = unpack('J*', self::urlDecode($data));
    return array_shift($array);
  }
}
