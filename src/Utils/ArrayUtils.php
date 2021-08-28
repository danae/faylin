<?php
namespace Danae\Faylin\Utils;


// Class that defines utility methods for arrays
final class ArrayUtils
{
  // Find the first value in an array that matches a predicate
  public static function find(array $array, callable $predicate)
  {
    foreach ($array as $v)
    {
      if (call_user_func($predicate, $v) === true)
        return $v;
    }

    return null;
  }

  // Return if any element in the array matches a predicate
  public static function any(array $array, callable $predicate)
  {
    foreach ($array as $v)
    {
      if (call_user_func($predicate, $v) === true)
        return true;
    }

    return false;
  }

  // Return if all elements in the array match a predicate
  public static function all(array $array, callable $predicate)
  {
    foreach ($array as $v)
    {
      if (call_user_func($predicate, $v) === false)
        return false;
    }

    return true;
  }
}
