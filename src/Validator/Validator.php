<?php
namespace Danae\Faylin\Validator;

use Symfony\Component\PropertyAccess\PropertyAccess;


// Class that sanitizes input
final class Validator
{
  // The defined rules of this validator
  private $definedRules;

  // The rules of this validator
  private $rules;


  // Constructor
  public function __construct()
  {
    $this->definedRules = [];
    $this->rules = [];

    // Define the standard rules
    $this->defineRule('bool', Rules\BoolRule::class);
    $this->defineRule('email', Rules\EmailRule::class);
    $this->defineRule('float', Rules\FloatRule::class);
    $this->defineRule('int', Rules\IntRule::class);
    $this->defineRule('max', Rules\MaxRule::class);
    $this->defineRule('maxlength', Rules\MaxLengthRule::class);
    $this->defineRule('min', Rules\MinRule::class);
    $this->defineRule('notempty', Rules\NotEmptyRule::class);
    $this->defineRule('regex', Rules\RegexRule::class);
    $this->defineRule('string', Rules\StringRule::class);
    $this->defineRule('url', Rules\UrlRule::class);
  }

  // Add a rule definition
  public function defineRule(string $name, $type)
  {
    $this->definedRules[$name] = $type;
  }

  // Add a required property
  public function withRequired(string $key, $rules, array $options = []): self
  {
    $this->rules[$key] = ['rules' => $this->resolveRules($rules), 'options' => $options];
    return $this;
  }

  // Add an optional property
  public function withOptional(string $key, $rules, $default = null, array $options = []): self
  {
    $this->rules[$key] = ['rules' => $this->resolveRules($rules), 'default' => $default, 'options' => $options];
    return $this;
  }

  // Validate an input array
  public function validate($array, array $options = []): ValidatorResult
  {
    $keys = [];
    $errors = [];

    // Set the default options
    $options['allowExtraFields'] ??= false;

    // Check if the array is an array
    if (!is_array($array))
      return new ValidatorResult([], ["Validation data is not an array"]);

    // Iterate over the keys
    foreach ($this->rules as $key => $keyOptions)
    {
      // Add the key to the keys array
      $keys[] = $key;

      // Check if the key exists in the array
      if (!array_key_exists($key, $array))
      {
        // Check if the key is required
        if (array_key_exists('default', $keyOptions))
          $array[$key] = $keyOptions['default'];
        else
          $errors[] = "Field \"{$key}\" is required";
      }
      else
      {
        // Get the value from the array
        $value = $array[$key];

        // Iterate over the rules
        foreach($keyOptions['rules'] as $rule)
        {
          // Check if the rule is valid
          if (!$rule->validate($value))
            $errors[] = str_replace("{{field}}", $key, $rule->message());
        }
      }
    }

    // Check for extra fields
    if (!$options['allowExtraFields'])
    {
      $extraKeys = array_diff(array_keys($array), $keys);
      if (!empty($extraKeys))
        $errors[] = "Array contains extra fields " . implode(', ', array_map(fn($key) => "\"$key\"", $extraKeys));
    }

    // Return the result
    return new ValidatorResult($array, $errors);
  }

  // Resolve the specified rules
  private function resolveRules($rules): array
  {
    // Parse the rules
    $rules = $this->parseRules($rules);

    // Iterate over the rules
    $resolvedRules = [];
    foreach ($rules as $key => $params)
    {
      if (!array_key_exists($key, $this->definedRules))
        throw new \InvalidArgumentException("Rule \"{$key}\" is not defined in this validator");

      // Add the resolved rule
      if (empty($params))
        $resolvedRules[] = new $this->definedRules[$key]();
      else
        $resolvedRules[] = new $this->definedRules[$key](...$params);
    }

    // Return the resolved rules
    return $resolvedRules;
  }

  // Parse the specified rules
  private function parseRules($rules): array
  {
    // If the rules are a string, then split them into an array
    if (is_string($rules))
      $rules = explode('|', $rules);

    // Iterate over the rules
    $parsedRules = [];
    foreach ($rules as $key => $params)
    {
      // If the key is an integer, then split the params into an array
      if (is_int($key))
      {
        // Check if there are parameters
        if (strpos($params, ':') !== false)
          [$key, $params] = explode(':', $params, 2);
        else
          [$key, $params] = [$params, []];
      }

      // If the params are a string, then split and parse it
      if (is_string($params))
      {
        $params = explode(',', $params);

        // Iterate over the params
        foreach ($params as &$param)
        {
          if ($param === "null")
            $param = null;
          elseif ($param === "false")
            $param = false;
          elseif ($param === "true")
            $param = true;
          elseif (preg_match('/0|[1-9][0-9]*/', $param))
            $param = intval($param);
          elseif (preg_match('/(?:0|[1-9][0-9]*)\.(?:[0-9+])/', $param))
            $param = floatval($param);
        }
      }

      // Add the parsed rule
      $parsedRules[$key] = $params;
    }

    // Return the parsed rules
    return $parsedRules;
  }
}
