<?php
namespace Danae\Faylin\Model;

use Danae\Astral\Database;
use Danae\Astral\Repository;


// Class that defines a database repository of users
final class UserRepository extends Repository
{
  // Constructor
  public function __construct(Database $database, string $table)
  {
    parent::__construct($database, $table, User::class);

    $this->field('id', 'string', ['length' => 256]);
    $this->field('email', 'string', ['length' => 256]);
    $this->field('passwordHash', 'string', ['length' => 256]);
    $this->field('name', 'string', ['length' => 32]);
    $this->field('description', 'string', ['length' => 256]);
    $this->field('public', 'boolean', ['default' => true]);
    $this->field('avatarId', 'string', ['length' => 256, 'notnull' => false, 'default' => null]);
    $this->field('createdAt', 'datetime');
    $this->field('updatedAt', 'datetime');

    $this->primary('id');
  }

  // Return a user for an identifier
  public function get(string $id): ?User
  {
    return $this->selectOne(['id' => $id]);
  }

  // Validate a user for an email address and password
  public function validate(string $email, string $password): ?User
  {
    $user = $this->selectOne(['email' => $email]);
    if ($user == null)
      return null;
    if (!$user->verifyPassword($password))
      return null;
    return $user;
  }
}
