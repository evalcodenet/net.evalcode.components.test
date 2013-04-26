<?php


namespace Components;


  /**
   * Test_Exception_Expected
   *
   * @package net.evalcode.components
   * @subpackage test.exception
   *
   * @author evalcode.net
   */
  class Test_Exception_Expected extends Test_Exception
  {
    // CONSTRUCTION
    public function __construct($namespace_='test/exception/expected',
      $message_='Expected exception.', \Exception $cause_=null, $logEnabled_=true)
    {
      parent::__construct($namespace_, $message_, $cause_, $logEnabled_);
    }
    //--------------------------------------------------------------------------
  }
?>
