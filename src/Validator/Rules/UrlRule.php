<?php
namespace Danae\Faylin\Validator\Rules;

use Danae\Faylin\Validator\Rule;


// Validator rule that validates if the key is a string
final class UrlRule extends Rule
{
  // The schemes to match in the input
  private $schemes;


  // Constructor
  public function __construct(string ...$schemes)
  {
    $this->schemes = $schemes;
  }

  // Validate this rule
  public function validate(&$input): bool
  {
    if (!is_string($input))
      return false;
    elseif (filter_var($input, \FILTER_VALIDATE_URL) === false)
      return false;
    elseif (preg_match("/^(?:" . implode("|", $this->schemes) . "):/", $input) === false)
      return false;
    else
      return true;
  }

  // Return the default error message for this rule
  public function message(): string
  {
    if (empty($this->schemes))
      return "Field \"{{field}}\" is not a valid URL";
    else
      return "Field \"{{field}}\" is not a valid URL or does not contain a valid scheme, valid schemes are " . implode(", ", $this->schemes);
  }
}
