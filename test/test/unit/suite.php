<?php


namespace Test;


  /**
   * Test_Unit_Suite
   *
   * @package net.evalcode.components
   * @subpackage test.unit.suite
   *
   * @since 1.0
   * @access public
   *
   * @author Carsten Schipke <carsten.schipke@evalcode.net>
   * @copyright Copyright (C) 2012 evalcode.net
   * @license GNU General Public License 3
   */
  class Test_Unit_Suite implements \Components\Test_Unit_Suite
  {
    // IMPLEMENTS
    public function name()
    {
      return 'test/unit/suite';
    }

    public function cases()
    {
      return array(
        'Test\Test_Unit_Case_Common',
        'Test\Test_Unit_Case_Mock'
      );
    }
    //--------------------------------------------------------------------------
  }
?>
