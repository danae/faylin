<?php
namespace Danae\Faylin\App\Authorization\Basic;

use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpUnauthorizedException;

use Danae\Faylin\App\Authorization\AuthorizationException;
use Danae\Faylin\App\Authorization\AuthorizationStrategyInterface;
use Danae\Faylin\Model\User;
use Danae\Faylin\Model\UserRepository;


// Authorization strategy that uses basic authorization
final class BasicAuthorizationStrategy implements AuthorizationStrategyInterface
{
  // The user repository to use with this strategy
  private $userRepository;


  // Constructor
  public function __construct(UserRepository $userRepository)
  {
    $this->userRepository = $userRepository;
  }

  // Return if this strategy is able to authorize the request
  public function canAuthorize(Request $request): bool
  {
    if (!$request->hasHeader('Authorization'))
      return false;
    if (strpos($request->getHeaderLine('Authorization'), 'Basic') !== 0)
      return false;

    return true;
  }

  // Return the authorized user from the request
  public function authorize(Request $request): User
  {
    // Check if this strategy is able to authorize the request
    if (!$this->canAuthorize($request))
      throw new \RuntimeException("BasicAuthorizationStrategy::authorize() cannot use the specified request to authorize a user");

    // Get the parsed authorization header
    $authorization = static::parseHeader($request->getHeaderLine('Authorization'));
    if (empty($authorization))
      throw new AuthorizationException("The request contains an invalid authorization header");

    // Check the username and password
    $user = $this->userRepository->validate($authorization['username'], $authorization['password']);
    if ($user == null)
      throw new AuthorizationException("The authorization credentials are incorrect");

    // Return the user
    return $user;
  }


  // Parse the authorization header for basic authentication
  private static function parseHeader(string $header): ?array
  {
    // Check if the header contains anything
    $header = explode(' ', $header, 2);
    if ($header === false || count($header) !== 2)
      return null;

    // Check if the header contains a bearer token
    if ($header[0] !== 'Basic')
      return null;

    // Decode the credentials
    $credentials = base64_decode($header[1]);
    if ($credentials === false)
      return null;

    // Split and return the credentials
    $credentials = explode(':', $header, 2);
    return ['username' => $credentials[0], 'password' => $credentials[1] ?? null];
  }
}
