<?php
// This script migrates the fayl.in database from Doctrine ORM to MongoDB.

// Usage: php migrate_to_mongodb.php [mongodb_database_url] [database_url]
// The database URL is specified as a Doctrine DBAL connection URL. See https://www.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html#connecting-using-a-url

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
  printf("This script migrates the fayl.in database from Doctrine ORM to MongoDB.\n\n");
  printf("Usage: php %s [database_url]\n", $args[0]);
  printf("The database URL is specified as a Doctrine DBAL connection URL. See https://www.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html#connecting-using-a-url\n\n");

  // Validate the arguments
  if (count($args) < 2)
    return;

  $databaseUrl = $args[1];

  // Create the container
  $container = buildContainer();

  // Drop the MongoDB database
  printf("Dropping the MongoDB database...\n");
  $container->get(Client::class)->dropDatabase($container->get('mongodb.database'));

  // Create the legacy database
  printf("Connecting to the database...\n", $databaseUrl);
  $legacyDatabaseConnection = DriverManager::getConnection(['url' => $databaseUrl]);
  $legacyDatabase = new Database($legacyDatabaseConnection);

  // Create the legacy repositories
  printf("Creating the repositories...\n");
  $userRepository = $container->get(UserRepositoryInterface::class);
  $imageRepository = $container->get(ImageRepositoryInterface::class);

  $legacyUserRepository = new LegacyUserRepository($legacyDatabase, 'users');
  $legacyImageRepository = new LegacyImageRepository($legacyDatabase, 'images', $userRepository, $container->get(Filesystem::class), $container->get(StreamFactoryInterface::class));

  // Migrate the users
  printf("Fetching the users...\n");
  foreach ($legacyUserRepository->select() as $user)
  {
    // Insert the user in the new repository
    printf("Migrating user '%s' with name '%s'...\n", $user->getId(), $user->getName());
    $userRepository->insert($user);
  }

  // Migrate the images
  printf("Fetching the images...\n");
  foreach ($legacyImageRepository->select() as $image)
  {
    // Insert the user in the new repository
    printf("Migrating image '%s' with name '%s'...\n", $image->getId(), $image->getName());
    $imageRepository->insert($image);
  }

  /*// Fetch the images
  printf("Fetching the files...\n");
  $files = $legacyRepository->select();

  // Iterate over the images
  $new_images = [];
  $new_users = [];
  foreach ($files as $file)
  {
    // Check if the file type is a supported type
    if (!array_key_exists($file->file_mime_type, $container->get('uploads.supportedContentTypes')))
    {
      printf("Skipping file '%s' with name '%s', because its type is not a supported type (%s)\n", $file->alias, $file->file_name, $file->file_mime_type);
      continue;
    }

    // Create the user to assign to the image
    $address = $file->share_addr;
    if (array_key_exists($address, $new_users))
      $user = $new_users[$address];
    else
      $user = $new_users[$address] = createUser($container->get(Snowflake::class), $address);

    // Create the image
    $image = createImage($file, $user);
    $new_images[$image->getId()] = $image;
  }

  // Create the users
  $userRepository = $container->get(UserRepository::class);
  printf("\n%d users to create\n", count($new_users));
  foreach ($new_users as $user)
  {
    printf("Creating user '%s' with name '%s'...\n", $user->getId(), $user->getName(), $user->getEmail());
    $userRepository->insert($user);
  }

  // Create the images
  $imageRepository = $container->get(ImageRepository::class);
  printf("\n%d images to create\n", count($new_images));
  foreach ($new_images as $i => $image)
  {
    printf("Creating image '%s' with name '%s'\n", $image->getId(), $image->getName());
    $imageRepository->insert($image);
  }*/
}

// Execute the main function
main($argv);
