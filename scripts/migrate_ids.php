<?php
// This script adds better IDs to models.

// Usage: php migrate_ids.php

require(__DIR__ . "/../vendor/autoload.php");

use DI\ContainerBuilder;
use League\Flysystem\Filesystem;

use Danae\Faylin\Model\CollectionRepositoryInterface;
use Danae\Faylin\Model\ImageRepositoryInterface;
use Danae\Faylin\Model\ImageStoreInterface;
use Danae\Faylin\Model\Snowflake;
use Danae\Faylin\Model\SnowflakeGenerator;
use Danae\Faylin\Model\UserRepositoryInterface;


// Function that builds the container
function buildContainer()
{
  // Create the container builder
  $containerBuilder = new ContainerBuilder();

  $settings = require(__DIR__ . "/../app/settings.php");
  $settings($containerBuilder);

  $dependencies = require(__DIR__ . "/../app/dependencies.php");
  $dependencies($containerBuilder);

  // Create the container
  return $containerBuilder->build();
}


// Main function
function main(array $args)
{
  printf("This script adds better IDs to models.\n\n");
  printf("Usage: php %s\n", $args[0]);

  // Create the container
  $container = buildContainer();

  // Get the dependencies
  $collectionDb = $container->get(CollectionRepositoryInterface::class)->getCollection();
  $imageDb = $container->get(ImageRepositoryInterface::class)->getCollection();
  $userDb = $container->get(UserRepositoryInterface::class)->getCollection();
  $filesystem = $container->get(Filesystem::class);
  $imageFileNameFormat = $container->get('store.imageFileNameFormat');
  $generator = $container->get(SnowflakeGenerator::class);

  // Iterate over the users
  printf("Fetching users...\n");
  $users = $userDb->find();
  foreach ($users as $user)
  {
    // Check if the title of the user is set
    if (empty($user['title']))
    {
      // Migrate the user
      printf("Migrating user '%s' with name '%s...\n'", $user['_id'], $user['name']);

      $previousId = $user['_id'];
      $snowflake = Snowflake::fromBase64($previousId);

      $user['title'] = $user['name'];
      $user['_id'] = $snowflake->toString();

      $userDb->insertOne($user);
      $userDb->deleteOne(['_id' => $previousId]);
    }
  }

  // Iterate over the images
  printf("Fetching images...\n");
  $images = $imageDb->find();
  $imageDate = new DateTime("2021-08-01T00:00:00Z");
  foreach ($images as $image)
  {
    // Check if the title of the image is set
    if (empty($image['title']))
    {
      // Migrate the image
      printf("Migrating image '%s' with name '%s...\n'", $image['_id'], $image['name']);

      $previousId = $image['_id'];
      if ($image['createdAt']->toDateTime() >= $imageDate)
        $snowflake = Snowflake::fromBase64($previousId);
      else
        $snowflake = $generator->generate(Snowflake::convertDateTimeToMillis($image['createdAt']->toDateTime()));

      $userSnowflake = Snowflake::fromBase64($image['user']);
      $image['user'] = $userSnowflake->toString();
      $image['title'] = $image['name'];
      $image['name'] = $image['_id'];
      $image['_id'] = $snowflake->toString();

      $imageDb->insertOne($image);
      $imageDb->deleteOne(['_id' => $previousId]);
    }

    // Check if the contents of the image exists
    if (!$filesystem->fileExists(sprintf($imageFileNameFormat, $image['_id'])))
    {
      printf("Migrating contents of image '%s' with name '%s...\n'", $image['_id'], $image['name']);
      $contents = $filesystem->read(sprintf($imageFileNameFormat, $previousId));
      $filesystem->write(sprintf($imageFileNameFormat, $image['_id']), $contents);
    }
  }

  // Iterate over the collections
  printf("Fetching collections...\n");
  $collections = $collectionDb->find();
  foreach ($collections as $collection)
  {
    // Check if the title of the collection is set
    if (empty($collection['title']))
    {
      // Migrate the collection
      printf("Migrating collection '%s' with name '%s...\n'", $collection['_id'], $collection['name']);

      $previousId = $collection['_id'];
      $snowflake = Snowflake::fromBase64($previousId);

      $userSnowflake = Snowflake::fromBase64($collection['user']);
      $collection['user'] = $userSnowflake->toString();
      $collection['images'] = array_map(function($imageId) {
        $imageSnowflake = Snowflake::fromBase64($imageId);
        return $imageSnowflake->toString();
      }, $collection['images']->getArrayCopy());
      $collection['title'] = $collection['name'];
      $collection['name'] = $collection['_id'];
      $collection['_id'] = $snowflake->toString();

      $collectionDb->insertOne($collection);
      $collectionDb->deleteOne(['_id' => $previousId]);
    }
  }
}

// Execute the main function
main($argv);
