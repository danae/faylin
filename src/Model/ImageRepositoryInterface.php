<?php
namespace Danae\Faylin\Model;

use Psr\Http\Message\StreamInterface;


// Interface that defines an image repository
interface ImageRepositoryInterface
{
  // Count images in the repository
  public function count(array $filter = [], array $options = []): int;

  // Find an image in the repository by its identifier
  public function find(string $id, array $options = []): ?Image;

  // Find a single image in the repository
  public function findBy(array $filter = [], array $options = []): ?Image;

  // Find multiple images in the repository
  public function findManyBy(array $filter = [], array $options = []): array;

  // Insert an image in the repository and return the inserted count
  public function insert(Image $image): int;

  // Update an image in the repository and return the updated count
  public function update(Image $image): int;

  // Delete an image in the repository and return the deleted count
  public function delete(Image $image): int;

  // Return a stream containing the contents of an image
  public function readFile(Image $image): StreamInterface;

  // Write the contents of an image from a stream
  public function writeFile(Image $image, StreamInterface $stream): void;

  // Delete the contents of the image
  public function deleteFile(Image $image): void;
}
