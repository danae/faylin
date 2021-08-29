<?php
namespace Danae\Faylin\Implementation\MongoDB;

use MongoDB\Client;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Model\BSONDocument;
use League\Flysystem\Filesystem;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

use Danae\Faylin\Model\Image;
use Danae\Faylin\Model\ImageRepositoryInterface;
use Danae\Faylin\Model\User;
use Danae\Faylin\Model\UserRepositoryInterface;


// Class that defines a repository of images
final class ImageRepository implements ImageRepositoryInterface
{
  use Traits\RepositoryTrait;


  // The filesystem to use with the repository
  private $filesystem;

  // The stream factory to use with the repository
  private $streamFactory;


  // Constructor
  public function __construct(Client $client, string $databaseName, string $collectionName, UserRepositoryInterface $userRepository, Filesystem $filesystem, StreamFactoryInterface $streamFactory)
  {
    $this->client = $client;
    $this->database = $this->client->selectDatabase($databaseName);
    $this->collection = $this->database->selectCollection($collectionName);

    $this->userRepository = $userRepository;
    $this->filesystem = $filesystem;
    $this->streamFactory = $streamFactory;
  }

  // Return the filesystem
  public function getFilesystem(): Filesystem
  {
    return $this->filesystem;
  }

  // Return the stream factory
  public function getStreamFactory(): StreamFactoryInterface
  {
    return $this->streamFactory;
  }


  // Count images in the repository
  public function count(array $filter = [], array $options = []): int
  {
    return $this->getCollection()->countDocuments($filter, $options);
  }

  // Find an image in the repository by its identifier
  public function find(string $id, array $options = []): ?Image
  {
    $result = $this->getCollection()->findOne(['_id' => $id], $options);

    return $result !== null ? $this->denormalize($result) : null;
  }

  // Find a single image in the repository
  public function findBy(array $filter = [], array $options = []): ?Image
  {
    $result = $this->getCollection()->findOne($filter, $options);

    return $result !== null ? $this->denormalize($result) : null;
  }

  // Find multiple images in the repository
  public function findManyBy(array $filter = [], array $options = []): array
  {
    $results = $this->getCollection()->find($filter, $options);

    $documents = [];
    foreach ($results as $result)
      $documents[] = $this->denormalize($result);
    return $documents;
  }

  // Insert an image in the repository and return the inserted count
  public function insert(Image $image): int
  {
    $document = $this->normalize($image);

    $result = $this->getCollection()->insertOne($document);
    return $result->getInsertedCount();
  }

  // Update an image in the repository and return the updated count
  public function update(Image $image): int
  {
    $document = $this->normalize($image);

    $result = $this->getCollection()->updateOne(['_id' => $image->getId()], ['$set' => $document]);
    return $result->getModifiedCount();
  }

  // Delete an image in the repository and return the deleted count
  public function delete(Image $image): int
  {
    $result = $this->getCollection()->deleteOne(['_id' => $image->getId()]);
    return $result->getDeletedCount();
  }

  // Return a stream containing the contents of an image
  public function readFile(Image $image): StreamInterface
  {
    $contents = $this->getFilesystem()->read($this->getFileName($image));
    $contents = gzdecode($contents);
    return $this->getStreamFactory()->createStream($contents);
  }

  // Write the contents of an image from a stream
  public function writeFile(Image $image, StreamInterface $stream): void
  {
    $contents = $stream->getContents();
    $contents = gzencode($contents);
    $this->getFilesystem()->write($this->getFileName($image), $contents);
  }

  // Delete the contents of the image
  public function deleteFile(Image $image): void
  {
    $this->getFilesystem()->delete($this->getFileName($image));
  }


  // Normalize an Image to its MongoDB representation
  private function normalize(Image $image): BSONDocument
  {
    return new BSONDocument([
      '_id' => $image->getId(),
      'createdAt' => new UTCDateTime($image->getCreatedAt()),
      'updatedAt' => new UTCDateTime($image->getUpdatedAt()),
      'user' => $image->getUser()->getId(),
      'name' => $image->getName(),
      'description' => $image->getDescription(),
      'public' => $image->getPublic(),
      'nsfw' => $image->getNsfw(),
      'contentType' => $image->getContentType(),
      'contentLength' => $image->getContentLength(),
    ]);
  }

  // Denormalize an Image from its MongoDB representation
  private function denormalize(BSONDocument $document): Image
  {
    return (new Image())
      ->setId($document['_id'])
      ->setCreatedAt($document['createdAt']->toDateTime())
      ->setUpdatedAt($document['updatedAt']->toDateTime())
      ->setUser($this->userRepository->find($document['user']))
      ->setName($document['name'])
      ->setDescription($document['description'])
      ->setPublic($document['public'])
      ->setNsfw($document['nsfw'])
      ->setContentType($document['contentType'])
      ->setContentLength($document['contentLength']);
  }

  // Get the file name for an image
  private function getFileName(Image $image): string
  {
    return sprintf("%s.gz", $image->getId());
  }
}
