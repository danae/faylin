<?php
namespace Danae\Faylin\App\Controllers\Backend;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpForbiddenException;
use Symfony\Component\Serializer\Serializer;

use Danae\Faylin\Model\User;
use Danae\Faylin\Validator\Validator;


// Controller that defines routes for users
final class UserController extends AbstractBackendController
{
  // Return all users as a JSON response
  public function index(Request $request, Response $response)
  {
    // Get the images
    $options = $this->createSelectOptions($request, ['sort' => '-createdAt']);
    $users = $this->userRepository->select([], $options);

    // Return the response
    return $this->serialize($request, $response, $users)
      ->withStatus(200);
  }

  // Get a user as a JSON response
  public function get(Request $request, Response $response, User $user)
  {
    // Return the response
    return $this->serialize($request, $response, $user)
      ->withStatus(200);
  }

  // Get the authorized user as a JSON response
  public function getMe(Request $request, Response $response, User $authUser)
  {
    // Return the response
    return $this->get($request, $response, $authUser);
  }

  // Patch a user and return the user as a JSON response
  public function patch(Request $request, Response $response, User $user, User $authUser)
  {
    // Check if the authorized user owns this user
    if ($authUser->getId() !== $user->getId())
      throw new HttpForbiddenException($request, "The current authorized user is not allowed to modify the user with id \"{$user->getId()}\"");

    // Get and validate the parameters
    $params = (new Validator())
      ->withOptional('name', 'string|maxlength:32', null)
      ->withOptional('email', 'email|maxlength:256', null)
      ->validate($request->getParsedBody())
      ->resultOrThrowBadRequest($request);

    // Modify the user
    if ($params['name'] !== null)
      $user->setName($params['name']);
    if ($params['email'] !== null)
      $user->setEmail($params['email']);
    $user->setUpdatedAt(new \DateTime());

    // Update the user in the repository
    $this->userRepository->update($user);

    // Return the response
    return $this->serialize($request, $response, $user)
      ->withStatus(200);
  }

  // Patch the authorized user and return the user as a JSON response
  public function patchMe(Request $request, Response $response, User $authUser)
  {
    // Return the response
    return $this->patch($request, $response, $authUser, $authUser);
  }

  // Return all images owned by a user as a JSON response
  public function images(Request $request, Response $response, User $user)
  {
    // Get the images
    $options = $this->queryToSelectOptions($request, ['sort' => '-createdAt']);
    $images = $this->imageRepository->select(['userId' => $user->getId()], $options);

    // Return the response
    return $this->serialize($request, $response, $images)
      ->withStatus(200);
  }

  // Return all images owned by the authorized user as a JSON response
  public function imagesMe(Request $request, Response $response, User $authUser)
  {
    // Return the response
    return $this->images($request, $response, $authUser);
  }
}
