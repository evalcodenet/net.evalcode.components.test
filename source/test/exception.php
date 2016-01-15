<?php


namespace Components;


  /**
   * Test_Exception
   *
   * @package net.evalcode.components.test
   *
   * @author evalcode.net
   */
  class Test_Exception extends Runtime_Exception_Abstract
  {
    // CONSTRUCTION
    public function __construct($namespace_='test/exception',
      $message_='Test exception.', \Exception $cause_=null, $logEnabled_=true)
    {
      parent::__construct($namespace_, $message_, $cause_, $logEnabled_);
    }
    //--------------------------------------------------------------------------
  }
?>
