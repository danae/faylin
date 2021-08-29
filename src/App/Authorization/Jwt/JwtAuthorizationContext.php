<?php
namespace Danae\Faylin\App\Authorization\Jwt;

use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\SignatureInvalidException;
use Symfony\Component\Serializer\Serializer;

use Danae\Faylin\App\Authorization\AuthorizationException;
use Danae\Faylin\Model\User;
use Danae\Faylin\Model\UserRepositoryInterface;
use Danae\Faylin\Model\Authorization\Token;


// Class that defines an authorization context for JWT (JSON Web Tokens)
final class JwtAuthorizationContext
{
  // The user repository to use with the context
  private $userRepository;

  // The serializer to use with the context
  private $serializer;

  // The key for signing tokens
  private $key;

  // The algorithm for signing tokens
  private $algorithm;


  // Constructor
  public function __construct(UserRepositoryInterface $userRepository, Serializer $serializer, string $key, string $algorithm = 'HS256')
  {
    $this->userRepository = $userRepository;
    $this->serializer = $serializer;
    $this->key = $key;
    $this->algorithm = $algorithm;
  }

  // Return an encoded token
  public function encode(Token $token): string
  {
    // Normalize the token
    $token = $this->serializer->normalize($token);

    // Encode the token
    return JWT::encode($token, $this->key);
  }

  // Return a decoded token
  public function decode(string $token): Token
  {
    try
    {
      // Decode the token
      $token = JWT::decode($token, $this->key, [$this->algorithm]);

      // Check the token claims
      if (!isset($token->sub))
        throw new AuthorizationException("No subject has been provided in the token");
      if (!isset($token->exp))
        throw new AuthorizationException("No expiration time has been provided in the token");
      if (!isset($token->iat))
        throw new AuthorizationException("No issued time has been provided in the token");
      if (!isset($token->jti))
        throw new AuthorizationException("No JWT ID has been provided in the token");

      // Denormalize the token
      $token = $this->serializer->denormalize((array)$token, Token::class, null, ['userRepository' => $this->userRepository]);

      // Check the token subject
      if ($token->getUser() === null)
        throw new AuthorizationException("The subject of the token is invalid");

      // Return the denormalized token
      return $token;
    }
    catch (SignatureInvalidException $ex)
    {
      throw new AuthorizationException("The signature of the token is invalid", $ex);
    }
    catch (BeforeValidException $ex)
    {
      throw new AuthorizationException("The issued date or not before date of the token is in the future", $ex);
    }
    catch (ExpiredException $ex)
    {
      throw new AuthorizationException("The token has been expired", $ex);
    }
    catch (\InvalidArgumentException | \UnexpectedValueException $ex)
    {
      throw new AuthorizationException("Could not decode the token: {$ex->getMessage()}", $ex);
    }
  }
}
