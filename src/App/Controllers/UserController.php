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
    // Get the authorized user
    $authorizedUser = $request->getAttribute('authUser');

    // Get the repository filter
    if ($authorizedUser !== null)
      $filter = ['$or' => [['public' => true], ['user' => $authorizedUser->getId()->toString()]]];
    else
      $filter = ['public' => true];

    // Get the images
    $options = $this->createSelectOptions($request, ['sort' => '-createdAt']);
    $users = $this->userRepository->findManyBy($filter, $options);

    // Return the response
    return $this->serialize($request, $response, $users)
      ->withStatus(200);
  }

  // Get a user as a JSON response
  public function getUser(Request $request, Response $response, User $user)
  {
    // Get the authorized user
    $authorizedUser = $request->getAttribute('authUser');

    // Check if the authorized user can read this user
    if (!$this->canReadUser($user, $authorizedUser))
      throw UserResolverMiddleware::createIdNotFound($request, $user->getId()->toString());

    // Return the response
    return $this->serialize($request, $response, $user)
      ->withStatus(200);
  }

  // Patch a user and return the user as a JSON response
  public function patchUser(Request $request, Response $response, User $user)
  {
    // Get the authorized user
    $authorizedUser = $request->getAttribute('authUser');

    // Check if the authorized user can read and modify this usere
    if (!$this->canReadUser($user, $authorizedUser))
      throw UserResolverMiddleware::createIdNotFound($request, $user->getId()->toString());
    if (!$this->canModifyUser($user, $authorizedUser))
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

  // Get the authorized user as a JSON response
  public function getAuthorizedUser(Request $request, Response $response)
  {
    // Get the authorized user
    $authorizedUser = $request->getAttribute('authUser');

    // Return the response
    return $this->getUser($request, $response, $authorizedUser);
  }

  // Patch the authorized user and return the user as a JSON response
  public function patchAuthorizedUser(Request $request, Response $response)
  {
    // Get the authorized user
    $authorizedUser = $request->getAttribute('authUser');

    // Return the response
    return $this->patchUser($request, $response, $authorizedUser, $authorizedUser);
  }

  // Update the email address of the authorized user and return the user as a JSON response
  public function updateAuthorizedUserEmail(Request $request, Response $response)
  {
    // Get the authorized user
    $authorizedUser = $request->getAttribute('authUser');

    // Get and validate the parameters
    $params = (new Validator())
      ->withRequired('email', 'email|notempty|maxlength:256')
      ->withRequired('currentPassword', 'string|notempty|maxlength:256')
      ->validate($request->getParsedBody())
      ->resultOrThrowBadRequest($request);

    // Check if the password matches
    if (!$authorizedUser->verifyPassword($params['currentPassword']))
      throw new HttpBadRequestException($request, "The password is incorrect");

    // Modify the user
    $authorizedUser->setEmail($params['email']);

    // Update the user in the repository
    $this->userRepository->update($authorizedUser->setUpdatedAt(new \DateTime()));

    // Return the response
    return $this->serialize($request, $response, $authorizedUser)
      ->withStatus(200);
  }

  // Update the password of the authorized user and return the user as a JSON response
  public function updateAuthorizedUserPassword(Request $request, Response $response)
  {
    // Get the authorized user
    $authorizedUser = $request->getAttribute('authUser');

    // Get and validate the parameters
    $params = (new Validator())
      ->withRequired('password', 'string|notempty|maxlength:256')
      ->withRequired('currentPassword', 'string|notempty|maxlength:256')
      ->validate($request->getParsedBody())
      ->resultOrThrowBadRequest($request);

    // Check if the password matches
    if (!$authorizedUser->verifyPassword($params['currentPassword']))
      throw new HttpBadRequestException($request, "The password is incorrect");

    // Modify the user
    $authorizedUser->hashPassword($params['password']);

    // Update the user in the repository
    $this->userRepository->update($authorizedUser->setUpdatedAt(new \DateTime()));

    // Return the response
    return $this->serialize($request, $response, $authorizedUser)
      ->withStatus(200);
  }

  // Delete the authorized user
  public function deleteAuthorizedUser(Request $request, Response $response)
  {
    // Get the authorized user
    $authorizedUser = $request->getAttribute('authUser');

    // Get and validate the parameters
    $params = (new Validator())
      ->withRequired('currentPassword', 'string|notempty|maxlength:256')
      ->validate($request->getParsedBody())
      ->resultOrThrowBadRequest($request);

    // Check if the password matches
    if (!$authorizedUser->verifyPassword($params['currentPassword']))
      throw new HttpBadRequestException($request, "The password is incorrect");

    // Remove all images owned by the user
    $images = $this->imageRepository->findManyBy(['user' => $authorizedUser->getId()]);
    foreach ($images as $image)
    {
      // Delete the image from the repository
      $this->imageRepository->delete($image);
      $this->imageRepository->deleteFile($image);
    }

    // Remove all collections owned by the user
    $collections = $this->collectionRepository->findManyBy(['user' => $authorizedUser->getId()]);
    foreach ($collections as $collection)
    {
      // Delete the collection from the repository
      $this->collectionRepository->delete($collection);
    }

    // Remove the user from the repository
    $this->userRepository->delete($authorizedUser);

    // Return the response
    return $response
      ->withStatus(204);
  }

  // Return all sessions owned by the authorized user as a JSON response
  public function getAuthorizedUserSessions(Request $request, Response $response)
  {
    // Get the authorized user
    $authorizedUser = $request->getAttribute('authUser');

    // Get the sessions
    $sessions = $this->sessionRepository->findManyBy(['user' => $authorizedUser->getId()->toString()], ['sort' => ['createdAt' => -1]]);

    // Return the response
    return $this->serialize($request, $response, $sessions)
      ->withStatus(200);
  }

  // Return a session owned by the authorized user as a JSON response
  public function getAuthorizedUserSession(Request $request, Response $response, string $sessionId)
  {
    // Get the authorized user
    $authorizedUser = $request->getAttribute('authUser');

    // Get the session
    $session = $this->sessionRepository->findBy(['_id' => $sessionId, 'user' => $authorizedUser->getId()->toString()]);
    if ($session == null)
      throw new HttpNotFoundException($request, "A session with id \"{$sessionId}\" coud not be found");

    // Return the response
    return $this->serialize($request, $response, $session)
      ->withStatus(200);
  }

  // Delete a session owned by the authorized user
  public function deleteAuthorizedUserSession(Request $request, Response $response, string $sessionId)
  {
    // Get the authorized user
    $authorizedUser = $request->getAttribute('authUser');

    // Get the session
    $session = $this->sessionRepository->findBy(['_id' => $sessionId, 'user' => $authorizedUser->getId()->toString()]);
    if ($session == null)
      throw new HttpNotFoundException($request, "A session with id \"{$sessionId}\" coud not be found");

    // Remove the session from the repository
    $this->sessionRepository->delete($session);

    // Return the response
    return $response
      ->withStatus(204);
  }


  // Return if the authorized user can read a user
  private function canReadUser(User $user, ?User $authorizedUser): bool
  {
    // Check if the user is public
    if ($user->getPublic())
      return true;

    // Check if the authorized user can modify the user
    if (!$this->canModifyUser($user, $authorizedUser))
      return false;

    // All checks passed
    return true;
  }

  // Return if the authorized user can modify a user
  private function canModifyUser(User $user, ?User $authorizedUser): bool
  {
    // Check if the authorized user is empty
    if ($authorizedUser === null)
      return false;

    // Check if the identifiers of the users match
    if ($user->getId() != $authorizedUser->getId())
      return false;

    // All checks passed
    return true;
  }
}
