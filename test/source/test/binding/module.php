<?php


namespace Components;


  /**
   * Test_Binding_Module
   *
   * @package net.evalcode.components
   * @subpackage test.binding
   *
   * @since 1.0
   * @access public
   *
   * @author Carsten Schipke <carsten.schipke@evalcode.net>
   * @copyright Copyright (C) 2012 evalcode.net
   * @license GNU General Public License 3
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
