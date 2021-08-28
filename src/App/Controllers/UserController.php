<?php
namespace Danae\Faylin\App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpForbiddenException;
use Symfony\Component\Serializer\Serializer;

use Danae\Faylin\Model\User;
use Danae\Faylin\Validator\Validator;


// Controller that defines routes for users
final class UserController extends AbstractController
{
  // Return all users as a JSON response
  public function index(Request $request, Response $response)
  {
    // Get the images
    $options = $this->createSelectOptions($request, ['sort' => '-createdAt']);
    $users = $this->userRepository->select(['public' => true], $options);

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

  // Patch a user and return the user as a JSON response
  public function patch(Request $request, Response $response, User $user, User $authUser)
  {
    // Check if the authorized user owns this user
    if ($authUser->getId() !== $user->getId())
      throw new HttpForbiddenException($request, "The current authorized user is not allowed to modify the user with id \"{$user->getId()}\"");

    // Get and validate the parameters
    $params = (new Validator())
      ->withOptional('name', 'string|notempty|maxlength:32')
      ->withOptional('description', 'string|maxlength:256', '')
      ->withOptional('public', 'bool')
      ->withOptional('avatarId', 'string|maxlength:256')
      ->validate($request->getParsedBody())
      ->resultOrThrowBadRequest($request);

    // Modify the user
    if ($params['name'] !== null)
      $user->setName($params['name']);
    if ($params['description'] !== null)
      $user->setDescription($params['description']);
    if ($params['public'] !== null)
      $user->setPublic($params['public']);
    if ($params['avatarId'] !== null)
      $user->setAvatarId($params['avatarId']);
    $user->setUpdatedAt(new \DateTime());

    // Update the user in the repository
    $this->userRepository->update($user);

    // Return the response
    return $this->serialize($request, $response, $user)
      ->withStatus(200);
  }

  // Return all collections owned by a user as a JSON response
  public function collections(Request $request, Response $response, User $user)
  {
    // Get the collections
    $options = $this->createSelectOptions($request, ['sort' => '-createdAt']);
    $collections = $this->collectionRepository->select(['userId' => $user->getId()], $options);

    // Return the response
    return $this->serialize($request, $response, $collections)
      ->withStatus(200);
  }

  // Return all images owned by a user as a JSON response
  public function images(Request $request, Response $response, User $user)
  {
    // Get the images
    $options = $this->createSelectOptions($request, ['sort' => '-createdAt']);
    $images = $this->imageRepository->select(['userId' => $user->getId()], $options);

    // Return the response
    return $this->serialize($request, $response, $images)
      ->withStatus(200);
  }

  // Get the authorized user as a JSON response
  public function getAuthorized(Request $request, Response $response, User $authUser)
  {
    // Return the response
    return $this->get($request, $response, $authUser);
  }

  // Patch the authorized user and return the user as a JSON response
  public function patchAuthorized(Request $request, Response $response, User $authUser)
  {
    // Return the response
    return $this->patch($request, $response, $authUser, $authUser);
  }

  // Update the email address of the authorized user and return the user as a JSON response
  public function updateEmailAuthorized(Request $request, Response $response, User $authUser)
  {
    // Get and validate the parameters
    $params = (new Validator())
      ->withRequired('email', 'email|notempty|maxlength:256')
      ->withRequired('currentPassword', 'string|notempty|maxlength:256')
      ->validate($request->getParsedBody())
      ->resultOrThrowBadRequest($request);

    // Check if the password matches
    if (!$authUser->verifyPassword($params['currentPassword']))
      throw new HttpBadRequestException($request, "The password is incorrect");

    // Modify the user
    $authUser
      ->setEmail($params['email'])
      ->setUpdatedAt(new \DateTime());

    // Update the user in the repository
    $this->userRepository->update($authUser);

    // Return the response
    return $this->serialize($request, $response, $authUser)
      ->withStatus(200);
  }

  // Update the password of the authorized user and return the user as a JSON response
  public function updatePasswordAuthorized(Request $request, Response $response, User $authUser)
  {
    // Get and validate the parameters
    $params = (new Validator())
      ->withRequired('password', 'string|notempty|maxlength:256')
      ->withRequired('currentPassword', 'string|notempty|maxlength:256')
      ->validate($request->getParsedBody())
      ->resultOrThrowBadRequest($request);

    // Check if the password matches
    if (!$authUser->verifyPassword($params['currentPassword']))
      throw new HttpBadRequestException($request, "The password is incorrect");

    // Modify the user
    $authUser
      ->hashPassword($params['password'])
      ->setUpdatedAt(new \DateTime());

    // Update the user in the repository
    $this->userRepository->update($authUser);

    // Return the response
    return $this->serialize($request, $response, $authUser)
      ->withStatus(200);
  }

  // Delete the authorized user
  public function deleteAuthorized(Request $request, Response $response, User $authUser)
  {
    // Get and validate the parameters
    $params = (new Validator())
      ->withRequired('currentPassword', 'string|notempty|maxlength:256')
      ->validate($request->getParsedBody())
      ->resultOrThrowBadRequest($request);

    // Check if the password matches
    if (!$authUser->verifyPassword($params['currentPassword']))
      throw new HttpBadRequestException($request, "The password is incorrect");

    // Remove all images owned by the user
    $images = $this->imageRepository->select(['userId' => $authUser->getId()]);
    foreach ($images as $image)
    {
      // Delete the image from the repository
      $this->imageRepository->delete($image);
      $this->imageRepository->deleteFile($image);
    }

    // Remove the user from the repository
    $this->userRepository->delete($authUser);

    // Return the response
    return $response
      ->withStatus(204);
  }

  // Return all collections owned by the authorized user as a JSON response
  public function collectionsAuthorized(Request $request, Response $response, User $authUser)
  {
    // Return the response
    return $this->collections($request, $response, $authUser);
  }

  // Return all images owned by the authorized user as a JSON response
  public function imagesAuthorized(Request $request, Response $response, User $authUser)
  {
    // Return the response
    return $this->images($request, $response, $authUser);
  }
}
