<?php
namespace Danae\Faylin\Implementation\MongoDB;

use MongoDB\Client;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Model\BSONDocument;

use Danae\Faylin\Model\User;
use Danae\Faylin\Model\UserRepositoryInterface;


// Class that defines a database repository of users
final class UserRepository implements UserRepositoryInterface
{
  use Traits\RepositoryTrait;


  // Constructor
  public function __construct(Client $client, string $databaseName, string $collectionName)
  {
    $this->client = $client;
    $this->database = $this->client->selectDatabase($databaseName);
    $this->collection = $this->database->selectCollection($collectionName);
  }

  // Count users in the repository
  public function count(array $filter = [], array $options = []): int
  {
    return $this->getCollection()->countDocuments($filter, $options);
  }

  // Get a user in the repository by its identifier
  public function find(string $id, array $options = []): ?User
  {
    $result = $this->getCollection()->findOne(['_id' => $id], $options);

    return $result !== null ? $this->denormalize($result) : null;
  }

  // Find a single user in the repository
  public function findBy(array $filter = [], array $options = []): ?User
  {
    $result = $this->getCollection()->findOne($filter, $options);

    return $result !== null ? $this->denormalize($result) : null;
  }

  // Find multiple users in the repository
  public function findManyBy(array $filter = [], array $options = []): array
  {
    $results = $this->getCollection()->find($filter, $options);

    $documents = [];
    foreach ($results as $result)
      $documents[] = $this->denormalize($result);
    return $documents;
  }

  // Insert a user in the repository and return the inserted count
  public function insert(User $user): int
  {
    $document = $this->normalize($user);

    $result = $this->getCollection()->insertOne($document);
    return $result->getInsertedCount();
  }

  // Update a user in the repository and return the updated count
  public function update(User $user): int
  {
    $document = $this->normalize($user);

    $result = $this->getCollection()->updateOne(['_id' => $user->getId()], ['$set' => $document]);
    return $result->getModifiedCount();
  }

  // Delete a user in the repository and return the deleted count
  public function delete(User $user): int
  {
    $result = $this->getCollection()->deleteOne(['_id' => $user->getId()]);
    return $result->getDeletedCount();
  }

  // Validate a user for an email address and password
  public function validate(string $email, string $password): ?User
  {
    // Find the user
    $user = $this->findBy(['email' => $email]);

    // Verify the email address of the user
    if ($user == null)
      return null;

    // Verify the password of the user
    if (!$user->verifyPassword($password))
      return null;

    // Return the verified user
    return $user;
  }


  // Normalize a user to its MongoDB representation
  private function normalize(User $user): BSONDocument
  {
    return new BSONDocument([
      '_id' => $user->getId(),
      'name' => $user->getName(),
      'createdAt' => new UTCDateTime($user->getCreatedAt()),
      'updatedAt' => new UTCDateTime($user->getUpdatedAt()),
      'email' => $user->getEmail(),
      'passwordHash' => $user->getPasswordHash(),
      'description' => $user->getDescription(),
      'public' => $user->getPublic(),
    ]);
  }

  // Denormalize a user from its MongoDB representation
  private function denormalize(BSONDocument $document): User
  {
    return (new User())
      ->setId($document['_id'])
      ->setName($document['name'])
      ->setCreatedAt($document['createdAt']->toDateTime())
      ->setUpdatedAt($document['updatedAt']->toDateTime())
      ->setEmail($document['email'])
      ->setPasswordHash($document['passwordHash'])
      ->setDescription($document['description'])
      ->setPublic($document['public']);
  }
}
