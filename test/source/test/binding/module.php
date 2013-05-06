<?php


namespace Components;


  /**
   * Test_Binding_Module
   *
   * @package net.evalcode.components
   * @subpackage test.binding
   *
   * @author evalcode.net
   */
  class Test_Binding_Module extends Binding_Module
  {
    // OVERRIDES
    protected function configure()
    {
      $this->bind('Test_Runner')->toInstance(Test_Runner::get());
    }
    //--------------------------------------------------------------------------
  }
?>
