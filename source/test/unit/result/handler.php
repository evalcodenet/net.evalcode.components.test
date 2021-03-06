<?php


namespace Components;


  /**
   * Test_Unit_Result_Handler
   *
   * @package net.evalcode.components.test
   * @subpackage unit.result
   *
   * @author evalcode.net
   */
  class Test_Unit_Result_Handler implements Test_Result_Handler
  {
    // PREDEFINED PROPERTIES
    const DEFAULT_REPORT_FILE_NAME_PREFIX='TEST-';
    const DEFAULT_REPORT_FILE_NAME_SUFFIX='.xml';

    const XML_ELEMENT_NAME_ERROR='error';
    const XML_ELEMENT_NAME_FAILURE='failure';
    const XML_ELEMENT_NAME_SKIPPED='skipped';
    const XML_ELEMENT_NAME_SYSTEM_OUTPUT='system-out';
    const XML_ELEMENT_NAME_SYSTEM_ERROR='system-err';
    const XML_ELEMENT_NAME_TEST_CASE='testcase';
    const XML_ELEMENT_NAME_TEST_SUITE='testsuite';

    const XML_ATTRIBUTE_NAME_CLASS_NAME='classname';
    const XML_ATTRIBUTE_NAME_ERRORS='errors';
    const XML_ATTRIBUTE_NAME_FAILURES='failures';
    const XML_ATTRIBUTE_NAME_HOSTNAME='hostname';
    const XML_ATTRIBUTE_NAME_MESSAGE='message';
    const XML_ATTRIBUTE_NAME_NAME='name';
    const XML_ATTRIBUTE_NAME_TESTS='tests';
    const XML_ATTRIBUTE_NAME_TIME='time';
    const XML_ATTRIBUTE_NAME_TIMESTAMP='timestamp';
    const XML_ATTRIBUTE_NAME_TYPE='type';
    const XML_ATTRIBUTE_NAME_SKIPS='skips';
    //--------------------------------------------------------------------------


    // CONSTRUCTION
    public function __construct($reportPath_,
      $reportFileNamePrefix_=self::DEFAULT_REPORT_FILE_NAME_PREFIX,
      $reportFileNameSuffix_=self::DEFAULT_REPORT_FILE_NAME_SUFFIX)
    {
      $this->m_reportPath=$reportPath_;

      $this->m_reportFileNamePrefix=$reportFileNamePrefix_;
      $this->m_reportFileNameSuffix=$reportFileNameSuffix_;
    }
    //--------------------------------------------------------------------------


    // OVERRIDES
    /**
     * @see Test_Result_Handler::handleResult() Test_Result_Handler::handleResult()
     */
    public function handleResult(Test_Result $result_)
    {
      $reportPath=$this->createReportPath();

      foreach($result_->collect(Test_Result::TYPE_CASE) as $result)
      {
        $this->writeReport($result,
          $this->createReportFile($reportPath, $result)
        );
      }
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    protected $m_reportFileNamePrefix;
    protected $m_reportFileNameSuffix;
    protected $m_reportPath;
    //-----


    protected function writeReport(Test_Result $resultCase_, $reportFilePath_)
    {
      if(Test_Result::TYPE_CASE!=$resultCase_->type)
      {
        throw new Exception_IllegalArgument('test/unit/result/handler',
          'Expected result of test type "case".'
        );
      }

      $path=strtolower(str_replace('/', '.', dirname($resultCase_->path)));
      $path='.'==substr($path, 0, 1)?substr($path, 1):$path;
      $name=$path.'.'.$resultCase_->name;

      $document=new \DOMDocument('1.0', 'utf-8');
      $document->formatOutput=true;

      $testSuite=$document->createElement(self::XML_ELEMENT_NAME_TEST_SUITE);
      $document->appendChild($testSuite);
      $this->writeSuite($document, $testSuite, $resultCase_, $name);

      foreach($resultCase_->collect(Test_Result::TYPE_TEST) as $resultTest)
      {
        $testCase=$document->createElement(self::XML_ELEMENT_NAME_TEST_CASE);
        $testSuite->appendChild($testCase);
        $this->writeTestResult($document, $testCase, $resultTest, $name);
      }

      if(null!==$resultCase_->output)
        $this->writeSystemOutput($document, $testSuite, $resultCase_->output);

      $document->save($reportFilePath_);
    }

    protected function writeSuite(\DOMDocument $document_, \DOMElement $testSuite_, Test_Result $resultCase_, $name_)
    {
      $host=gethostname();

      // TODO Date
      $date=new \DateTime();
      $date=$date->format(\DateTime::ATOM);

      $tests=$resultCase_->collect(Test_Result::TYPE_TEST, Test_Result::STATE_ALL);
      $failures=$resultCase_->collect(Test_Result::TYPE_TEST, Test_Result::STATE_FAILED);
      $countSkips=$resultCase_->count(Test_Result::TYPE_TEST, Test_Result::STATE_SKIPPED);

      $countErrors=0;
      foreach($failures as $failure)
      {
        if(null!==$failure->exception && $failure->exception->isErrorException)
          $countErrors++;
      }

      $countFailures=count($failures)-$countErrors;

      $processingTime=0;
      foreach($tests as $test)
        $processingTime+=$test->processingTime;

      $testSuite_->setAttribute(self::XML_ATTRIBUTE_NAME_HOSTNAME, $host);
      $testSuite_->setAttribute(self::XML_ATTRIBUTE_NAME_NAME, $name_);
      $testSuite_->setAttribute(self::XML_ATTRIBUTE_NAME_TESTS, count($tests));
      $testSuite_->setAttribute(self::XML_ATTRIBUTE_NAME_SKIPS, $countSkips);
      $testSuite_->setAttribute(self::XML_ATTRIBUTE_NAME_ERRORS, $countErrors);
      $testSuite_->setAttribute(self::XML_ATTRIBUTE_NAME_FAILURES, $countFailures);
      $testSuite_->setAttribute(self::XML_ATTRIBUTE_NAME_TIME, $processingTime);
      $testSuite_->setAttribute(self::XML_ATTRIBUTE_NAME_TIMESTAMP, $date);
    }

    protected function writeTestResult(\DOMDocument $document_, \DOMElement $testCase_, Test_Result $resultTest_, $name_)
    {
      $testCase_->setAttribute(self::XML_ATTRIBUTE_NAME_CLASS_NAME, $name_);
      $testCase_->setAttribute(self::XML_ATTRIBUTE_NAME_NAME, $resultTest_->name);
      $testCase_->setAttribute(self::XML_ATTRIBUTE_NAME_TIME, $resultTest_->processingTime);

      if($resultTest_->hasState(Test_Result::STATE_SKIPPED))
      {
        $testCase_->appendChild($document_->createElement(self::XML_ELEMENT_NAME_SKIPPED));
      }
      else if($resultTest_->hasState(Test_Result::STATE_FAILED))
      {
        if(null!==$resultTest_->exception && $resultTest_->exception->isErrorException)
          $elementName=self::XML_ELEMENT_NAME_ERROR;
        else
          $elementName=self::XML_ELEMENT_NAME_FAILURE;

        if(null!==$resultTest_->exception)
          $this->writeTestException($document_, $testCase_, $resultTest_, $elementName);
        else if($resultTest_->count(Test_Result::TYPE_ASSERTION, Test_Result::STATE_FAILED))
          $this->writeTestAssertionsFailed($document_, $testCase_, $resultTest_, $elementName);
      }
    }

    protected function writeTestException(\DOMDocument $document_, \DOMElement $testCase_, Test_Result $resultTest_, $elementName_)
    {
      $element=$document_->createElement($elementName_);
      $testCase_->appendChild($element);

      $element->setAttribute(self::XML_ATTRIBUTE_NAME_TYPE, $resultTest_->exception->type);
      $element->setAttribute(self::XML_ATTRIBUTE_NAME_MESSAGE, $resultTest_->exception->message);

      $element->appendChild($document_->createCDATASection(sprintf(
        '%2$s%1$s%3$s:%4$s%1$s%5$s',
          Io::LINE_SEPARATOR_DEFAULT,
          $resultTest_->exception->message,
          $resultTest_->exception->file,
          $resultTest_->exception->line,
          $resultTest_->exception->traceAsString
      )));
    }

    protected function writeTestAssertionsFailed(\DOMDocument $document_, \DOMElement $testCase_, Test_Result $resultTest_, $elementName_)
    {
      $element=$document_->createElement($elementName_);
      $testCase_->appendChild($element);

      $element->setAttribute(self::XML_ATTRIBUTE_NAME_TYPE, 'Assertion_Error');
      $element->setAttribute(self::XML_ATTRIBUTE_NAME_MESSAGE, 'Assertion Failed');

      $output=[];
      foreach($resultTest_->collect(Test_Result::TYPE_ASSERTION, Test_Result::STATE_FAILED) as $result)
        $output[]=$result->output;

      $element->appendChild($document_->createCDATASection(implode(Io::LINE_SEPARATOR_DEFAULT, $output)));
    }

    protected function writeSystemOutput(\DOMDocument $document_, \DOMElement $testSuite_, $output_)
    {
      $systemOutput=$document_->createElement(self::XML_ELEMENT_NAME_SYSTEM_OUTPUT);
      $testSuite_->appendChild($systemOutput);

      $systemOutput->appendChild($document_->createCDATASection(implode('', $output_)));
    }

    protected function createReportPath()
    {
      if(false===Io::directoryCreate($this->m_reportPath))
      {
        throw new Exception_IllegalArgument('test/unit/result/handler', sprintf(
          'Unable to create report path [path: %1$s].', $this->m_reportPath
        ));
      }

      return $this->m_reportPath;
    }

    protected function createReportFile($reportPath_, Test_Result $resultCase_)
    {
      if(Test_Result::TYPE_CASE!=$resultCase_->type)
      {
        throw new Exception_IllegalArgument('test/unit/result/handler',
          'Expected result of test type "case".'
        );
      }

      $reportFileName=$this->m_reportFileNamePrefix.$resultCase_->name.
        $this->m_reportFileNameSuffix;

      $reportFilePath=$reportPath_.Io::DIRECTORY_SEPARATOR.$reportFileName;

      // TODO Io_File::create()
      if(false===touch($reportFilePath))
      {
        throw new Exception_IllegalArgument('test/unit/result/handler', sprintf(
          'Unable to create report file [path: %1$s, report: %2%s].',
            $this->m_reportPath,
            $resultCase_->name
        ));
      }

      return $reportFilePath;
    }
    //--------------------------------------------------------------------------
  }
?>
