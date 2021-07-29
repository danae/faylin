<?php
namespace Danae\Faylin\App\Middleware\Authorization;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Exception\HttpUnauthorizedException;

use Danae\Faylin\Model\UserRepository;


// Middleware that adds basic authorization
final class BasicAuthorizationMiddleware extends AuthorizationMiddleware
{
  // The user repository to use in the middleware
  private $userRepository;

  // Constructor
  public function __construct(UserRepository $userRepository)
  {
    $this->userRepository = $userRepository;
  }

  // Authorize a user using the request and returns attributes to set on the request
  public function authorize(Request $request): array
  {
    // Get the parsed authorization header
    $authorization = self::parseHeader($request->getHeaderLine('Authorization'));
    if (empty($authorization))
      throw new HttpUnauthorizedException($request, "The Authorization header contains no credentials");

    // Check the username and password
    $user = $this->userRepository->selectOne(['name' => $authorization['username']]);
    if ($user == null)
      throw new HttpUnauthorizedException($request, "Incorrect username");
    if (!$user->verifyPassword($authorization['password']))
      throw new HttpUnauthorizedException($request, "Incorrect password");

    // Return the attributes
    return ['authUser' => $user];
  }


  // Parse the authorization header for basic authentication
  private static function parseHeader(string $header): ?array
  {
    if (strpos($header, 'Basic') !== 0)
      return null;

    $header = base64_decode(substr($header, 6));
    if ($header === false)
      return null;

    $header = explode(':', $header, 2);
    return ['username' => $header[0], 'password' => isset($header[1]) ? $header[1] : null];
  }
}
