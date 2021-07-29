<?php
namespace Danae\Faylin\Validator\Rules;

use Danae\Faylin\Validator\Rule;


// Validator rule that validates if the input is not lower than a maximum value
final class MinRule extends Rule
{
  // The minimal value of the input
  private $min;


  // Constructor
  public function __construct(int $min)
  {
    $this->min = $min;
  }

  // Validate this rule
  public function validate(&$input): bool
  {
    return is_int($input) && $input >= $this->min;
  }

  // Return the default error message for this rule
  public function message(): string
  {
    return "Field \"{{field}}\" is lower than the allowed minimal value, minimal value is {$this->min}";
  }
}
