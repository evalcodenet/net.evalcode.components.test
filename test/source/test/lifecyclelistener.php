<?php


namespace Components;


  /**
   * Test_LifecycleListener
   *
   * @package net.evalcode.components
   * @subpackage test
   *
   * @since 1.0
   * @access public
   *
   * @author Carsten Schipke <carsten.schipke@evalcode.net>
   * @copyright Copyright (C) 2012 evalcode.net
   * @license GNU General Public License 3
   */
  interface Test_LifecycleListener
  {
    // CONSTANTS
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
