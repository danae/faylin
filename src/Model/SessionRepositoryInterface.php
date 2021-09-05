<?php
namespace Danae\Faylin\Model;


// Interface that defines a session repository
interface SessionRepositoryInterface
{
  // Count sessions in the repository
  public function count(array $filter = [], array $options = []): int;

  // Find a session in the repository by its identifier
  public function find(Snowflake $id, array $options = []): ?Session;

  // Find a single session in the repository
  public function findBy(array $filter = [], array $options = []): ?Session;

  // Find multiple session in the repository
  public function findManyBy(array $filter = [], array $options = []): array;

  // Insert a session in the repository and return the inserted count
  public function insert(Session $session): int;

  // Update a session in the repository and return the updated count
  public function update(Session $session): int;

  // Delete a session in the repository and return the deleted count
  public function delete(Session $session): int;
}
