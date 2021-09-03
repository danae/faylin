<?php
namespace Danae\Faylin\App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpForbiddenException;
use Symfony\Component\Serializer\Serializer;

use Danae\Faylin\Model\Collection;
use Danae\Faylin\Model\Image;
use Danae\Faylin\Model\SnowflakeGenerator;
use Danae\Faylin\Model\User;
use Danae\Faylin\Utils\ArrayUtils;
use Danae\Faylin\Validator\Validator;


// Controller that defines routes for collections
final class CollectionController extends AbstractController
{
  // Return all collections as a JSON response
  public function getCollections(Request $request, Response $response)
  {
    // Get the collections
    $options = $this->createSelectOptions($request, ['sort' => '-createdAt']);
    $collections = $this->collectionRepository->findManyBy(['public' => true], $options);

    // Return the response
    return $this->serialize($request, $response, $collections);
  }

  // Post a new collection and return the collection as a JSON response
  public function postCollection(Request $request, Response $response, User $authUser, SnowflakeGenerator $snowflakeGenerator)
  {
    $now = new \DateTime();

    // Get and validate the body parameters
    $params = (new Validator())
      ->withRequired('name', 'string|notempty|maxlength:256')
      ->withOptional('description', 'string|maxlength:256', '')
      ->withOptional('public', 'bool', true)
      ->validate($request->getParsedBody())
      ->resultOrThrowBadRequest($request);

    // Create the collection
    $collection = new Collection();
    $collection->generateId($snowflakeGenerator);
    $collection->setUser($authUser);
    $collection->setName($params['name']);
    $collection->setDescription($params['description']);
    $collection->setPublic($params['public']);

    // Create the collection in the repository
    $this->collectionRepository->insert($collection);

    // Return the response
    return $this->serialize($request, $response, $collection)
      ->withStatus(201);
  }

  // Get a collection as a JSON response
  public function getCollection(Request $request, Response $response, Collection $collection)
  {
    // Return the response
    return $this->serialize($request, $response, $collection);
  }

  // Patch a collection and return the collection as a JSON response
  public function patchCollection(Request $request, Response $response, Collection $collection, User $authUser)
  {
    // Check if the authorized user owns this collection
    if ($authUser->getId() != $collection->getUser()->getId())
      throw new HttpForbiddenException($request, "The current authorized user is not allowed to modify the collection with id \"{$collection->getId()}\"");

    // Get and validate the body parameters
    $params = (new Validator())
      ->withOptional('name', 'string|notempty|maxlength:256')
      ->withOptional('description', 'string|maxlength:256')
      ->withOptional('public', 'bool')
      ->validate($request->getParsedBody())
      ->resultOrThrowBadRequest($request);

    // Modify the collection
    if ($params['name'] !== null)
      $collection->setName($params['name']);
    if ($params['description'] !== null)
      $collection->setDescription($params['description']);
    if ($params['public'] !== null)
      $collection->setPublic($params['public']);

    // Update the collection in the repository
    $this->collectionRepository->update($collection->setUpdatedAt(new \DateTime()));

    // Return the response
    return $this->serialize($request, $response, $collection);
  }

  // Delete a collection
  public function deleteCollection(Request $request, Response $response, Collection $collection, User $authUser)
  {
    // Check if the authorized user owns this collection
    if ($authUser->getId() != $collection->getUser()->getId())
      throw new HttpForbiddenException($request, "The current authorized user is not allowed to delete the collection with id \"{$collection->getId()}\"");

    // Delete the collection from the repository
    $this->collectionRepository->delete($collection);

    // Return the response
    return $response
      ->withStatus(204);
  }

  // Get all images in a collection as a JSON response
  public function getCollectionImages(Request $request, Response $response, Collection $collection)
  {
    // Return the response
    return $this->serialize($request, $response, $collection->getImages());
  }

  // Put an image in a collection
  public function putCollectionImage(Request $request, Response $response, Collection $collection, Image $image, User $authUser)
  {
    $now = new \DateTime();

    // Check if the authorized user owns this collection
    if ($authUser->getId() != $collection->getUser()->getId())
      throw new HttpForbiddenException($request, "The current authorized user is not allowed to modify the collection with id \"{$collection->getId()}\"");

    // Check if the image is already in the collection
    if (ArrayUtils::any($collection->getImages(), fn($i) => $i->getId() == $image->getId()))
      throw new HttpBadRequestException($request, "The collection with id \"{$collection->getId()}\" already contains the image with id \"{$image->getId()}\"");

    // Add the image to the collection
    $collection->addImage($image);
    $collection->setUpdatedAt($now);

    // Update the collection in the repository
    $this->collectionRepository->update($collection);

    // Return the response
    return $response
      ->withStatus(204);
  }

  // Delete an image in a collection
  public function deleteCollectionImage(Request $request, Response $response, Collection $collection, Image $image, User $authUser)
  {
    $now = new \DateTime();

    // Check if the authorized user owns this collection
    if ($authUser->getId() != $collection->getUser()->getId())
      throw new HttpForbiddenException($request, "The current authorized user is not allowed to modify the collection with id \"{$collection->getId()}\"");

    // Check if the image is not in the collection
    if (!ArrayUtils::any($collection->getImages(), fn($i) => $i->getId() == $image->getId()))
      throw new HttpBadRequestException($request, "The collection with id \"{$collection->getId()}\" does not contain the image with id \"{$image->getId()}\"");

    // Remove the image from the collection
    $collection->removeImage($image);
    $collection->setUpdatedAt($now);

    // Update the collection in the repository
    $this->collectionRepository->update($collection);

    // Return the response
    return $response
      ->withStatus(204);
  }
}
