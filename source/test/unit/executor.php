<?php


namespace Components;


  /**
   * Test_Unit_Executor
   *
   * @package net.evalcode.components.test
   * @subpackage unit
   *
   * @author evalcode.net
   */
  class Test_Unit_Executor
  {
    // CONSTRUCTION
    public function __construct(Test_Unit_Runner $runner_)
    {
      $this->m_runner=$runner_;
    }
    //--------------------------------------------------------------------------


    // ACCESSORS
    public function setSuite($class_)
    {
      $suite=new Test_Unit_Internal_DynamicProxy($class_, $this->m_runner);
      if(false===$suite->isInstanceOf($this->m_runner->typeTestSuite))
      {
        throw new Exception_IllegalArgument('test/unit/executor', sprintf(
          'Type of %1$s expected [%2$s].', $this->m_runner->typeTestSuite, $class_
        ));
      }

      $suite->createInstance();

      $this->m_suite=$suite;
      foreach($this->m_suite->cases() as $testCaseClass)
        $this->addCase($testCaseClass);
    }

    public function addCase($class_)
    {
      $case=new Test_Unit_Internal_DynamicProxy($class_, $this->m_runner);

      if(false===$case->isInstanceOf($this->m_runner->typeTestCase))
      {
        throw new Exception_IllegalArgument('test/unit/executor', sprintf(
          'Type of %1$s expected [%2$s].', $this->m_runner->typeTestCase, $class_
        ));
      }

      array_push($this->m_cases, $case);
    }

    public function execute(Test_Result $result_)
    {
      $result_->path=$this->m_suite->getPath();
      $result_->name=$this->m_suite->getClass()->name;

      $this->m_runner->output->enterSuite($result_);

      $this->invokeMethod($result_, $this->m_suite, Annotation_BeforeSuite::NAME);

      foreach($this->m_cases as $case)
        $this->executeCase($case, $result_->create(Test_Result::TYPE_CASE));

      $this->invokeMethod($result_, $this->m_suite, Annotation_AfterSuite::NAME);

      $this->m_runner->output->leaveSuite($result_);
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @var \Components\Test_Unit_Internal_DynamicProxy[]
     */
    protected $m_cases=[];
    /**
     * @var \Components\Test_Unit_Internal_DynamicProxy
     */
    protected $m_suite;
    /**
     * @var \Components\Test_Unit_Runner
     */
    protected $m_runner;
    //-----


    protected function executeCase(Test_Unit_Internal_DynamicProxy $case_, Test_Result $result_)
    {
      $result_->path=$case_->getPath();
      $result_->name=$case_->getClass()->name;

      $this->m_runner->output->enterCase($result_);

      $this->invokeMethod($result_, $case_, Annotation_BeforeClass::NAME);

      $case_->createInstance();

      $tests=$case_->getTests();

      foreach($tests as $test)
      {
        $resultTest=$result_->create(Test_Result::TYPE_TEST);
        $resultTest->name=$test->name;

        $this->m_runner->output->enterTest($resultTest);

        if($case_->skipTest($test))
        {
          $resultTest->addState(Test_RESULT::STATE_SKIPPED);
        }
        else
        {
          $this->invokeMethod($result_, $case_, Annotation_BeforeMethod::NAME);
          $this->invokeTest($case_, $test, $resultTest);
          $this->invokeMethod($result_, $case_, Annotation_AfterMethod::NAME);
        }

        $this->m_runner->output->leaveTest($resultTest);
      }

      $case_->destroyInstance();

      $this->invokeMethod($result_, $case_, Annotation_AfterClass::NAME);

      $this->m_runner->output->leaveCase($result_);
    }

    protected function invokeTest(Test_Unit_Internal_DynamicProxy $case_, \ReflectionMethod $test_, Test_Result $result_)
    {
      Assertion_Context::push(new Assertion_Context());

      $start=microtime(true);
      $case_->{$test_->name}();
      $result_->processingTime=microtime(true)-$start;

      $context=Assertion_Context::pop();

      foreach($context->getAssertions() as $assertion)
      {
        $assertionResult=$result_->create(Test_Result::TYPE_ASSERTION);
        $assertionResult->name=$assertion['name'];
        $assertionResult->output=$assertion['message'];

        if(false===$assertion['result'])
          $assertionResult->addState(Test_Result::STATE_FAILED);
      }

      $failed=$case_->isFailed($test_);
      $failedExpected=$case_->isFailedExpected($test_);
      $failedAssertions=$result_->count(Test_Result::TYPE_ASSERTION, Test_Result::STATE_FAILED, true);

      if((($failed || 0<$failedAssertions) && false===$failedExpected)
       || ((false===$failed && 1>$failedAssertions) && $failedExpected))
      {
        $result_->addState(Test_Result::STATE_FAILED);
        $result_->exception=$case_->getException($test_);

        if(null===$result_->exception
          && ($expectedExceptionClass=$case_->getExpectedExceptionClass($test_)))
        {
          $result_->exception=Exception_Flat::createEmpty();
          $result_->exception->message=sprintf(
            'Expected exception not thrown [%1$s].', $expectedExceptionClass
          );
        }
      }

      if($profiler=$case_->getProfilerResult($test_))
      {
        $result_->processingTime=$profiler->processingTime();

        $result_->profilerMemoryConsumption=$profiler->memoryConsumptionAsString();
        $result_->profilerPosixTimes=$profiler->posixTimesAsString();
        $result_->profilerProcessingTime=$profiler->processingTimeAsString();
        $result_->profilerSplitTimeTable=$profiler->splitTimeTable();
      }

      $result_->output=$case_->getOutput($test_);
    }

    protected function invokeMethod(Test_Result $parentResult_, Test_Unit_Internal_DynamicProxy $proxy_, $methodName_)
    {
      if(null!==($method=$proxy_->getMethod($methodName_)))
        $proxy_->{$methodName_}();
    }
    //--------------------------------------------------------------------------
  }
?>
