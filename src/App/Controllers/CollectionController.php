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
    // Get the authorized user
    $authorizedUser = $request->getAttribute('authUser');

    // Get the repository filter
    if ($authorizedUser !== null)
      $filter = ['$or' => [['public' => true], ['user' => $authorizedUser->getId()->toString()]]];
    else
      $filter = ['public' => true];

    // Get the collections
    $options = $this->createSelectOptions($request, ['sort' => '-createdAt']);
    $collections = $this->collectionRepository->findManyBy($filter, $options);

    // Return the response
    return $this->serialize($request, $response, $collections);
  }

  // Return all collections owned by a user as a JSON response
  public function getUserCollections(Request $request, Response $response, User $user)
  {
    // Get the authorized user
    $authorizedUser = $request->getAttribute('authUser');

    // Get the repository filter
    if ($authorizedUser !== null)
      $filter = ['user' => $user->getId()->toString(), '$or' => [['public' => true], ['user' => $authorizedUser->getId()->toString()]]];
    else
      $filter = ['user' => $user->getId()->toString(), 'public' => true];

    // Get the collections
    $options = $this->createSelectOptions($request, ['sort' => '-createdAt']);
    $collections = $this->collectionRepository->findManyBy($filter, $options);

    // Return the response
    return $this->serialize($request, $response, $collections)
      ->withStatus(200);
  }

  // Return all collections owned by the authorized user as a JSON response
  public function getAuthorizedUserCollections(Request $request, Response $response)
  {
    // Get the authorized user
    $authorizedUser = $request->getAttribute('authUser');

    // Return the response
    return $this->getUserCollections($request, $response, $authorizedUser);
  }

  // Post a new collection and return the collection as a JSON response
  public function postCollection(Request $request, Response $response, SnowflakeGenerator $snowflakeGenerator)
  {
    // Get the authorized user
    $authorizedUser = $request->getAttribute('authUser');

    // Get and validate the body parameters
    $params = (new Validator())
      ->withRequired('title', 'string|notempty|maxlength:256')
      ->withOptional('description', 'string|maxlength:256', '')
      ->withOptional('public', 'bool', true)
      ->validate($request->getParsedBody())
      ->resultOrThrowBadRequest($request);

    // Create the collection
    $collection = new Collection();
    $collection->generateId($snowflakeGenerator);
    $collection->setName($collection->getId()->toBase64());
    $collection->setUser($authorizedUser);
    $collection->setTitle($params['title']);
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
    // Get the authorized user
    $authorizedUser = $request->getAttribute('authUser');

    // Check if the authorized user can read this collection
    if (!$this->canReadCollection($collection, $authorizedUser))
      throw CollectionResolverMiddleware::createIdNotFound($request, $collection->getId()->toString());

    // Return the response
    return $this->serialize($request, $response, $collection);
  }

  // Patch a collection and return the collection as a JSON response
  public function patchCollection(Request $request, Response $response, Collection $collection)
  {
    // Get the authorized user
    $authorizedUser = $request->getAttribute('authUser');

    // Check if the authorized user can read and modify this collection
    if (!$this->canReadCollection($collection, $authorizedUser))
      throw CollectionResolverMiddleware::createIdNotFound($request, $collection->getId()->toString());
    if (!$this->canModifyCollection($collection, $authorizedUser))
      throw new HttpForbiddenException($request, "The current authorized user is not allowed to modify the collection with id \"{$collection->getId()}\"");

    // Get and validate the body parameters
    $params = (new Validator())
      ->withOptional('title', 'string|notempty|maxlength:256')
      ->withOptional('description', 'string|maxlength:256')
      ->withOptional('public', 'bool')
      ->validate($request->getParsedBody())
      ->resultOrThrowBadRequest($request);

    // Modify the collection
    if ($params['title'] !== null)
      $collection->setTitle($params['title']);
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
  public function deleteCollection(Request $request, Response $response, Collection $collection)
  {
    // Get the authorized user
    $authorizedUser = $request->getAttribute('authUser');

    // Check if the authorized user can read and modify this collection
    if (!$this->canReadCollection($collection, $authorizedUser))
      throw CollectionResolverMiddleware::createIdNotFound($request, $collection->getId()->toString());
    if (!$this->canModifyCollection($collection, $authorizedUser))
      throw new HttpForbiddenException($request, "The current authorized user is not allowed to modify the collection with id \"{$collection->getId()}\"");

    // Delete the collection from the repository
    $this->collectionRepository->delete($collection);

    // Return the response
    return $response
      ->withStatus(204);
  }

  // Get all images in a collection as a JSON response
  public function getCollectionImages(Request $request, Response $response, Collection $collection)
  {
    // Get the authorized user
    $authorizedUser = $request->getAttribute('authUser');

    // Check if the authorized user can read this collection
    if (!$this->canReadCollection($collection, $authorizedUser))
      throw CollectionResolverMiddleware::createIdNotFound($request, $collection->getId()->toString());

    // Return the response
    return $this->serialize($request, $response, $collection->getImages());
  }

  // Put an image in a collection
  public function putCollectionImage(Request $request, Response $response, Collection $collection, Image $image)
  {
    // Get the authorized user
    $authorizedUser = $request->getAttribute('authUser');

    // Check if the authorized user can read and modify this collection
    if (!$this->canReadCollection($collection, $authorizedUser))
      throw CollectionResolverMiddleware::createIdNotFound($request, $collection->getId()->toString());
    if (!$this->canModifyCollection($collection, $authorizedUser))
      throw new HttpForbiddenException($request, "The current authorized user is not allowed to modify the collection with id \"{$collection->getId()}\"");

    // Check if the image is already in the collection
    if (ArrayUtils::any($collection->getImages(), fn($i) => $i->getId() == $image->getId()))
      throw new HttpBadRequestException($request, "The collection with id \"{$collection->getId()}\" already contains the image with id \"{$image->getId()}\"");

    // Add the image to the collection
    $collection->addImage($image);

    // Update the collection in the repository
    $this->collectionRepository->update($collection->setUpdatedAt(new \DateTime()));

    // Return the response
    return $response
      ->withStatus(204);
  }

  // Delete an image in a collection
  public function deleteCollectionImage(Request $request, Response $response, Collection $collection, Image $image)
  {
    // Get the authorized user
    $authorizedUser = $request->getAttribute('authUser');

    // Check if the authorized user can read and modify this collection
    if (!$this->canReadCollection($collection, $authorizedUser))
      throw CollectionResolverMiddleware::createIdNotFound($request, $collection->getId()->toString());
    if (!$this->canModifyCollection($collection, $authorizedUser))
      throw new HttpForbiddenException($request, "The current authorized user is not allowed to modify the collection with id \"{$collection->getId()}\"");

    // Check if the image is not in the collection
    if (!ArrayUtils::any($collection->getImages(), fn($i) => $i->getId() == $image->getId()))
      throw new HttpBadRequestException($request, "The collection with id \"{$collection->getId()}\" does not contain the image with id \"{$image->getId()}\"");

    // Remove the image from the collection
    $collection->removeImage($image);

    // Update the collection in the repository
    $this->collectionRepository->update($collection->setUpdatedAt(new \DateTime()));

    // Return the response
    return $response
      ->withStatus(204);
  }


  // Return if the authorized user can read a collection
  private function canReadCollection(Collection $collection, ?User $authorizedUser): bool
  {
    // Check if the collection is public
    if ($collection->getPublic())
      return true;

    // Check if the authorized user can modify the collection
    if (!$this->canModifyCollection($collection, $authorizedUser))
      return false;

    // All checks passed
    return true;
  }

  // Return if the authorized user can modify a collection
  private function canModifyCollection(Collection $collection, ?User $authorizedUser): bool
  {
    // Check if the authorized user is empty
    if ($authorizedUser === null)
      return false;

    // Check if the identifiers of the users match
    if ($collection->getUser()->getId() != $authorizedUser->getId())
      return false;

    // All checks passed
    return true;
  }
}
