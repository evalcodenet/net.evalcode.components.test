<?php


  /**
   * Test_Exception_Failed
   *
   * @package net.evalcode.components
   * @subpackage test.exception
   *
   * @since 1.0
   * @access public
   *
   * @author Carsten Schipke <carsten.schipke@evalcode.net>
   * @copyright Copyright (C) 2012 evalcode.net
   * @license GNU General Public License 3
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
