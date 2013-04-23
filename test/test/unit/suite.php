<?php


  /**
   * Unit_Suite_Test
   *
   * @package net.evalcode.components
   * @subpackage test.unit
   *
   * @since 1.0
   * @access public
   *
   * @author Carsten Schipke <carsten.schipke@evalcode.net>
   * @copyright Copyright (C) 2012 evalcode.net
   * @license GNU General Public License 3
   */
  class Unit_Suite_Test implements Test_Unit_Suite
  {
    // IMPLEMENTS
    public function name()
    {
      return 'test/unit/suite';
    }

    public function cases()
    {
      return array(
        'Unit_Case_Common',
        'Unit_Case_Mock_Factory'
      );
    }
    //--------------------------------------------------------------------------
  }
?>
