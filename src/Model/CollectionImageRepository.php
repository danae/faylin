<?php
namespace Danae\Faylin\Model;

use Danae\Astral\Database;
use Danae\Astral\Repository;


// Class that defines a database repository of collection images
final class CollectionImageRepository extends Repository
{
  // Constructor
  public function __construct(Database $database, string $table)
  {
    parent::__construct($database, $table, CollectionImage::class);

    $this->field('collectionId', 'string', ['length' => 64]);
    $this->field('imageId', 'string', ['length' => 64]);
    $this->field('order', 'integer', ['notnull' => false]);

    $this->primary('collectionId');
    $this->primary('imageId');
  }
}
