<?php
namespace Danae\Faylin\App\Controllers;

use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpInternalServerErrorException;


// Controller that defines routes for authorization
final class AuthorizationController extends AbstractController
{
  // The authorization server to use with the controller
  protected $authorizationServer;


  // Request an access token
  public function token(Request $request, Response $response, Snowflake $snowflake)
  {
    try
    {
      // Try to respond to the request
      return $this->server->respondToAccessTokenRequest($request, $response);
    }
    catch (OAuthServerException $ex)
    {
      // All instances of OAuthServerException can be formatted into a HTTP response
      return $ex->generateHttpResponse($response);
    }
    catch (Exception $ex)
    {
      // Unknown exception
      throw new HttpInternalServerErrorException($request, $ex->getMessage(), $ex);
    }
  }
}
