<?php
namespace Danae\Faylin\Model;

use Danae\Astral\Database;
use Danae\Astral\Repository;

use Danae\Faylin\Utils\ArrayUtils;


// Class that defines a database repository of collections
final class CollectionRepository extends Repository
{
  // The collection image repository to use with the collection repository
  private $collectionImageRepository;

  // The image repository to use with the collection repository
  private $imageRepository;


  // Constructor
  public function __construct(Database $database, string $table, CollectionImageRepository $collectionImageRepository, ImageRepository $imageRepository)
  {
    parent::__construct($database, $table, Collection::class);
    $this->collectionImageRepository = $collectionImageRepository;
    $this->imageRepository = $imageRepository;

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
  public function getImages(string $id, array $options = []): array
  {
    $images = $this->imageRepository->select();

    $collectionImages = $this->collectionImageRepository->select(['collectionId' => $id], $options);
    $collectionImages = array_map(fn($collectionImage) => ArrayUtils::find($images, fn($image) => $image->getId() === $collectionImage->getImageId()), $collectionImages);
    return $collectionImages;
  }

  // Put an image in a collection
  public function putImage(string $id, string $imageId): void
  {
    $image = (new CollectionImage)
      ->setCollectionId($id)
      ->setImageId($imageId);

    $this->collectionImageRepository->insert($image);
  }

  // Delete an image in a collection
  public function deleteImage(string $id, string $imageId): void
  {
    $image = (new CollectionImage)
      ->setCollectionId($id)
      ->setImageId($imageId);

    $this->collectionImageRepository->delete($image);
  }
}
