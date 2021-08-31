<?php
// This script adds checksums to images without one.

// Usage: php add_checksums.php

require(__DIR__ . "/../vendor/autoload.php");

use Danae\Astral\Database;
use Danae\Astral\Repository;
use DI\ContainerBuilder;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use League\Flysystem\Filesystem;
use MongoDB\Client;
use Psr\Http\Message\StreamFactoryInterface;

use Danae\Faylin\Legacy\ImageRepository as LegacyImageRepository;
use Danae\Faylin\Legacy\UserRepository as LegacyUserRepository;
use Danae\Faylin\Model\ImageRepositoryInterface;
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
  printf("This script adds checksums to images without one.\n\n");
  printf("Usage: php %s\n", $args[0]);

  // Create the container
  $container = buildContainer();

  // Create the repository
  $imageRepository = $container->get(ImageRepositoryInterface::class);

  // Iterate over the images
  $images = $imageRepository->findManyBy();
  foreach ($images as $image)
  {
    // Check if there is a checksum or content length
    if (empty($image->getChecksum()) || $image->getContentLength() === 0)
    {
      $stream = $imageRepository->readFile($image);

      if (empty($image->getChecksum()))
      {
        // Calculate the checksum
        printf("Calculating checksum for image '%s' with name '%s'...\n", $image->getId(), $image->getName());
        $image->setChecksum(hash('sha256', $stream->getContents()));
      }

      if ($image->getContentLength() === 0)
      {
        // Calculate the checksum
        printf("Calculating conent length for image '%s' with name '%s'...\n", $image->getId(), $image->getName());
        $image->setContentLength($stream->getSize());
      }

      // Save the image
      printf("Saving image '%s' with name '%s'...\n", $image->getId(), $image->getName());
      $imageRepository->update($image);
    }
  }
}

// Execute the main function
main($argv);
