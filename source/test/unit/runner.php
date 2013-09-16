<?php


namespace Components;


  /**
   * Test_Unit_Runner
   *
   * @package net.evalcode.components.test
   * @subpackage unit
   *
   * @author evalcode.net
   */
  class Test_Unit_Runner extends Test_Runner
  {
    // CONSTRUCTION
    protected function __construct()
    {
      $this->typeTestCase='Components\\Test_Unit_Case';
      $this->typeTestSuite='Components\\Test_Unit_Suite';

      $this->includePathSchema='test/unit';
    }
    //--------------------------------------------------------------------------


    // OVERRIDES
    public function hashCode()
    {
      return object_hash($this);
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
    protected function initialize()
    {
      $this->addClass(__DIR__.'/../../assertion.php');

      $this->addResultHandler(
        new Test_Unit_Result_Handler(Io::path($this->getBuildPath())->test->unit)
      );
    }

    /**
     * Initiates unit test execution.
     *
     * Results are written to given instance of Test_Result.
     */
    protected function execute()
    {
      $this->output->appendBanner();

      foreach($this->m_suites as $suite)
      {
        $executor=new Test_Unit_Executor($this);

        $executor->setSuite($suite);
        $executor->execute($this->m_result->create(Test_Result::TYPE_SUITE));
      }

      $this->output->appendSummary($this->m_result);
    }
    //--------------------------------------------------------------------------
  }
?>
