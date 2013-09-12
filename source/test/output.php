<?php


namespace Components;


  /**
   * Test_Output
   *
   * @package net.evalcode.components.test
   *
   * @author evalcode.net
   */
  interface Test_Output
  {
    // ACCESSORS
    function appendBanner();
    function appendSummary(Test_Result $result_);

    function enterSuite(Test_Result $result_);
    function leaveSuite(Test_Result $result_);

    function enterCase(Test_Result $result_);
    function leaveCase(Test_Result $result_);

    function enterTest(Test_Result $result_);
    function leaveTest(Test_Result $result_);

    function appendAssertion($name_, $successful_, $message_);

    function appendLine($string_='');
    //--------------------------------------------------------------------------
  }
?>
