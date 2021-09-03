<?php
namespace Danae\Faylin\Implementation\MongoDB;

use MongoDB\Client;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Model\BSONArray;
use MongoDB\Model\BSONDocument;

use Danae\Faylin\Model\Collection;
use Danae\Faylin\Model\CollectionRepositoryInterface;
use Danae\Faylin\Model\Image;
use Danae\Faylin\Model\ImageRepositoryInterface;
use Danae\Faylin\Model\Snowflake;
use Danae\Faylin\Model\User;
use Danae\Faylin\Model\UserRepositoryInterface;


// Class that defines a repository of collections
final class CollectionRepository implements CollectionRepositoryInterface
{
  use Traits\RepositoryTrait;


  // The user repository to use with the repository
  private $userRepository;

  // The image repository to use with the repository
  private $imageRepository;


  // Constructor
  public function __construct(Client $client, string $databaseName, string $collectionName, UserRepositoryInterface $userRepository, ImageRepositoryInterface $imageRepository)
  {
    $this->client = $client;
    $this->database = $this->client->selectDatabase($databaseName);
    $this->collection = $this->database->selectCollection($collectionName);

    $this->userRepository = $userRepository;
    $this->imageRepository = $imageRepository;
  }


  // Count collections in the repository
  public function count(array $filter = [], array $options = []): int
  {
    return $this->getCollection()->countDocuments($filter, $options);
  }

  // Find a collection in the repository by its identifier
  public function find(Snowflake $id, array $options = []): ?Collection
  {
    $result = $this->getCollection()->findOne(['_id' => $id->toBase64()], $options);

    return $result !== null ? $this->denormalize($result) : null;
  }

  // Find a single collection in the repository
  public function findBy(array $filter = [], array $options = []): ?Collection
  {
    $result = $this->getCollection()->findOne($filter, $options);

    return $result !== null ? $this->denormalize($result) : null;
  }

  // Find multiple collections in the repository
  public function findManyBy(array $filter = [], array $options = []): array
  {
    $results = $this->getCollection()->find($filter, $options);

    $documents = [];
    foreach ($results as $result)
      $documents[] = $this->denormalize($result);
    return $documents;
  }

  // Insert a collection in the repository and return the inserted count
  public function insert(Collection $collection): int
  {
    $document = $this->normalize($collection);

    $result = $this->getCollection()->insertOne($document);
    return $result->getInsertedCount();
  }

  // Update a collection in the repository and return the updated count
  public function update(Collection $collection): int
  {
    $document = $this->normalize($collection);

    $result = $this->getCollection()->updateOne(['_id' => $collection->getId()], ['$set' => $document]);
    return $result->getModifiedCount();
  }

  // Delete a collection in the repository and return the deleted count
  public function delete(Collection $collection): int
  {
    $result = $this->getCollection()->deleteOne(['_id' => $collection->getId()]);
    return $result->getDeletedCount();
  }


  // Normalize a collection to its MongoDB representation
  private function normalize(Collection $collection): BSONDocument
  {
    return new BSONDocument([
      '_id' => $collection->getId()->toBase64(),
      'name' => $collection->getName(),
      'createdAt' => new UTCDateTime($collection->getCreatedAt()),
      'updatedAt' => new UTCDateTime($collection->getUpdatedAt()),
      'user' => $collection->getUser()->getId()->toBase64(),
      'images' => new BSONArray(array_map(fn($image) => $image->getId(), $collection->getImages())),
      'title' => $collection->getTitle(),
      'description' => $collection->getDescription(),
      'public' => $collection->getPublic(),
    ]);
  }

  // Denormalize a collection from its MongoDB representation
  private function denormalize(BSONDocument $document): Collection
  {
    return (new Collection())
      ->setId(Snowflake::fromBase64($document['_id']))
      ->setName($document['name'])
      ->setCreatedAt($document['createdAt']->toDateTime())
      ->setUpdatedAt($document['updatedAt']->toDateTime())
      ->setUser($this->userRepository->find(Snowflake::fromBase64($document['user'])))
      ->setImages(array_map(fn($imageId) => $this->imageRepository->find($imageId), $document['images']->getArrayCopy()))
      ->setTitle($document['title'] ?? "")
      ->setDescription($document['description'])
      ->setPublic($document['public']);
  }
}
