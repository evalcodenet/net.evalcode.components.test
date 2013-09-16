<?php


namespace Components;


  /**
   * Test_Result_Handler
   *
   * @package net.evalcode.components.test
   * @subpackage result
   *
   * @author evalcode.net
   */
  interface Test_Result_Handler
  {
    // ACCESSORS
    /**
     * Result modification callback.
     *
     * @param \Components\Test_Result $result_
     */
    function handleResult(Test_Result $result_);
    //--------------------------------------------------------------------------
  }
?>
