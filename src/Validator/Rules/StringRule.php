<?php
namespace Danae\Faylin\Validator\Rules;

use Danae\Faylin\Validator\Rule;


// Validator rule that validates if the key is a string
final class StringRule extends Rule
{
  // Validate this rule
  public function validate(&$input): bool
  {
    return is_string($input);
  }

  // Return the default error message for this rule
  public function message(): string
  {
    return "Field \"{{field}}\" is not a valid string";
  }
}
