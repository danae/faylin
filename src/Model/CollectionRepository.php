<?php
namespace Danae\Faylin\Model;

use Danae\Astral\Database;
use Danae\Astral\Repository;


// Class that defines a database repository of collections
final class CollectionRepository extends Repository
{
  // The image repository to use with the collection repository
  private $imageRepository;

  // The collection image repository to use with the collection repository
  private $collectionImageRepository;


  // Constructor
  public function __construct(Database $database, string $table, ImageRepository $imageRepository, CollectionImageRepository $collectionImageRepository)
  {
    parent::__construct($database, $table, Collection::class);
    $this->imageRepository = $imageRepository;
    $this->collectionImageRepository = $collectionImageRepository;

    $this->field('id', 'string', ['length' => 64]);
    $this->field('name', 'string', ['length' => 64]);
    $this->field('description', 'string', ['length' => 512]);
    $this->field('public', 'boolean', ['default' => true]);
    $this->field('userId', 'string', ['length' => 64]);
    $this->field('createdAt', 'datetime');
    $this->field('updatedAt', 'datetime');

    $this->primary('id');
  }

  // Return a collection for an identifier
  public function get(string $id): ?Collection
  {
    return $this->selectOne(['id' => $id]);
  }

  // Return the images in a collection for an identifier
  public function getImages(string $id): array
  {
    $images = $this->collectionImageRepository->select(['collectionId' => $id], ['order_by' => ['order']]);
    return array_map(fn($image) => $this->imageRepository->get($image->getImageId()), $images);
  }
}
