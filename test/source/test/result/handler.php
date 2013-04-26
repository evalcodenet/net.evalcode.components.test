<?php


namespace Components;


  /**
   * Test_Result_Handler
   *
   * @package net.evalcode.components
   * @subpackage test.result
   *
   * @author evalcode.net
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
