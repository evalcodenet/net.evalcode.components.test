<?php


namespace Components;


  /**
   * Mock_Stub
   *
   * @package net.evalcode.components.test
   * @subpackage mock
   *
   * @author evalcode.net
   */
  class Mock_Stub
  {
      // PROPERTIES
    public $answer;
    public $value;
    public $exceptionClazz;
    //--------------------------------------------------------------------------


    // ACCESSORS
    public function call()
    {
      if($this->exceptionClazz)
        throw new $this->exceptionClazz();

      if($this->value)
        return $this->value;
    }

    public function doReturn($value_)
    {
      $this->value=$value_;
    }

    public function doThrow($exceptionClazz_)
    {
      $this->exceptionClazz=$exceptionClazz_;
    }

    public function doAnswer(\Closure $answer_)
    {
      $this->answer=$answer_;
    }

    public function doNothing()
    {
      $this->value=$this->exceptionClazz=null;
    }
    //--------------------------------------------------------------------------
  }
?>
