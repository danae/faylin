<?php
namespace Danae\Faylin\App\Authorization;


// Exception that is thrown when authorization fails
class AuthorizationException extends \Exception
{
  // Constructor
  public function __construct(string $message, \Throwable $previous = null)
  {
    parent::__construct($message, 0, $previous);
  }
}
