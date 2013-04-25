<?php


namespace Components;


  /**
   * Assertion_Error
   *
   * @package net.evalcode.components
   * @subpackage test.assertion
   *
   * @since 1.0
   * @access public
   *
   * @author Carsten Schipke <carsten.schipke@evalcode.net>
   * @copyright Copyright (C) 2012 evalcode.net
   * @license GNU General Public License 3
   */
  class Assertion_Error extends Test_ErrorException
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
