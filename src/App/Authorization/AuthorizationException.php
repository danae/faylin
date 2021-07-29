<?php
namespace Danae\Faylin\App\Authorization;


// Exception that is thrown when authorization fails
class AuthorizationException extends \Exception
{
  // Constructor
  public function __construct(string $message, int $code = 0, \Throwable $previous = null)
  {
    parent::__construct($message, $code, $previous);
  }
}
