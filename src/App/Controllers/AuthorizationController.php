<?php
namespace Danae\Faylin\App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;

use Danae\Faylin\Model\Session;
use Danae\Faylin\Model\User;
use Danae\Faylin\Model\Authorization\Token;
use Danae\Faylin\Model\SnowflakeGenerator;
use Danae\Faylin\Validator\Validator;


// Controller that defines routes for authorization
final class AuthorizationController extends AbstractController
{
  // The authorization context to use with the controller
  protected $authorizationContext;


  // Request an access token
  public function token(Request $request, Response $response, SnowflakeGenerator $snowflakeGenerator)
  {
    // Get and validate the parameters
    $params = (new Validator())
      ->withRequired('username', 'string|notempty')
      ->withRequired('password', 'string|notempty')
      ->validate($request->getParsedBody())
      ->resultOrThrowBadRequest($request);

    // Check if the user is valid
    $user = $this->userRepository->validate($params['username'], $params['password']);
    if ($user === null)
      throw new HttpBadRequestException($request, "The combination of username and password is incorrect");

    // Issue a token for the user
    $token = $this->authorizationContext->issueFor($snowflakeGenerator->generate(), $user);

    // Create a session for the token
    $session = new Session();
    $session->generateId($snowflakeGenerator);
    $session->setUser($user);
    $session->setUserAgent($request->getServerParams()['HTTP_USER_AGENT']);
    $session->setUserAddress($request->getServerParams()['REMOTE_ADDR']);
    $session->setAccessToken($token);

    // Insert the session in the repository
    $this->sessionRepository->insert($session);

    // Return the response
    return $this->serialize($request, $response, ['accessToken' => $token])
      ->withStatus(200);
  }
}
