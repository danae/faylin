<?php
// This script prints information about Twitter snowflake IDs.

// Usage: php snowflake.php [snowflake string]

require(__DIR__ . "/../vendor/autoload.php");

use Danae\Faylin\Model\Snowflake;
use Danae\Faylin\Model\SnowflakeGenerator;


// Main function
function main(array $args)
{
  printf("This script prints information about Twitter snowflake IDs.\n\n");
  printf("Usage: php %s [snowflake string]\n\n", $args[0]);

  if (count($args) !== 2)
    return;

  $snowflake = Snowflake::fromString($args[1]);

  printf("Snowflake: %d\n", $snowflake->getId());
  printf("  Timestamp: %s\n", $snowflake->getTimestamp());
  printf("  Timestamp as DateTime: %s\n", $snowflake->getTimestampAsDateTime(SnowflakeGenerator::DEFAULT_EPOCH)->format(DateTime::RFC3339));
  printf("  Datacenter ID: %d\n", $snowflake->getDatacenterId());
  printf("  Worker ID: %d\n", $snowflake->getWorkerId());
  printf("  Sequence: %d\n\n", $snowflake->getSequence());

  printf("Snowflake as Base36 string: %s\n", $snowflake->toBase36());
  printf("Snowflake as Base64 string: %s\n", $snowflake->toBase64());
}

// Execute the main function
main($argv);
