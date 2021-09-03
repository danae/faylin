<?php
namespace Danae\Faylin\Model;


// Interface that defines a collection repository
interface CollectionRepositoryInterface
{
  // Count collections in the repository
  public function count(array $filter = [], array $options = []): int;

  // Find a collection in the repository by its identifier
  public function find(Snowflake $id, array $options = []): ?Collection;

  // Find a single collection in the repository
  public function findBy(array $filter = [], array $options = []): ?Collection;

  // Find multiple collections in the repository
  public function findManyBy(array $filter = [], array $options = []): array;

  // Insert a collection in the repository and return the inserted count
  public function insert(Collection $collection): int;

  // Update a collection in the repository and return the updated count
  public function update(Collection $collection): int;

  // Delete a collection in the repository and return the deleted count
  public function delete(Collection $collection): int;
}
