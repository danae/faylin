<?php
namespace Danae\Faylin\App\Authorization\Jwt;

use Lcobucci\Clock\SystemClock;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\Validation\RequiredConstraintsViolated;
use Lcobucci\JWT\Validation\Constraint\LooseValidAt;
use Lcobucci\JWT\Validation\Constraint\RelatedTo;

use Danae\Faylin\App\Authorization\AuthorizationException;
use Danae\Faylin\Model\SessionRepositoryInterface;
use Danae\Faylin\Model\Snowflake;
use Danae\Faylin\Model\User;
use Danae\Faylin\Model\UserRepositoryInterface;


// Class that defines an authorization context for JWT (JSON Web Tokens)
final class JwtAuthorizationContext
{
  // The JWT configuration to use with the context
  private $configuration;

  // The user repository to use with the context
  private $userRepository;

  // The session repository to use with the context
  private $sessionRepository;


  // Constructor
  public function __construct(Configuration $configuration, UserRepositoryInterface $userRepository, SessionRepositoryInterface $sessionRepository)
  {
    $this->configuration = $configuration;
    $this->userRepository = $userRepository;
    $this->sessionRepository = $sessionRepository;
  }

  // Issue a token for a user
  public function issueFor(Snowflake $id, User $user): string
  {
    return $this->configuration->builder()
      ->identifiedBy($id->toString())
      ->relatedTo($user->getId()->toString())
      ->issuedAt(new \DateTimeImmutable())
      ->getToken($this->configuration->signer(), $this->configuration->signingKey())
      ->toString();
  }

  // Validate a token for a user
  public function validateFor(string $token, User $user): void
  {
    $token = $this->configuration->parser()->parse($token);
    $constraints = [new RelatedTo($user->getId()), new LooseValidAt(SystemClock::fromUTC())];

    try
    {
      $this->configuration->validator()->assert($token, ...$constraints);
    }
    catch (RequiredConstraintsViolated $ex)
    {
      $message = implode(', ', array_map(fn($violation) => $violation->getMessage(), $ex->violations()));
      throw new AuthorizationException("The token is invalid: {$message}");
    }
  }

  // Validate a token and return the user it was validated for
  public function validate(string $token): User
  {
    $parsedToken = $this->configuration->parser()->parse($token);

    $users = $this->userRepository->findManyBy();
    $usersThatMatch = array_filter($users, fn($user) => $parsedToken->isRelatedTo($user->getId()->toString()));
    if (empty($usersThatMatch))
      throw new AuthorizationException("The token is invalid: No user found for the token");

    $user = reset($usersThatMatch);

    $session = $this->sessionRepository->findBy(['accessToken' => $token]);
    if ($session === null)
      throw new AuthorizationException("The token is invalid: No session found for the token");
    if ($user->getId() != $session->getUser()->getId())
      throw new AuthorizationException("The token is invalid: The session user does not match the user specified in the token");

    $this->validateFor($token, $user);
    return $user;
  }
}
