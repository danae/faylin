<?php
namespace Danae\Faylin\Validator\Rules;

use Danae\Faylin\Validator\Rule;


// Validator rule that validates if the key is a float
final class FloatRule extends Rule
{
  // Indicates if a string should be converted to a float before validating
  private $convertToFloat;


  // Constructor
  public function __construct(bool $convertToFloat = false)
  {
    $this->convertToFloat = $convertToFloat;
  }

  // Validate this rule
  public function validate(&$input): bool
  {
    if ($this->convertToFloat && is_string($input) && preg_match('/0|[1-9][0-9]*/', $input))
      $input = floatval($input);

    return is_float($input);
  }

  // Return the default error message for this rule
  public function message(): string
  {
    return "Field \"{{field}}\" is not a valid float";
  }
}
