<?php
namespace Danae\Faylin\Implementation\MongoDB\Traits;

use MongoDB\Client;
use MongoDB\Collection;
use MongoDB\Database;


// Trait the defines repository functionality
trait RepositoryTrait
{
  // The MongoDB client to use with the repository
  private $client;

  // The MongoDB database to use with the repository
  private $database;

  // The MongoDB collection to use with the repository
  private $collection;


  // Return the MongoDB client
  public function getClient(): Client
  {
    return $this->client;
  }

  // Return the MongoDB database
  public function getDatabase(): Database
  {
    return $this->database;
  }

  // Return the MongoDB collection
  public function getCollection(): Collection
  {
    return $this->collection;
  }
}
