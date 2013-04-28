<?php


namespace Components;


  /**
   * Test_LifecycleListener
   *
   * @package net.evalcode.components
   * @subpackage test
   *
   * @author evalcode.net
   */
  interface Test_LifecycleListener
  {
    // PREDEFINED PROPERTIES
    const INITIALIZATION='initialization';
    const EXECUTION='execution';
    const TERMINATION='termination';
    //--------------------------------------------------------------------------


    // ACCESSORS
    function initialization(Test_Runner $runner_);
    function execution(Test_Runner $runner_);
    function termination(Test_Runner $runner_);
    //--------------------------------------------------------------------------
  }
?>
