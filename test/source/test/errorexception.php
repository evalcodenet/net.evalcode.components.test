<?php


namespace Components;


  /**
   * Test_ErrorException
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
  class Test_ErrorException extends Runtime_ErrorException
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
