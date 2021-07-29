<?php
namespace Danae\Faylin\App\Middleware\Authorization;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Exception\HttpUnauthorizedException;

use Danae\Faylin\App\Authorization\AuthorizationContext;
use Danae\Faylin\App\Authorization\AuthorizationException;
use Danae\Faylin\Model\UserRepository;


// Middleware that adds authorization using JWT (JSON Web Tokens)
final class JwtAuthorizationMiddleware extends AuthorizationMiddleware
{
  // The user repository to use in the middleware
  private $userRepository;

  // The authorization context to use in the middleware
  private $authorizationContext;


  // Constructor
  public function __construct(UserRepository $userRepository, AuthorizationContext $authorizationContext)
  {
    $this->userRepository = $userRepository;
    $this->authorizationContext = $authorizationContext;
  }

  // Authorize a user using the request and returns attributes to set on the request
  public function authorize(Request $request): array
  {
    // Get the serialized token
    $token = self::parseHeader($request->getHeaderLine('Authorization'));
    if (empty($token))
      throw new HttpUnauthorizedException($request, "The Authorization header contains no credentials");

    try
    {
      // Get the token ad its associated user
      [$token, $user] = $this->authorizationContext->decode($token);

      // Return the attributes
      return ['authUser' => $user, 'authToken' => $token];
    }
    catch (AuthorizationException $ex)
    {
      throw new HttpUnauthorizedException($request, $ex->getMessage(), $ex);
    }
  }


  // Parse the authorization header for bearer authentication
  private static function parseHeader(string $header): ?string
  {
    if (strpos($header, 'Bearer') !== 0)
      return null;
    else
      return substr($header, 7);
  }
}
