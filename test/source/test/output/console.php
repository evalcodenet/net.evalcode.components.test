<?php


namespace Components;


  /**
   * Test_Output_Console
   *
   * @package net.evalcode.components
   * @subpackage test.output
   *
   * @author evalcode.net
   */
  // FIXME (Re-)Integrate Profiling & Summaries
  class Test_Output_Console implements Test_Output
  {
    // PROPERTIES
    public $width=80;
    //--------------------------------------------------------------------------


    // CONSTRUCTION
    public function __construct(Io_Console $console_)
    {
      $this->m_console=$console_;
    }
    //--------------------------------------------------------------------------


    // OVERRIDES/IMPLEMENTS
    public function appendBanner()
    {
      $this->appendLine('  Unit Test Executor 0.1');
      $this->append('  net.evalcode.components');
      $this->appendLine(str_repeat(' ', $this->width-54).'Copyright (C) evalcode.net');
      $this->appendLine();
    }

    public function appendSummary(Test_Result $result_)
    {
      $processingTime=sprintf('%2.5f', $result_->getProcessingTime());

      $this->append(str_repeat(' ', $this->width-($this->m_cursor+12)));
      $this->append($processingTime);

      if($result_->hasState(Test_Result::STATE_SKIPPED))
        $this->append(' SKIP');
      else if($result_->count(Test_Result::TYPE_TEST, Test_Result::STATE_FAILED))
        $this->append(' FAIL');
      else
        $this->append('   OK');

      $this->appendLine();
      $this->appendLine();
    }

    public function enterSuite(Test_Result $result_)
    {
      $this->m_case=0;
      $this->m_suite++;
    }

    public function leaveSuite(Test_Result $result_)
    {
      $processingTime=sprintf('%2.5f', $result_->getProcessingTime());

      $this->append(str_repeat(' ', $this->width-($this->m_cursor+12)));
      $this->append($processingTime);

      if($result_->hasState(Test_Result::STATE_SKIPPED))
        $this->append(' SKIP');
      else if($result_->count(Test_Result::TYPE_TEST, Test_Result::STATE_FAILED))
        $this->append(' FAIL');
      else
        $this->append('   OK');

      $this->appendLine();
      $this->appendLine();
    }

    public function enterCase(Test_Result $result_)
    {
      $this->m_test=0;
      $this->m_case++;

      $this->appendLine();
      $this->appendLine(sprintf('  %d.%d [CASE] %s: %s',
        $this->m_suite, $this->m_case, $result_->parent->name, $result_->name
      ));
      $this->appendLine('  '.str_repeat('-', $this->width-2));
    }

    public function leaveCase(Test_Result $result_)
    {
      $this->appendLine();

      $processingTime=sprintf('%2.5f', $result_->getProcessingTime());

      $this->append(str_repeat(' ', $this->width-($this->m_cursor+12)));
      $this->append($processingTime);

      if($result_->hasState(Test_Result::STATE_SKIPPED))
        $this->append(' SKIP');
      else if($result_->count(Test_Result::TYPE_TEST, Test_Result::STATE_FAILED))
        $this->append(' FAIL');
      else
        $this->append('   OK');

      $this->appendLine();
    }

    public function enterTest(Test_Result $result_)
    {
      $this->m_test++;

      $this->appendLine();
      $this->append(sprintf('  %d.%d.%d [TEST] %s ',
        $this->m_suite, $this->m_case, $this->m_test, $result_->name
      ));
    }

    public function leaveTest(Test_Result $result_)
    {
      $processingTime=sprintf('%2.5f', $result_->getProcessingTime());

      $this->append(str_repeat(' ', $this->width-($this->m_cursor+12)));
      $this->append($processingTime);

      if($result_->hasState(Test_Result::STATE_SKIPPED))
        $this->append(' SKIP');
      else if($result_->hasState(Test_Result::STATE_FAILED))
        $this->append(' FAIL');
      else
        $this->append('   OK');

      $this->appendLine();

      if(trim($result_->output))
      {
        $output=wordwrap($result_->output, $this->width-6, Io::LINE_SEPARATOR_DEFAULT, true);

        $lines=explode(Io::LINE_SEPARATOR_DEFAULT, $output);
        foreach($lines as $line)
        {
          if(trim($line))
            $this->m_console->appendLine("    > $line");
        }
      }

      if($result_->count(Test_Result::TYPE_ASSERTION, Test_Result::STATE_FAILED))
      {
        foreach($result_->collect(Test_Result::TYPE_ASSERTION, Test_Result::STATE_FAILED) as $assertion)
          $this->m_console->appendLine("    X {$assertion->output}");
      }
    }

    public function appendAssertion($name_, $successful_, $message_)
    {
      if(true===$successful_)
        $this->append('.');
      else
        $this->append('x');
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @var Io_Console
     */
    private $m_console;
    private $m_cursor=0;
    private $m_suite=0;
    private $m_case=0;
    private $m_test=0;
    //-----


    private function append($string_)
    {
      $this->m_console->append($string_);
      $this->m_console->flush();
      $this->m_cursor+=String::length($string_);
    }

    private function appendLine($string_='')
    {
      $this->m_console->appendLine($string_);
      $this->m_console->flush();
      $this->m_cursor=0;
    }
    //--------------------------------------------------------------------------
  }
?>
