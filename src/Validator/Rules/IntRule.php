<?php
namespace Danae\Faylin\Validator\Rules;

use Danae\Faylin\Validator\Rule;


// Validator rule that validates if the key is an integer
final class IntRule extends Rule
{
  // Indicates if a string should be converted to an int before validating
  private $convertToInt;


  // Constructor
  public function __construct(bool $convertToInt = false)
  {
    $this->convertToInt = $convertToInt;
  }

  // Validate this rule
  public function validate(&$input): bool
  {
    if ($this->convertToInt && is_string($input) && preg_match('/0|[1-9][0-9]*/', $input))
      $input = intval($input);

    return is_int($input);
  }

  // Return the default error message for this rule
  public function message(): string
  {
    return "Field \"{{field}}\" is not a valid integer";
  }
}
