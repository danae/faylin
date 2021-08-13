<?php
namespace Danae\Faylin\Model;

use Danae\Astral\Database;
use Danae\Astral\Repository;


// Class that defines a database repository of authorization tokens
final class TokenRepository extends Repository
{
  // Constructor
  public function __construct(Database $database, string $table)
  {
    parent::__construct($database, $table, Token::class);

    $this->field('id', 'string', ['length' => 64]);
    $this->field('userId', 'string', ['length' => 64]);
    $this->field('userAgent', 'string', ['length' => 256]);
    $this->field('userIpAddress', 'string', ['length' => 256]);
    $this->field('audience', 'simple_array', ['notnull' => false, 'default' => null]);
    $this->field('createdAt', 'datetime');
    $this->field('expiresAt', 'datetime', ['notnull' => false, 'default' => null]);

    $this->primary('id');
  }

  // Return a token for an identifier
  public function get(string $id): ?Token
  {
    return $this->selectOne(['id' => $id]);
  }
}
