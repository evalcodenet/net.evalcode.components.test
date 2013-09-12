<?php


namespace Components;


  /**
   * Test_Output_Null
   *
   * @package net.evalcode.components.test
   * @subpackage output
   *
   * @author evalcode.net
   */
  class Test_Output_Null implements Test_Output
  {
    // OVERRIDES
    public function appendBanner()
    {
      // Do nothing ...
    }

    public function appendSummary(Test_Result $result_)
    {
      // Do nothing ...
    }

    public function enterSuite(Test_Result $result_)
    {
      // Do nothing ...
    }

    public function leaveSuite(Test_Result $result_)
    {
      // Do nothing ...
    }

    public function enterCase(Test_Result $result_)
    {
      // Do nothing ...
    }

    public function leaveCase(Test_Result $result_)
    {
      // Do nothing ...
    }

    public function enterTest(Test_Result $result_)
    {
      // Do nothing ...
    }

    public function leaveTest(Test_Result $result_)
    {
      // Do nothing ...
    }

    public function appendAssertion($name_, $successful_, $message_)
    {
      // Do nothing ...
    }

    public function appendLine($string_='')
    {
      // Do nothing ...
    }
    //--------------------------------------------------------------------------
  }
?>
