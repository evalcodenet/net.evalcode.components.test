<?php


namespace Components;


  /**
   * Test_Result_Handler
   *
   * @package net.evalcode.components
   * @subpackage test.result
   *
   * @since 1.0
   * @access public
   *
   * @author Carsten Schipke <carsten.schipke@evalcode.net>
   * @copyright Copyright (C) 2012 evalcode.net
   * @license GNU General Public License 3
   */
  interface Test_Result_Handler
  {
    // ACCESSORS
    /**
     * Result modification callback.
     *
     * @param Test_Result $result_
     */
    function handleResult(Test_Result $result_);
    //--------------------------------------------------------------------------
  }
?>
