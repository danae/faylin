<?php
namespace Danae\Faylin\Validator\Rules;

use Danae\Faylin\Validator\Rule;


// Validator rule that validates if the input is of a maximum length
final class MaxLengthRule extends Rule
{
  // The maximum length of the input
  private $maxLength;


  // Constructor
  public function __construct(int $maxLength)
  {
    $this->maxLength = $maxLength;
  }

  // Validate this rule
  public function validate(&$input): bool
  {
    return is_string($input) && strlen($input) <= $this->maxLength;
  }

  // Return the default error message for this rule
  public function message(): string
  {
    return "Field \"{{field}}\" is longer than the allowed length, maximal length is {$this->maxLength} characters";
  }
}
