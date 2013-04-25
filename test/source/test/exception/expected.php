<?php


namespace Components;


  /**
   * Test_Exception_Expected
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
