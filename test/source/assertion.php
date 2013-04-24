<?php


  /**
   * Assertion
   *
   * @package net.evalcode.components
   * @subpackage test
   *
   * @since 1.0
   * @access public
   *
   * @author Carsten Schipke <carsten.schipke@evalcode.net>
   * @copyright Copyright (C) 2012 evalcode.net
   * @license GNU General Public License 3
   */
  interface Assertion
  {
    // ACCESSORS
    /**
     * @param mixed $value_
     */
    function assert($value_);
    //--------------------------------------------------------------------------
  }


  /**
   * ===========================================================================
   *
   * Assertions.
   *
   * @package net.evalcode.components
   * @subpackage test
   *
   * @since 1.0
   * @access public
   *
   * @author Carsten Schipke <carsten.schipke@evalcode.net>
   * @copyright Copyright (C) 2012 evalcode.net
   * @license GNU General Public License 3
   *
   * TODO Make use of ASSERT_CALLBACK and built-in assert().
   *
   * ---------------------------------------------------------------------------
   */


  /**
   * @param mixed $value_
   * @param int $size_
   *
   * @return boolean|false If given parameter is not of type array.
   */
  function assertArray($value_)
  {
    $message=Assertion_Helper::getMessage(
      'Expected an array', __FUNCTION__, func_get_args()
    );

    $result=is_array($value_);

    Assertion_Context::current()->add(__FUNCTION__, $result, $message);

    return $result;
  }


  /**
   * @param mixed $value_
   * @param int $size_
   *
   * @return boolean|false If given parameter is not of type array
   *                       and given size.
   */
  function assertArraySize($value_, $size_)
  {
    $message=Assertion_Helper::getMessage(
      'Expected an array of size', __FUNCTION__, func_get_args()
    );

    if(false===is_array($value_) || count($value_)!=(int)$size_)
    {
      Assertion_Context::current()->add(__FUNCTION__, false, $message);

      return false;
    }

    Assertion_Context::current()->add(__FUNCTION__, true, $message);

    return true;
  }


  /**
   * @param mixed $value_
   * @param int $size_
   *
   * @return boolean|false If given parameter is not of type array
   *                       or less or equal than given size.
   */
  function assertArraySizeGreaterThan($value_, $size_)
  {
    $message=Assertion_Helper::getMessage(
      'Expected an array of size greater than', __FUNCTION__, func_get_args()
    );

    if(false===is_array($value_) || count($value_)<=(int)$size_)
    {
      Assertion_Context::current()->add(__FUNCTION__, false, $message);

      return false;
    }

    Assertion_Context::current()->add(__FUNCTION__, true, $message);

    return true;
  }


  /**
   * @param mixed $value_
   * @param int $size_
   *
   * @return boolean|false If given parameter is not of type array
   *                       or greater or equal than given size.
   */
  function assertArraySizeLessThan($value_, $size_)
  {
    $message=Assertion_Helper::getMessage(
      'Expected an array of size less than', __FUNCTION__, func_get_args()
    );

    if(false===is_array($value_) || count($value_)>=(int)$size_)
    {
      Assertion_Context::current()->add(__FUNCTION__, false, $message);

      return false;
    }

    Assertion_Context::current()->add(__FUNCTION__, true, $message);

    return true;
  }


  /**
   * @param mixed $value_
   * @param string $pattern_
   *
   * @return boolean|false If given parameter is not of type array or does not
   *                       contain given pattern in its keys/values.
   */
  function assertArrayContains($value_, $pattern_)
  {
    $message=Assertion_Helper::getMessage(
      'Expected an array containing a key/value matching given pattern',
      __FUNCTION__, func_get_args()
    );

    if(false===is_array($value_)
      || (false===Arrays::containsKeyBySubstring($value_, $pattern_, Arrays::RECURSIVE)
      && false===Arrays::containsValueBySubstring($value_, $pattern_, Arrays::RECURSIVE)))
    {
      Assertion_Context::current()->add(__FUNCTION__, false, $message);

      return false;
    }

    Assertion_Context::current()->add(__FUNCTION__, true, $message);

    return true;
  }


  /**
   * @param mixed $value_
   * @param string $pattern_
   *
   * @return boolean|false If given parameter is not of type array
   *                       or contains given pattern in its keys/values.
   */
  function assertNotArrayContains($value_, $pattern_)
  {
    $message=Assertion_Helper::getMessage(
      'Expected an array not containing a key/value matching given pattern',
      __FUNCTION__, func_get_args()
    );

    if(false===is_array($value_)
      || false!==Arrays::containsKeyBySubstring($value_, $pattern_, Arrays::RECURSIVE)
      || false!==Arrays::containsValueBySubstring($value_, $pattern_, Arrays::RECURSIVE))
    {
      Assertion_Context::current()->add(__FUNCTION__, false, $message);

      return false;
    }

    Assertion_Context::current()->add(__FUNCTION__, true, $message);

    return true;
  }


  /**
   * @param mixed $value_
   * @param string $key_
   *
   * @return boolean|false If given parameter is not of type array
   *                       or does not contain given key.
   */
  function assertArrayContainsKey($value_, $key_)
  {
    $message=Assertion_Helper::getMessage(
      'Expected an array containing given key', __FUNCTION__, func_get_args()
    );

    if(false===is_array($value_)
      || false===Arrays::containsKey($value_, $key_, Arrays::RECURSIVE))
    {
      Assertion_Context::current()->add(__FUNCTION__, false, $message);

      return false;
    }

    Assertion_Context::current()->add(__FUNCTION__, true, $message);

    return true;
  }


  /**
   * @param mixed $value_
   * @param string $key_
   *
   * @return boolean|false If given parameter is not of type array
   *                       or contains given key.
   */
  function assertNotArrayContainsKey($value_, $key_)
  {
    $message=Assertion_Helper::getMessage(
      'Expected an array not containing given key', __FUNCTION__, func_get_args()
    );

    if(false===is_array($value_)
      || false!==Arrays::containsKey($value_, $key_, Arrays::RECURSIVE))
    {
      Assertion_Context::current()->add(__FUNCTION__, false, $message);

      return false;
    }

    Assertion_Context::current()->add(__FUNCTION__, true, $message);

    return true;
  }


  /**
   * @param mixed $value_
   *
   * @return boolean|false If given parameter is not of type boolean.
   */
  function assertBoolean($value_)
  {
    $message=Assertion_Helper::getMessage(
      'Expected a boolean value', __FUNCTION__, func_get_args()
    );

    $result=is_bool($value_);

    Assertion_Context::current()->add(__FUNCTION__, $result, $message);

    return $result;
  }


  /**
   * @param mixed $value_
   *
   * @return boolean|false If given parameter is of type boolean.
   */
  function assertNotBoolean($value_)
  {
    $message=Assertion_Helper::getMessage(
      'Expected a value of another type than boolean', __FUNCTION__, func_get_args()
    );

    $result=!(false===$value_ || true===$value_);

    Assertion_Context::current()->add(__FUNCTION__, $result, $message);

    return $result;
  }


  /**
   * @param mixed $value_
   * @param string $pattern_
   *
   * @return boolean|false If given array/string does not contain given pattern.
   */
  function assertContains($value_, $pattern_)
  {
    $message=Assertion_Helper::getMessage(
      'Expected any type containing a key/value matching given pattern', __FUNCTION__, func_get_args()
    );

    if(is_array($value_))
    {
      if(false===Arrays::containsKeyBySubstring($value_, $pattern_, Arrays::RECURSIVE)
        && false===Arrays::containsValueBySubstring($value_, $pattern_, Arrays::RECURSIVE))
      {
        Assertion_Context::current()->add(__FUNCTION__, false, $message);

        return false;
      }
    }
    else
    {
      if(false===String::contains($value_, $pattern_))
      {
        Assertion_Context::current()->add(__FUNCTION__, false, $message);

        return false;
      }
    }

    Assertion_Context::current()->add(__FUNCTION__, true, $message);

    return true;
  }


  /**
   * @param mixed $value_
   * @param string $pattern_
   *
   * @return boolean|false If given array/string does contain given pattern.
   */
  function assertNotContains($value_, $pattern_)
  {
    $message=Assertion_Helper::getMessage(
      'Expected any type not containing a key/value matching given pattern', __FUNCTION__, func_get_args()
    );

    if(is_array($value_))
    {
      if(false!==Arrays::containsKeyBySubstring($value_, $pattern_, Arrays::RECURSIVE)
        || false!==Arrays::containsValueBySubstring($value_, $pattern_, Arrays::RECURSIVE))
      {
        Assertion_Context::current()->add(__FUNCTION__, false, $message);

        return false;
      }
    }
    else
    {
      if(String::contains($value_, $pattern_))
      {
        Assertion_Context::current()->add(__FUNCTION__, false, $message);

        return false;
      }
    }

    Assertion_Context::current()->add(__FUNCTION__, true, $message);

    return true;
  }


  /**
   * @param mixed $value_
   *
   * @return boolean|false If given parameter is not of type boolean
   *                       and holding the value 'true'.
   */
  function assertTrue($value_)
  {
    $message=Assertion_Helper::getMessage(
      'Expected value of boolean \'true\'', __FUNCTION__, func_get_args()
    );

    $result=true===$value_;

    Assertion_Context::current()->add(__FUNCTION__, $result, $message);

    return $result;
  }


  /**
   * @param mixed $value_
   *
   * @return boolean|false If given parameter of type boolean
   *                       and holding the value 'true'.
   */
  function assertNotTrue($value_)
  {
    $message=Assertion_Helper::getMessage(
      'Expected another value than boolean \'true\'', __FUNCTION__, func_get_args()
    );

    $result=!true===$value_;

    Assertion_Context::current()->add(__FUNCTION__, $result, $message);

    return $result;
  }


  /**
   * @param mixed $value_
   *
   * @return boolean|false If given parameter is not of type
   *                       boolean and holding the value 'false'.
   */
  function assertFalse($value_)
  {
    $message=Assertion_Helper::getMessage(
      'Expected value of boolean \'false\'', __FUNCTION__, func_get_args()
    );

    $result=false===$value_;

    Assertion_Context::current()->add(__FUNCTION__, $result, $message);

    return $result;
  }


  /**
   * @param mixed $value_
   *
   * @return boolean|false If given parameter is of type boolean
   *                       and holding the value 'false'.
   */
  function assertNotFalse($value_)
  {
    $message=Assertion_Helper::getMessage(
      'Expected another value than boolean \'false\'', __FUNCTION__, func_get_args()
    );

    $result=false!==$value_;

    Assertion_Context::current()->add(__FUNCTION__, $result, $message);

    return $result;
  }


  /**
   * @param mixed $value_
   *
   * @return boolean|false If given parameter is not of type integer.
   */
  function assertInteger($value_)
  {
    $message=Assertion_Helper::getMessage(
      'Expected value of type integer', __FUNCTION__, func_get_args()
    );

    if(0!==$value_ && 0==(int)$value_)
    {
      Assertion_Context::current()->add(__FUNCTION__, false, $message);

      return false;
    }

    Assertion_Context::current()->add(__FUNCTION__, true, $message);

    return true;
  }


  /**
   * @param mixed $value_
   *
   * @return boolean|false If given parameter is of type integer.
   */
  function assertNotInteger($value_)
  {
    $message=Assertion_Helper::getMessage(
      'Expected value of another type than integer', __FUNCTION__, func_get_args()
    );

    if(0===$value_ || O<(int)$value_ || 0>(int)$value_)
    {
      Assertion_Context::current()->add(__FUNCTION__, false, $message);

      return false;
    }

    Assertion_Context::current()->add(__FUNCTION__, true, $message);

    return true;
  }


  /**
   * @param mixed $value_
   *
   * @return boolean|false If given parameter is not equal to 'null'.
   */
  function assertNull($value_)
  {
    $message=Assertion_Helper::getMessage(
      'Expected \'null\'', __FUNCTION__, func_get_args()
    );

    $result=null===$value_;

    Assertion_Context::current()->add(__FUNCTION__, $result, $message);

    return $result;
  }


  /**
   * @param mixed $value_
   *
   * @return boolean|false If given parameter is equal to 'null'.
   */
  function assertNotNull($value_)
  {
    $message=Assertion_Helper::getMessage(
      'Expected another value than \'null\'', __FUNCTION__, func_get_args()
    );

    $result=null!==$value_;

    Assertion_Context::current()->add(__FUNCTION__, $result, $message);

    return $result;
  }


  /**
   * @param mixed $value_
   *
   * @return boolean|false If given parameter has a value.
   */
  function assertEmpty($value_)
  {
    $message=Assertion_Helper::getMessage(
      'Expected an empty value', __FUNCTION__, func_get_args()
    );

    if(false===$value_ || $value_ || 0!=(int)$value_ || (is_array($value_) && 0<count($value_)))
    {
      Assertion_Context::current()->add(__FUNCTION__, false, $message);

      return false;
    }

    Assertion_Context::current()->add(__FUNCTION__, true, $message);

    return true;
  }


  /**
   * @param mixed $value_
   *
   * @return boolean|false If given parameter has no value.
   */
  function assertNotEmpty($value_)
  {
    $message=Assertion_Helper::getMessage(
      'Expected any value', __FUNCTION__, func_get_args()
    );

    if((0===$value_ // zero is empty
      || (is_string($value_) && 1>String::length($value_)) // zero-character string is empty
      || (is_array($value_) && 1>count($value_))) // zero-element array is empty
      && false!==$value_) // boolean 'false' is not empty
    {
      Assertion_Context::current()->add(__FUNCTION__, false, $message);

      return false;
    }

    Assertion_Context::current()->add(__FUNCTION__, true, $message);

    return true;
  }


  /**
   * @param mixed $value_
   *
   * @return boolean|false If given parameter is not of type string
   *                       or has no visible characters.
   */
  function assertVisibleString($value_)
  {
    $message=Assertion_Helper::getMessage(
      'Expected any value of type string containing visible characters', __FUNCTION__, func_get_args()
    );

    if(false===is_string($value_) || !trim($value_))
    {
      Assertion_Context::current()->add(__FUNCTION__, false, $message);

      return false;
    }

    Assertion_Context::current()->add(__FUNCTION__, true, $message);

    return true;
  }


  /**
   * @param mixed $value_
   *
   * @return boolean|false If given parameter is not of type string
   *                       or has visible characters.
   */
  function assertNotVisibleString($value_)
  {
    $message=Assertion_Helper::getMessage(
      'Expected any value of type string not containing visible characters', __FUNCTION__, func_get_args()
    );

    if(false===is_string($value_) || trim($value_))
    {
      Assertion_Context::current()->add(__FUNCTION__, false, $message);

      return false;
    }

    Assertion_Context::current()->add(__FUNCTION__, true, $message);

    return true;
  }


  /**
   * @param mixed $value_
   *
   * @return boolean|false If given parameter is not of a numeric type.
   */
  function assertNumeric($value_)
  {
    $message=Assertion_Helper::getMessage(
      'Expected value of any numeric type', __FUNCTION__, func_get_args()
    );

    $result=is_numeric($value_);

    Assertion_Context::current()->add(__FUNCTION__, $result, $message);

    return $result;
  }


  /**
   * @param mixed $value_
   *
   * @return boolean|false If given parameter is not of a numeric type.
   */
  function assertNotNumeric($value_)
  {
    $message=Assertion_Helper::getMessage(
      'Expected value of any other type than numeric ones', __FUNCTION__, func_get_args()
    );

    $result=false===is_numeric($value_);

    Assertion_Context::current()->add(__FUNCTION__, $result, $message);

    return $result;
  }


  /**
   * @param mixed $value_
   *
   * @return boolean|false If given parameter is not an object.
   */
  function assertObject($value_)
  {
    $message=Assertion_Helper::getMessage(
      'Expected object', __FUNCTION__, func_get_args()
    );

    $result=is_object($value_);

    Assertion_Context::current()->add(__FUNCTION__, $result, $message);

    return $result;
  }


  /**
   * @param mixed $value_
   *
   * @return boolean|false If given parameter is an object
   */
  function assertNotObject($value_)
  {
    $message=Assertion_Helper::getMessage(
      'Expected value of any other type than object', __FUNCTION__, func_get_args()
    );

    $result=false===is_object($value_);

    Assertion_Context::current()->add(__FUNCTION__, $result, $message);

    return $result;
  }


  /**
   * @param mixed $value_
   *
   * @return boolean|false If given parameter is not of a scalar type.
   */
  function assertScalar($value_)
  {
    $message=Assertion_Helper::getMessage(
      'Expected value of a scalar type', __FUNCTION__, func_get_args()
    );

    $result=is_scalar($value_);

    Assertion_Context::current()->add(__FUNCTION__, $result, $message);

    return $result;
  }


  /**
   * @param mixed $value_
   *
   * @return boolean|false If given parameter is of a scalar type.
   */
  function assertNotScalar($value_)
  {
    $message=Assertion_Helper::getMessage(
      'Expected value of any other type than scalar ones', __FUNCTION__, func_get_args()
    );

    $result=false===is_scalar($value_);

    Assertion_Context::current()->add(__FUNCTION__, $result, $message);

    return $result;
  }


  /**
   * @param mixed $value_
   *
   * @return boolean|false If given parameter is not of type string.
   */
  function assertString($value_)
  {
    $message=Assertion_Helper::getMessage(
      'Expected value of type string', __FUNCTION__, func_get_args()
    );

    $result=is_string($value_);

    Assertion_Context::current()->add(__FUNCTION__, $result, $message);

    return $result;
  }


  /**
   * @param mixed $value_
   *
   * @return boolean|false If given parameter is of type string.
   */
  function assertNotString($value_)
  {
    $message=Assertion_Helper::getMessage(
      'Expected value of any other type than string', __FUNCTION__, func_get_args()
    );

    $result=false===is_string($value_);

    Assertion_Context::current()->add(__FUNCTION__, $result, $message);

    return $result;
  }


  /**
   * @param mixed $expectedValue_
   * @param mixed $actualValue_
   *
   * @return boolean|false If given parameters' values are not equal.
   */
  function assertEquals($expectedValue_, $actualValue_)
  {
    $message=Assertion_Helper::getMessage(
      'Expected two equal values', __FUNCTION__, func_get_args()
    );

    $result=$expectedValue_==$actualValue_;

    Assertion_Context::current()->add(__FUNCTION__, $result, $message);

    return $result;
  }


  /**
   * @param mixed $expectedValue_
   * @param mixed $actualValue_
   *
   * @return boolean|false If given parameters' values are equal.
   */
  function assertNotEquals($expectedValue_, $actualValue_)
  {
    $message=Assertion_Helper::getMessage(
      'Expected two different values', __FUNCTION__, func_get_args()
    );

    $result=$expectedValue_!=$actualValue_;

    Assertion_Context::current()->add(__FUNCTION__, $result, $message);

    return $result;
  }


  /**
   * Alias for assertEquals.
   *
   * @see assertEquals
   */
  function assertEqual($expectedValue_, $actualValue_)
  {
    $message=Assertion_Helper::getMessage(
      'Expected two equal values', __FUNCTION__, func_get_args()
    );

    $result=$expectedValue_==$actualValue_;

    Assertion_Context::current()->add(__FUNCTION__, $result, $message);

    return $result;
  }


  /**
   * Alias for assertNotEquals.
   *
   * @see assertNotEquals
   */
  function assertNotEqual($expectedValue_, $actualValue_)
  {
    $message=Assertion_Helper::getMessage(
      'Expected two different values', __FUNCTION__, func_get_args()
    );

    $result=$expectedValue_!=$actualValue_;

    Assertion_Context::current()->add(__FUNCTION__, $result, $message);

    return $result;
  }


  /**
   * @param mixed $expectedValue_
   * @param mixed $actualValue_
   *
   * @return boolean|false If given parameters' types & values
   *                       are not exactly the same.
   */
  function assertSame($expectedValue_, $actualValue_)
  {
    $message=Assertion_Helper::getMessage(
      'Expected that given values are the same', __FUNCTION__, func_get_args()
    );

    $result=$expectedValue_===$actualValue_;

    Assertion_Context::current()->add(__FUNCTION__, $result, $message);

    return $result;
  }


  /**
   * @param mixed $expectedValue_
   * @param mixed $actualValue_
   *
   * @return boolean|false If given parameters' types & values
   *                       are exactly the same.
   */
  function assertNotSame($expectedValue_, $actualValue_)
  {
    $message=Assertion_Helper::getMessage(
      'Expected that given values are not the same', __FUNCTION__, func_get_args()
    );

    $result=$expectedValue_!==$actualValue_;

    Assertion_Context::current()->add(__FUNCTION__, $result, $message);

    return $result;
  }


  /**
   * @param mixed $assertionValue_
   * @param mixed $actualValue_
   *
   * @return boolean|false If second parameter's value is not greater
   *                       than first parameter's value.
   */
  function assertGreaterThan($assertionValue_, $actualValue_)
  {
    $message=Assertion_Helper::getMessage(
      'Expected that second parameter\'s value is greater than first one\'s',
      __FUNCTION__, func_get_args()
    );

    $result=$assertionValue_<$actualValue_;

    Assertion_Context::current()->add(__FUNCTION__, $result, $message);

    return $result;
  }


  /**
   * @param mixed $assertionValue_
   * @param mixed $actualValue_
   *
   * @return boolean|false If second parameter's value is not less
   *                       than first parameter's value.
   */
  function assertLessThan($assertionValue_, $actualValue_)
  {
    $message=Assertion_Helper::getMessage(
      'Expected that second parameter\'s value is less than first one\'s',
      __FUNCTION__, func_get_args()
    );

    $result=$assertionValue_>$actualValue_;

    Assertion_Context::current()->add(__FUNCTION__, $result, $message);

    return $result;
  }


  /**
   * @param string $value_
   * @param string $pattern_
   *
   * @return boolean|false If given value does not match given pattern.
   */
  function assertMatches($value_, $pattern_)
  {
    $message=Assertion_Helper::getMessage(
      'Expected value that matches given pattern', __FUNCTION__, func_get_args()
    );

    $matches=array();
    $result=preg_match($pattern_, $value_, $matches);
    $result=0<(int)$result;

    Assertion_Context::current()->add(__FUNCTION__, $result, $message);

    return $result;
  }


  /**
   * @param string $value_
   * @param string $pattern_
   *
   * @return boolean|false If given value does match given pattern.
   */
  function assertNotMatches($value_, $pattern_)
  {
    $message=Assertion_Helper::getMessage(
      'Expected value that not matches given pattern', __FUNCTION__, func_get_args()
    );

    $matches=array();
    $result=preg_match($pattern_, $value_, $matches);
    $result=1>(int)$result;

    Assertion_Context::current()->add(__FUNCTION__, $result, $message);

    return $result;
  }
?>
