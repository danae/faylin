<?php
namespace Danae\Faylin\Validator;

use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;


// Class that defines the result of a validator
final class ValidatorResult
{
  // The result array after validation
  private $result;

  // The errors that occurred during validation
  private $errors;


  // Constructor
  public function __construct(array $result, array $errors)
  {
    $this->result = $result;
    $this->errors = $errors;
  }

  // Return the result
  public function getResult(): array
  {
    return $this->result;
  }

  // Return the errors
  public function getErrors(): array
  {
    return $this->errors;
  }

  // Return if the errors array is not empty
  public function hasErrors(): bool
  {
    return !empty($this->errors);
  }

  // Return the result or throw an exception if there are errors
  public function resultOrThrowBadRequest(Request $request): array
  {
    if ($this->hasErrors())
      throw new HttpBadRequestException($request, implode('; ', $this->errors));
    return $this->getResult();
  }
}
