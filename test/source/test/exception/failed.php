<?php


namespace Components;


  /**
   * Test_Exception_Failed
   *
   * @package net.evalcode.components
   * @subpackage test.exception
   *
   * @author evalcode.net
   */
  class Test_Exception_Failed extends Test_Exception
  {
    // CONSTRUCTION
    public function __construct($namespace_='test/exception/failed',
      $message_='Test failed.', \Exception $cause_=null, $logEnabled_=true)
    {
      parent::__construct($namespace_, $message_, $cause_, $logEnabled_);
    }
    //--------------------------------------------------------------------------
  }
?>
