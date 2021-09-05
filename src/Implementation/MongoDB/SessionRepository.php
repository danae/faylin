<?php
namespace Danae\Faylin\Implementation\MongoDB;

use MongoDB\Client;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Model\BSONArray;
use MongoDB\Model\BSONDocument;

use Danae\Faylin\Model\Session;
use Danae\Faylin\Model\SessionRepositoryInterface;
use Danae\Faylin\Model\Snowflake;
use Danae\Faylin\Model\User;
use Danae\Faylin\Model\UserRepositoryInterface;


// Class that defines a repository of sessions
final class SessionRepository implements SessionRepositoryInterface
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


  // Count sessions in the repository
  public function count(array $filter = [], array $options = []): int
  {
    return $this->getCollection()->countDocuments($filter, $options);
  }

  // Find a session in the repository by its identifier
  public function find(Snowflake $id, array $options = []): ?Session
  {
    $result = $this->getCollection()->findOne(['_id' => $id->toString()], $options);

    return $result !== null ? $this->denormalize($result) : null;
  }

  // Find a single session in the repository
  public function findBy(array $filter = [], array $options = []): ?Session
  {
    $result = $this->getCollection()->findOne($filter, $options);

    return $result !== null ? $this->denormalize($result) : null;
  }

  // Find multiple sessions in the repository
  public function findManyBy(array $filter = [], array $options = []): array
  {
    $results = $this->getCollection()->find($filter, $options);

    $documents = [];
    foreach ($results as $result)
      $documents[] = $this->denormalize($result);
    return $documents;
  }

  // Insert a session in the repository and return the inserted count
  public function insert(Session $session): int
  {
    $document = $this->normalize($session);

    $result = $this->getCollection()->insertOne($document);
    return $result->getInsertedCount();
  }

  // Update a session in the repository and return the updated count
  public function update(Session $session): int
  {
    $document = $this->normalize($session);

    $result = $this->getCollection()->updateOne(['_id' => $session->getId()->toString()], ['$set' => $document]);
    return $result->getModifiedCount();
  }

  // Delete a session in the repository and return the deleted count
  public function delete(Session $session): int
  {
    $result = $this->getCollection()->deleteOne(['_id' => $session->getId()->toString()]);
    return $result->getDeletedCount();
  }


  // Normalize a collection to its MongoDB representation
  private function normalize(Session $session): BSONDocument
  {
    return new BSONDocument([
      '_id' => $session->getId()->toString(),
      'createdAt' => new UTCDateTime($session->getCreatedAt()),
      'updatedAt' => new UTCDateTime($session->getUpdatedAt()),
      'user' => $session->getUser()->getId()->toString(),
      'userAgent' => $session->getUserAgent(),
      'userAddress' => $session->getUserAddress(),
      'accessToken' => $session->getAccessToken(),
    ]);
  }

  // Denormalize a collection from its MongoDB representation
  private function denormalize(BSONDocument $document): Session
  {
    return (new Session())
      ->setId(Snowflake::fromString($document['_id']))
      ->setCreatedAt($document['createdAt']->toDateTime())
      ->setUpdatedAt($document['updatedAt']->toDateTime())
      ->setUser($this->userRepository->find(Snowflake::fromString($document['user'])))
      ->setUserAgent($document['userAgent'])
      ->setUserAddress($document['userAddress'])
      ->setAccessToken($document['accessToken']);
  }
}
