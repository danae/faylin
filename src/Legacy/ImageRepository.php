<?php
namespace Danae\Faylin\Legacy;

use Danae\Astral\Database;
use Danae\Astral\Repository;
use League\Flysystem\Filesystem;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

use Danae\Faylin\Model\Image;
use Danae\Faylin\Model\User;
use Danae\Faylin\Model\UserRepositoryInterface;


// Class that defines a database repository of images
final class ImageRepository extends Repository
{
  // The user repository to use with the repository
  private $userRepository;

  // The filesystem to use with the repository
  private $filesystem;

  // The stream factory to use with the repository
  private $streamFactory;


  // Constructor
  public function __construct(Database $database, string $table, UserRepositoryInterface $userRepository, Filesystem $filesystem, StreamFactoryInterface $streamFactory)
  {
    parent::__construct($database, $table, Image::class);
    $this->userRepository = $userRepository;
    $this->filesystem = $filesystem;
    $this->streamFactory = $streamFactory;

    $this->field('id', 'string', ['length' => 64]);
    $this->field('createdAt', 'datetime');
    $this->field('updatedAt', 'datetime');
    $this->field('userId', 'string', ['length' => 64, 'accessor' => 'user', 'normalize_mapper' => fn($user) => $user->getId(), 'denormalize_mapper' => fn($userId) => $this->userRepository->find($userId)]);
    $this->field('name', 'string', ['length' => 64]);
    $this->field('description', 'string', ['length' => 512]);
    $this->field('public', 'boolean', ['default' => true]);
    $this->field('nsfw', 'boolean', ['default' => false]);
    $this->field('contentType', 'string', ['length' => 256]);
    $this->field('contentLength', 'integer');

    $this->primary('id');
  }


  // Return a stream containing the contents of the image
  public function readFile(Image $image): StreamInterface
  {
    $contents = $this->filesystem->read($this->getFileName($image));
    $contents = gzdecode($contents);
    return $this->streamFactory->createStream($contents);
  }

  // Write the contents of an image from a stream
  public function writeFile(Image $image, StreamInterface $stream): void
  {
    $contents = $stream->getContents();
    $contents = gzencode($contents);
    $this->filesystem->write($this->getFileName($image), $contents);
  }

  // Delete the contents of the image
  public function deleteFile(Image $image): void
  {
    $this->filesystem->delete($this->getFileName($image));
  }


  // Get the file name for an image
  private function getFileName(Image $image): string
  {
    return sprintf("%s.gz", $image->getId());
  }
}
