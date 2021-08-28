<?php
namespace Danae\Faylin\Model;


// Class that defines a collection image object
final class CollectionImage
{
  // The identifier of the collection
  private $collectionId;

  // The identifier of the image
  private $imageId;

  // The order of the collection image
  private $order;


  // Constructor
  public function __construct()
  {
    $this->collectionId = null;
    $this->imageId = null;
    $this->order = null;
  }

  // Get the collection identifier of the collection image
  public function getCollectionId(): string
  {
    return $this->collectionId;
  }

  // Set the collection identifier of the collection
  public function setCollectionId(string $collectionId): self
  {
    $this->collectionId = $collectionId;
    return $this;
  }

  // Get the image identifier of the collection image
  public function getImageId(): string
  {
    return $this->collectionId;
  }

  // Set the image identifier of the collection image
  public function setImageId(string $imageId): self
  {
    $this->imageId = $imageId;
    return $this;
  }

  // Get the order of the collection image
  public function getOrder(): ?int
  {
    return $this->order;
  }

  // Set the order of the collection image
  public function setOrder(string $order): self
  {
    $this->order = $order;
    return $this;
  }
}
