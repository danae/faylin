<?php
namespace Danae\Faylin\Validator\Rules;

use Danae\Faylin\Validator\Rule;


// Validator rule that validates if the input is a string and a valid email address
final class EmailRule extends Rule
{
  // Validate this rule
  public function validate(&$input): bool
  {
    if (!is_string($input))
      return false;
    elseif (filter_var($input, \FILTER_VALIDATE_EMAIL) === false)
      return false;
    else
      return true;
  }

  // Return the default error message for this rule
  public function message(): string
  {
    return "Field \"{{field}}\" is not a valid email address";
  }
}
