<?php
namespace Danae\Faylin\Legacy;

use Danae\Astral\Database;
use Danae\Astral\Repository;

use Danae\Faylin\Model\User;


// Class that defines a database repository of users
final class UserRepository extends Repository
{
  // Constructor
  public function __construct(Database $database, string $table)
  {
    parent::__construct($database, $table, User::class);

    $this->field('id', 'string', ['length' => 64]);
    $this->field('createdAt', 'datetime');
    $this->field('updatedAt', 'datetime');
    $this->field('email', 'string', ['length' => 256]);
    $this->field('passwordHash', 'string', ['length' => 256]);
    $this->field('name', 'string', ['length' => 32]);
    $this->field('description', 'string', ['length' => 512]);
    $this->field('public', 'boolean', ['default' => true]);

    $this->primary('id');
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