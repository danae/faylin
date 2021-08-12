<?php
namespace Danae\Faylin\Model;

use Danae\Astral\Database;
use Danae\Astral\Repository;
use League\Flysystem\Filesystem;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;


// Class that defines a database repository of images
final class ImageRepository extends Repository
{
  // The filesystem to use with the image repository
  private $filesystem;

  // The stream factory to use with the image repository
  private $streamFactory;


  // Constructor
  public function __construct(Database $database, string $table, Filesystem $filesystem, StreamFactoryInterface $streamFactory)
  {
    parent::__construct($database, $table, Image::class);
    $this->filesystem = $filesystem;
    $this->streamFactory = $streamFactory;

    $this->field('id', 'string', ['length' => 256]);
    $this->field('name', 'string', ['length' => 256]);
    $this->field('description', 'string', ['length' => 256]);
    $this->field('tags', 'simple_array');
    $this->field('public', 'boolean', ['default' => true]);
    $this->field('nsfw', 'boolean', ['default' => false]);
    $this->field('contentType', 'string', ['length' => 256]);
    $this->field('contentLength', 'integer');
    $this->field('userId', 'string', ['length' => 256]);
    $this->field('createdAt', 'datetime');
    $this->field('updatedAt', 'datetime');

    $this->primary('id');
  }

  // Return an image for an identifier
  public function get(string $id): ?Image
  {
    return $this->selectOne(['id' => $id]);
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
