<?php


namespace Components;


  /**
   * Test_Listener
   *
   * @package net.evalcode.components
   * @subpackage test
   *
   * @author evalcode.net
   */
  interface Test_Listener extends Object
  {
    // PREDEFIEND PROPERTIES
    const INITIALIZATION='onInitialize';
    const EXECUTION='onExecute';
    const TERMINATION='onTerminate';
    //--------------------------------------------------------------------------

    // ACCESSORS
    /**
     * @param \Components\Test_Runner $runner_
     */
    function onInitialize(Test_Runner $runner_);
    /**
     * @param \Components\Test_Runner $runner_
     */
    function onExecute(Test_Runner $runner_);
    /**
     * @param \Components\Test_Runner $runner_
     */
    function onTerminate(Test_Runner $runner_);
    //--------------------------------------------------------------------------
  }
?>
