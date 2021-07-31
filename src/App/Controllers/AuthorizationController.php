<?php
namespace Danae\Faylin\App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;

use Danae\Faylin\Model\Token;
use Danae\Faylin\Model\User;
use Danae\Faylin\Utils\Snowflake;
use Danae\Faylin\Validator\Validator;


// Controller that defines routes for authorization
final class AuthorizationController extends AbstractController
{
  // The authorization context to use with the controller
  protected $authorizationContext;


  // Request an access token
  public function token(Request $request, Response $response, Snowflake $snowflake)
  {
    // Get and validate the parameters
    $params = (new Validator())
      ->withRequired('username', 'string|maxlength:32')
      ->withRequired('password', 'string')
      ->validate($request->getParsedBody())
      ->resultOrThrowBadRequest($request);

    // Check if the user is valid
    $user = $this->userRepository->selectOne(['name' => $params['username']]);
    if ($user === null)
      throw new HttpBadRequestException($request, "Invalid username");
    if (!$user->verifyPassword($params['password']))
      throw new HttpBadRequestException($request, "Invalid password");

    // Create a token
    $token = (new Token())
      ->setId($snowflake->generateBase64String())
      ->setUserId($user->getId())
      ->setIssuedAt(new \DateTime())
      ->setExpiresAt(new \DateTime('60 minutes'));

    // Encode the token
    $token = $this->authorizationContext->encode($token);

    // Create the body
    $body = ['token' => $token];

    // Return the response
    return $this->serialize($request, $response, $body)
      ->withStatus(200);
  }
}
