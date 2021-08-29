<?php
namespace Danae\Faylin\Model;


// Interface that defines a user repository
interface UserRepositoryInterface
{
  // Count users in the repository
  public function count(array $filter = [], array $options = []): int;

  // Find a user in the repository by its identifier
  public function find(string $id, array $options = []): ?User;

  // Find a single user in the repository
  public function findBy(array $filter = [], array $options = []): ?User;

  // Find multiple users in the repository
  public function findManyBy(array $filter = [], array $options = []): array;

  // Insert a user in the repository and return the inserted count
  public function insert(User $user): int;

  // Update a user in the repository and return the updated count
  public function update(User $user): int;

  // Delete a user in the repository and return the deleted count
  public function delete(User $user): int;

  // Validate a user for an email address and password
  public function validate(string $email, string $password): ?User;
}
