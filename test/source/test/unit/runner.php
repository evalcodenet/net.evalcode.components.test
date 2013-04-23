<?php


  /**
   * Test_Unit_Runner
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
  class Test_Unit_Runner extends Test_Runner
  {
    // CONSTRUCTION
    protected function __construct()
    {
      $this->typeTestCase='Test_Unit_Case';
      $this->typeTestSuite='Test_Unit_Suite';

      $this->includePathSchema='test/unit';
    }
    //--------------------------------------------------------------------------


    // OVERRIDES/IMPLEMENTS
    public function hashCode()
    {
      return spl_object_hash($this);
    }

    public function __toString()
    {
      return sprintf('%s@%s', __CLASS__, $this->hashCode());
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    protected function getTitle()
    {
      return 'Unit Test';
    }

    /**
     * Initializes / prepares unit test execution.
     */
    protected function initializeImpl()
    {
      $this->addClass(__DIR__.'/../../assertion.php');

      $this->discoverTests();
    }

    /**
     * Initiates unit test execution.
     *
     * Results are written to given instance of Test_Result.
     */
    protected function runImpl()
    {
      foreach($this->m_suites as $suite)
      {
        $executor=new Test_Unit_Executor($this);

        $executor->setSuite($suite);
        $executor->execute($this->m_result->create(Test_Result::TYPE_SUITE));
      }
    }
    //--------------------------------------------------------------------------
  }
?>
