<?php


namespace Components;


  /**
   * Test_Binding_Module
   *
   * @package net.evalcode.components.test
   * @subpackage binding
   *
   * @author evalcode.net
   */
  class Test_Binding_Module extends Binding_Module
  {
    // OVERRIDES
    /**
     * @see \Components\Binding_Module::configure()
     */
    protected function configure()
    {
      $this->bind('Test_Runner')->toInstance(Test_Runner::get());
    }
    //--------------------------------------------------------------------------
  }
?>
