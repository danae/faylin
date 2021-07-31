<?php
namespace Danae\Faylin\Model;

use Danae\Astral\Database;
use Danae\Astral\Repository;


// Class that defines a database repository of clients
final class ClientRepository extends Repository
{
  // Constructor
  public function __construct(Database $database, string $table)
  {
    parent::__construct($database, $table, Client::class);

    $this->field('id', 'string', ['length' => 256]);
    $this->field('name', 'string', ['length' => 256]);
    $this->field('secret', 'string', ['length' => 256]);
    $this->field('redirectUri', 'string', ['length' => 256]);
    $this->field('confidential', 'boolean');

    $this->primary('id');
  }

  // Return a client for an identifier
  public function get(string $id): ?Client
  {
    return $this->selectOne(['id' => $id]);
  }
}
