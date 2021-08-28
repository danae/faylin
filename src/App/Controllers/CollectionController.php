<?php
namespace Danae\Faylin\App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpForbiddenException;
use Symfony\Component\Serializer\Serializer;

use Danae\Faylin\Model\Collection;
use Danae\Faylin\Model\User;
use Danae\Faylin\Utils\Snowflake;
use Danae\Faylin\Validator\Validator;


// Controller that defines routes for collections
final class CollectionController extends AbstractController
{
  // Return all collections as a JSON response
  public function index(Request $request, Response $response)
  {
    // Get the images
    $options = $this->createSelectOptions($request, ['sort' => '-createdAt']);
    $collections = $this->collectionRepository->select(['public' => true], $options);

    // Return the response
    return $this->serialize($request, $response, $collections);
  }

  // Post a new collection and return the collection as a JSON response
  public function post(Request $request, Response $response, User $authUser, Snowflake $snowflake)
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
    $collection = (new Collection())
      ->setId($snowflake->generateBase64String())
      ->setUserId($authUser->getId())
      ->setName($params['name'])
      ->setDescription($params['description'])
      ->setPublic($params['public'])
      ->setCreatedAt($now)
      ->setUpdatedAt($now);

    // Create the collection in the repository
    $this->collectionRepository->insert($collection);

    // Return the response
    return $this->serialize($request, $response, $collection)
      ->withStatus(201);
  }

  // Get a collection as a JSON response
  public function get(Request $request, Response $response, Collection $collection)
  {
    // Return the response
    return $this->serialize($request, $response, $collection);
  }

  // Patch a collection and return the collection as a JSON response
  public function patch(Request $request, Response $response, Collection $collection, User $authUser)
  {
    $now = new \DateTime();

    // Check if the authorized user owns this collection
    if ($authUser->getId() !== $collection->getUserId())
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
    $collection->setUpdatedAt($now);

    // Update the collection in the repository
    $this->collectionRepository->update($collection);

    // Return the response
    return $this->serialize($request, $response, $collection);
  }

  // Delete a collection
  public function delete(Request $request, Response $response, Collection $collection, User $authUser)
  {
    // Check if the authorized user owns this collection
    if ($authUser->getId() !== $collection->getUserId())
      throw new HttpForbiddenException($request, "The current authorized user is not allowed to delete the collection with id \"{$collection->getId()}\"");

    // Delete the collection from the repository
    $this->collectionRepository->delete($collection);

    // Return the response
    return $response
      ->withStatus(204);
  }
}
