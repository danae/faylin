<?php
namespace Danae\Faylin\Validator\Rules;

use Danae\Faylin\Validator\Rule;


// Validator rule that validates if the input is a stringmatches a regex pattern
final class RegexRule extends Rule
{
  // The pattern to match in the input
  private $pattern;


  // Constructor
  public function __construct(string $pattern)
  {
    $this->pattern = $pattern;
  }

  // Validate this rule
  public function validate(&$input): bool
  {
    if (!is_string($input))
      return false;
    elseif (preg_match($this->pattern, $input) === false)
      return false;
    else
      return true;
  }

  // Return the default error message for this rule
  public function message(): string
  {
    return "Field \"{{field}}\" does not match the pattern \"{$this->pattern}\"";
  }
}
