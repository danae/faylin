<?php
namespace Danae\Faylin\Validator\Rules;

use Danae\Faylin\Validator\Rule;


// Validator rule that validates if the input is not higher than a maximum value
final class MaxRule extends Rule
{
  // The maximal value of the input
  private $max;


  // Constructor
  public function __construct(int $max)
  {
    $this->max = $max;
  }

  // Validate this rule
  public function validate(&$input): bool
  {
    return is_int($input) && $input <= $this->max;
  }

  // Return the default error message for this rule
  public function message(): string
  {
    return "Field \"{{field}}\" is higher than the allowed maximal value, maximal value is {$this->max}";
  }
}
