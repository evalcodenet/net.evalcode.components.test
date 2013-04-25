<?php


namespace Components;


  /**
   * Mock_Stub
   *
   * @package net.evalcode.components
   * @subpackage test.mock
   *
   * @since 1.0
   * @access public
   *
   * @author Carsten Schipke <carsten.schipke@evalcode.net>
   * @copyright Copyright (C) 2012 evalcode.net
   * @license GNU General Public License 3
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
