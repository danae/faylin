<?php
// This script imports images from a legacy fayl.in instance.
// First the script creates anonymous users for every share address found, second the script imports the images. Note that you need to copy the actual image contents manually to the new instance.

// Usage: php import_legacy.php [database_url]
// The database URL is specified as a Doctrine DBAL connection URL. See https://www.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html#connecting-using-a-url

require(__DIR__ . "/../vendor/autoload.php");

use Danae\Astral\Database;
use Danae\Astral\Repository;
use DI\ContainerBuilder;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

use Danae\Faylin\Model\Image;
use Danae\Faylin\Model\ImageRepository;
use Danae\Faylin\Model\User;
use Danae\Faylin\Model\UserRepository;
use Danae\Faylin\Utils\Snowflake;


// Class that defines a legacy image
class LegacyImage
{
  public $id;
  public $user_id;
  public $alias;
  public $location;
  public $file_name;
  public $file_mime_type;
  public $file_size;
  public $private;
  public $share_addr;
  public $share_date;
}


// Class that defines a legacy image repository
class LegacyImageRepository extends Repository
{
  public function __construct(database $database, string $table)
  {
    parent::__construct($database, $table, LegacyImage::class);

    $this->field('id', 'integer', ['autoincrement' => true]);
    $this->field('user_id', 'integer');
    $this->field('alias', 'string');
    $this->field('location', 'string');
    $this->field('file_name', 'string');
    $this->field('file_mime_type', 'string');
    $this->field('file_size', 'integer');
    $this->field('private', 'boolean');
    $this->field('share_addr', 'string');
    $this->field('share_date', 'datetime');

    $this->primary('id');
  }
}


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
  return  $containerBuilder->build();
}


// Function that creates a user
function createUser(Snowflake $snowflake, string $address)
{
  $address = base_convert(strval(ip2long($address)), 10, 36);

  return (new User)
    ->setId($snowflake->generateBase64String())
    ->setName('anonymous_' . $address)
    ->setEmail('anonymous_' . $address . "@fayl.in")
    ->hashPassword($address);
}


// Function that creates an image
function createImage(LegacyImage $file, user $user)
{
  return (new Image)
    ->setId($file->alias)
    ->setName($file->file_name)
    ->setContentType($file->file_mime_type)
    ->setContentLength($file->file_size)
    ->setCreatedAt($file->share_date)
    ->setUserId($user->getId());
}


// Main function
function main(array $args)
{
  printf("This script imports images from a legacy fayl.in instance.\n");
  printf("First the script creates anonymous users for every share address found, second the script imports the images. Note that you need to copy the actual image contents manually to the new instance.\n\n");
  printf("Usage: php %s [database_url]\n", $args[0]);
  printf("The database URL is specified as a Doctrine DBAL connection URL. See https://www.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html#connecting-using-a-url\n\n");

  // Validate the arguments
  if (count($args) < 2)
    return;

  $databaseUrl = $args[1];

  // Create the container
  $container = buildContainer();

  // Create the legacy database
  printf("Connecting to the database...\n", $databaseUrl);
  $legacyDatabaseConnection = DriverManager::getConnection(['url' => $databaseUrl]);
  $legacyDatabase = new Database($legacyDatabaseConnection);

  // Create the legacy repository
  printf("Creating the repository...\n");
  $legacyRepository = new LegacyImageRepository($legacyDatabase, 'files');

  // Fetch the images
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
  }
}

// Execute the main function
main($argv);
