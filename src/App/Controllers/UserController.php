<?php
namespace Danae\Faylin\App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpForbiddenException;
use Symfony\Component\Serializer\Serializer;

use Danae\Faylin\Model\Session;
use Danae\Faylin\Model\User;
use Danae\Faylin\Validator\Validator;


// Controller that defines routes for users
final class UserController extends AbstractController
{
  // Return all users as a JSON response
  public function getUsers(Request $request, Response $response)
  {
    // Get the images
    $options = $this->createSelectOptions($request, ['sort' => '-createdAt']);
    $users = $this->userRepository->findManyBy(['public' => true], $options);

    // Return the response
    return $this->serialize($request, $response, $users)
      ->withStatus(200);
  }

  // Get a user as a JSON response
  public function getUser(Request $request, Response $response, User $user)
  {
    // Return the response
    return $this->serialize($request, $response, $user)
      ->withStatus(200);
  }

  // Patch a user and return the user as a JSON response
  public function patchUser(Request $request, Response $response, User $user, User $authUser)
  {
    // Check if the authorized user owns this user
    if ($authUser->getId() != $user->getId())
      throw new HttpForbiddenException($request, "The current authorized user is not allowed to modify the user with id \"{$user->getId()}\"");

    // Get and validate the parameters
    $params = (new Validator())
      ->withOptional('name', 'string|notempty|maxlength:32')
      ->withOptional('title', 'string|notempty|maxlength:64')
      ->withOptional('description', 'string|maxlength:256', '')
      ->withOptional('public', 'bool')
      ->withOptional('avatarId', 'string|maxlength:256')
      ->validate($request->getParsedBody())
      ->resultOrThrowBadRequest($request);

    // Modify the user
    if ($params['name'] !== null)
      $user->setName($params['name']);
    if ($params['title'] !== null)
      $user->setTitle($params['title']);
    if ($params['description'] !== null)
      $user->setDescription($params['description']);
    if ($params['public'] !== null)
      $user->setPublic($params['public']);
    if ($params['avatarId'] !== null)
      $user->setAvatarId($params['avatarId']);

    // Update the user in the repository
    $this->userRepository->update($user->setUpdatedAt(new \DateTime()));

    // Return the response
    return $this->serialize($request, $response, $user)
      ->withStatus(200);
  }

  // Return all collections owned by a user as a JSON response
  public function getUserCollections(Request $request, Response $response, User $user)
  {
    // Get the collections
    $options = $this->createSelectOptions($request, ['sort' => '-createdAt']);
    $collections = $this->collectionRepository->findManyBy(['user' => $user->getId()->toString()], $options);

    // Return the response
    return $this->serialize($request, $response, $collections)
      ->withStatus(200);
  }

  // Return all images owned by a user as a JSON response
  public function getUserImages(Request $request, Response $response, User $user)
  {
    // Get the images
    $options = $this->createSelectOptions($request, ['sort' => '-createdAt']);
    $images = $this->imageRepository->findManyBy(['user' => $user->getId()->toString()], $options);

    // Return the response
    return $this->serialize($request, $response, $images)
      ->withStatus(200);
  }

  // Get the authorized user as a JSON response
  public function getAuthorizedUser(Request $request, Response $response, User $authUser)
  {
    // Return the response
    return $this->getUser($request, $response, $authUser);
  }

  // Patch the authorized user and return the user as a JSON response
  public function patchAuthorizedUser(Request $request, Response $response, User $authUser)
  {
    // Return the response
    return $this->patchUser($request, $response, $authUser, $authUser);
  }

  // Update the email address of the authorized user and return the user as a JSON response
  public function updateAuthorizedUserEmail(Request $request, Response $response, User $authUser)
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
    $authUser->setEmail($params['email']);

    // Update the user in the repository
    $this->userRepository->update($authUser->setUpdatedAt(new \DateTime()));

    // Return the response
    return $this->serialize($request, $response, $authUser)
      ->withStatus(200);
  }

  // Update the password of the authorized user and return the user as a JSON response
  public function updateAuthorizedUserPassword(Request $request, Response $response, User $authUser)
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
    $authUser->hashPassword($params['password']);

    // Update the user in the repository
    $this->userRepository->update($authUser->setUpdatedAt(new \DateTime()));

    // Return the response
    return $this->serialize($request, $response, $authUser)
      ->withStatus(200);
  }

  // Delete the authorized user
  public function deleteAuthorizedUser(Request $request, Response $response, User $authUser)
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
    $images = $this->imageRepository->findManyBy(['user' => $authUser->getId()]);
    foreach ($images as $image)
    {
      // Delete the image from the repository
      $this->imageRepository->delete($image);
      $this->imageRepository->deleteFile($image);
    }

    // Remove all collections owned by the user
    $collections = $this->collectionRepository->findManyBy(['user' => $authUser->getId()]);
    foreach ($collections as $collection)
    {
      // Delete the collection from the repository
      $this->collectionRepository->delete($collection);
    }

    // Remove the user from the repository
    $this->userRepository->delete($authUser);

    // Return the response
    return $response
      ->withStatus(204);
  }

  // Return all sessions owned by the authorized user as a JSON response
  public function getAuthorizedUserSessions(Request $request, Response $response, User $authUser)
  {
    // Get the sessions
    $sessions = $this->sessionRepository->findManyBy(['user' => $authUser->getId()->toString()], ['sort' => ['createdAt' => -1]]);

    // Return the response
    return $this->serialize($request, $response, $sessions)
      ->withStatus(200);
  }

  // Return a session owned by the authorized user as a JSON response
  public function getAuthorizedUserSession(Request $request, Response $response, User $authUser, string $sessionId)
  {
    // Get the session
    $session = $this->sessionRepository->findBy(['_id' => $sessionId, 'user' => $authUser->getId()->toString()]);
    if ($session == null)
      throw new HttpNotFoundException($request, "A session with id \"{$sessionId}\" coud not be found");

    // Return the response
    return $this->serialize($request, $response, $session)
      ->withStatus(200);
  }

  // Delete a session owned by the authorized user
  public function deleteAuthorizedUserSession(Request $request, Response $response, User $authUser, string $sessionId)
  {
    // Get the session
    $session = $this->sessionRepository->findBy(['_id' => $sessionId, 'user' => $authUser->getId()->toString()]);
    if ($session == null)
      throw new HttpNotFoundException($request, "A session with id \"{$sessionId}\" coud not be found");

    // Remove the session from the repository
    $this->sessionRepository->delete($session);

    // Return the response
    return $response
      ->withStatus(204);
  }

  // Return all collections owned by the authorized user as a JSON response
  public function getAuthorizedUserCollections(Request $request, Response $response, User $authUser)
  {
    // Return the response
    return $this->getUserCollections($request, $response, $authUser);
  }

  // Return all images owned by the authorized user as a JSON response
  public function getAuthorizedUserImages(Request $request, Response $response, User $authUser)
  {
    // Return the response
    return $this->getUserImages($request, $response, $authUser);
  }
}
