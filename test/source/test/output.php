<?php


namespace Components;


  /**
   * Test_Output
   *
   * @package net.evalcode.components
   * @subpackage test
   *
   * @author evalcode.net
   * @copyright Copyright (C); 2012 evalcode.net
   * @license GNU General License 3
   */
  interface Test_Output
  {
    // ACCESSORS/MUTATORS
    function appendBanner();
    function appendSummary(Test_Result $result_);

    function enterSuite(Test_Result $result_);
    function leaveSuite(Test_Result $result_);

    function enterCase(Test_Result $result_);
    function leaveCase(Test_Result $result_);

    function enterTest(Test_Result $result_);
    function leaveTest(Test_Result $result_);

    function appendAssertion($name_, $successful_, $message_);
    //--------------------------------------------------------------------------
  }
?>
