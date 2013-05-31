<?php


namespace Components;


  /**
   * Test_Unit_Suite_Common
   *
   * @package net.evalcode.components
   * @subpackage test.unit.suite
   *
   * @author evalcode.net
   */
  class Test_Unit_Suite_Common implements Test_Unit_Suite
  {
    // OVERRIDES
    public function name()
    {
      return 'test/unit/suite';
    }

    public function cases()
    {
      return array(
        'Components\\Test_Unit_Case_Common',
        'Components\\Test_Unit_Case_Mock'
      );
    }
    //--------------------------------------------------------------------------
  }
?>
