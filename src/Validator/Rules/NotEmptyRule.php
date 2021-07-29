<?php
namespace Danae\Faylin\Validator\Rules;

use Danae\Faylin\Validator\Rule;


// Validator rule that validates if the input is not empty
final class NotEmptyRule extends Rule
{
  // Validate this rule
  public function validate(&$input): bool
  {
    return !empty($input);
  }

  // Return the default error message for this rule
  public function message(): string
  {
    return "Field \"{{field}}\" is empty";
  }
}
