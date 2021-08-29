<?php
namespace Danae\Faylin\App\Authorization\Jwt;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Exception\HttpUnauthorizedException;

use Danae\Faylin\App\Authorization\AuthorizationException;
use Danae\Faylin\App\Authorization\AuthorizationStrategyInterface;
use Danae\Faylin\Model\User;


// Authorization strategy that uses JWT (JSON Web Tokens)
final class JwtAuthorizationStrategy implements AuthorizationStrategyInterface
{
  // The authorization context to use with the strategy
  private $context;


  // Constructor
  public function __construct(JwtAuthorizationContext $context)
  {
    $this->context = $context;
  }

  // Return if this strategy is able to authorize the request
  public function canAuthorize(Request $request): bool
  {
    if (!$request->hasHeader('Authorization'))
      return false;
    if (strpos($request->getHeaderLine('Authorization'), 'Bearer') !== 0)
      return false;

    return true;
  }

  // Return the authorized user from the request
  public function authorize(Request $request): User
  {
    // Check if this strategy is able to authorize the request
    if (!$this->canAuthorize($request))
      throw new \RuntimeException("JwtAuthorizationStrategy::authorize() cannot use the specified request to authorize a user");

    // Get the serialized token
    $token = static::parseHeader($request->getHeaderLine('Authorization'));
    if (empty($token))
      throw new AuthorizationException("The request contains an invalid authorization header");

    // Get the token and its associated user
    $token = $this->context->decode($token);

    // Set the token as attribute on the request
    $request = $request->withAttribute('authToken', $token);

    // Return the user
    return $token->getUser();
  }


  // Parse the authorization header for bearer authentication
  private static function parseHeader(string $header): ?string
  {
    // Check if the header contains anything
    $header = explode(' ', $header, 2);
    if ($header === false || count($header) !== 2)
      return null;

    // Check if the header contains a bearer token
    if ($header[0] !== 'Bearer')
      return null;

    // Return the token
    return $header[1];
  }
}
