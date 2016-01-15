<?php


namespace Components;


  /**
   * Assertion_Error
   *
   * @api
   * @package net.evalcode.components.test
   * @subpackage assertion
   *
   * @author evalcode.net
   */
  class Assertion_Error extends Test_Error
  {
    // CONSTRUCTION
    public function __construct($namespace_='test/assertion/error',
      $message_='Assertion failed.', $code_=null, $filename_=null, $line_=0,
      $cause_=null, $logEnabled_=true)
    {
      parent::__construct(
        $namespace_, $message_, $code_, $filename_, $line_, $cause_, $logEnabled_
      );
    }
    //--------------------------------------------------------------------------
  }
?>
