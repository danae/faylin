<?php
namespace Danae\Faylin\Validator;


// Base class for validator rules
abstract class Rule
{
  // Validate this rule
  public abstract function validate(&$input): bool;

  // Return the default error message for this rule
  public abstract function message(): string;
}
