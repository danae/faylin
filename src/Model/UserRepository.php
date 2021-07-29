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
    $this->field('name', 'string', ['length' => 32]);
    $this->field('email', 'string', ['length' => 256]);
    $this->field('passwordHash', 'string', ['length' => 256]);
    $this->field('createdAt', 'datetime');
    $this->field('updatedAt', 'datetime');

    $this->primary('id');
  }
}
