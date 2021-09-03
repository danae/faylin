<?php
namespace Danae\Faylin\Implementation\MongoDB;

use MongoDB\Client;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Model\BSONDocument;

use Danae\Faylin\Model\Image;
use Danae\Faylin\Model\ImageRepositoryInterface;
use Danae\Faylin\Model\Snowflake;
use Danae\Faylin\Model\User;
use Danae\Faylin\Model\UserRepositoryInterface;


// Class that defines a repository of images
final class ImageRepository implements ImageRepositoryInterface
{
  use Traits\RepositoryTrait;


  // The user repository to use with the repository
  private $userRepository;


  // Constructor
  public function __construct(Client $client, string $databaseName, string $collectionName, UserRepositoryInterface $userRepository)
  {
    $this->client = $client;
    $this->database = $this->client->selectDatabase($databaseName);
    $this->collection = $this->database->selectCollection($collectionName);

    $this->userRepository = $userRepository;
  }


  // Count images in the repository
  public function count(array $filter = [], array $options = []): int
  {
    return $this->getCollection()->countDocuments($filter, $options);
  }

  // Find an image in the repository by its identifier
  public function find(Snowflake $id, array $options = []): ?Image
  {
    $result = $this->getCollection()->findOne(['_id' => $id->toBase64()], $options);

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


  // Normalize an Image to its MongoDB representation
  private function normalize(Image $image): BSONDocument
  {
    return new BSONDocument([
      '_id' => $image->getId()->toBase64(),
      'name' => $image->getName(),
      'createdAt' => new UTCDateTime($image->getCreatedAt()),
      'updatedAt' => new UTCDateTime($image->getUpdatedAt()),
      'user' => $image->getUser()->getId()->toBase64(),
      'title' => $image->getTitle(),
      'description' => $image->getDescription(),
      'public' => $image->getPublic(),
      'nsfw' => $image->getNsfw(),
      'contentType' => $image->getContentType(),
      'contentLength' => $image->getContentLength(),
      'checksum' => $image->getChecksum(),
    ]);
  }

  // Denormalize an Image from its MongoDB representation
  private function denormalize(BSONDocument $document): Image
  {
    return (new Image())
      ->setId(Snowflake::fromBase64($document['_id']))
      ->setName($document['name'])
      ->setCreatedAt($document['createdAt']->toDateTime())
      ->setUpdatedAt($document['updatedAt']->toDateTime())
      ->setUser($this->userRepository->find(Snowflake::fromBase64($document['user'])))
      ->setTitle($document['title'] ?? "")
      ->setDescription($document['description'])
      ->setPublic($document['public'])
      ->setNsfw($document['nsfw'])
      ->setContentType($document['contentType'])
      ->setContentLength($document['contentLength'])
      ->setChecksum($document['checksum']);
  }

  // Get the file name for an image
  private function getFileName(Image $image): string
  {
    return sprintf("%s.gz", $image->getId());
  }
}
