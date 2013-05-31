<?php


namespace Components;


  /**
   * Assertion_Helper
   *
   * @package net.evalcode.components
   * @subpackage test.assertion
   *
   * @author evalcode.net
   */
  class Assertion_Helper
  {
    // STATIC ACCESSRS
    public static function getMessage($prefix_, $method_, array $args_)
    {
      $error=new Assertion_Error();
      $trace=$error->getTrace();

      // TODO Useful truncation/line-wrapping for file-/methodname & parameter values.

      $line=0;
      $file='L';
      if(isset($trace[1]))
      {
        $line=$trace[1]['line'];
        $file=substr($trace[1]['file'], strrpos($trace[1]['file'], '/')+1);
      }

      $args=array();
      foreach($args_ as $arg)
        $args[]=self::mixedToString($arg);

      return sprintf('%1$d %2$s(%3$s).', $line, $method_, implode(', ', $args));
    }

    public static function mixedToString($mixed_)
    {
      if(is_array($mixed_))
        return Arrays::toString($mixed_);

      if(is_object($mixed_) && false===method_exists($mixed_, '__toString'))
        return get_class($mixed_);

      return trim((string)$mixed_);
    }
    //--------------------------------------------------------------------------
  }
?>
