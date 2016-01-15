<?php


namespace Components;


  /**
   * Test_Error
   *
   * @package net.evalcode.components.test
   *
   * @author evalcode.net
   */
  class Test_Error extends Runtime_Error_Abstract
  {
    // CONSTRUCTION
    public function __construct($namespace_='test/error',
      $message_='Test error.', $code_=null, $filename_=null, $line_=0,
      $cause_=null, $logEnabled_=true)
    {
      parent::__construct(
        $namespace_, $message_, $code_, $filename_, $line_, $cause_, $logEnabled_
      );
    }
    //--------------------------------------------------------------------------
  }
?>
