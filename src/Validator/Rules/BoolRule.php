<?php
namespace Danae\Faylin\Validator\Rules;

use Danae\Faylin\Validator\Rule;


// Validator rule that validates if the input is a bool
final class BoolRule extends Rule
{
  // Indicates if a string should be converted to a bool before validating
  private $convertToBool;


  // Constructor
  public function __construct(bool $convertToBool = false)
  {
    $this->convertToBool = $convertToBool;
  }

  // Validate this rule
  public function validate(&$input): bool
  {
    if ($this->convertToBool && is_string($input))
    {
      if ($input === "false" || $input === "0")
        $input = false;
      else if ($input === "true" || $input === "1")
        $input = true;
    }

    return is_bool($input);
  }

  // Return the default error message for this rule
  public function message(): string
  {
    return "Field \"{{field}}\" is not a valid bool";
  }
}
