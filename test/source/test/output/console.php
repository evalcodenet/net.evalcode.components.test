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


    // OVERRIDES
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

      $count=$result_->count(
        Test_Result::TYPE_TEST, Test_Result::STATE_ALL
      );

      $failed=$result_->count(
        Test_Result::TYPE_TEST, Test_Result::STATE_FAILED
      );
      $skipped=$result_->count(
        Test_Result::TYPE_TEST, Test_Result::STATE_SKIPPED
      );

      $this->appendLine();

      $this->appendLine(sprintf(' %-54.60s %s',
        sprintf('%d Tested %d Succeeded %d Ignored %d Failed in %04.6s sec',
          $count,
          $count-($failed+$skipped),
          $skipped,
          $failed,
          sprintf('%08.8s', sprintf('%-02.5f', round($result_->getProcessingTime(), 5)))
        ),
        '[INCR PEAK TIME    ] [R]'
      ));

      $this->appendLine(' INCR: Increased Memory Consumption in MB due to Test.');
      $this->appendLine(' PEAK: Peak Memory Consumption in MB during Test.');
      $this->appendLine(' TIME: Processing Time in Seconds of Test.');
      $this->appendLine(' R   : (O) Succeeded (X) Failed (-) Ignored.');
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

      // FIXME (CSH) Align consumption details.
      if($result_->profilerMemoryConsumption)
        $consumption=$result_->profilerMemoryConsumption;
      else
        $consumption=str_repeat(' ', 10);

      $consumption.=sprintf('%07.7s', sprintf('%-.4f',
        round($result_->getProcessingTime(), 5)
      ));

      $this->append(str_repeat(' ', $this->width-($this->m_cursor+22)));
      $this->append($consumption);

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
      // FIXME (CSH) Align consumption details.
      if($result_->profilerMemoryConsumption)
        $consumption=$result_->profilerMemoryConsumption;
      else
        $consumption=str_repeat(' ', 10);

      $consumption.=sprintf('%07.7s', sprintf('%-.4f',
        round($result_->processingTime, 5)
      ));

      $this->m_cursor+=$result_->count(Test_Result::TYPE_ASSERTION);
      $this->append(str_repeat(' ', $this->width-($this->m_cursor+22)));
      $this->append($consumption);

      if($result_->hasState(Test_Result::STATE_SKIPPED))
        $this->append(' SKIP');
      else if($result_->hasState(Test_Result::STATE_FAILED))
        $this->append(' FAIL');
      else
        $this->append('   OK');

      $this->appendLine();

      if(count($result_->profilerSplitTimeTable))
      {
        $this->appendLine();
        $this->appendLine('    + TIMES');
        foreach($result_->profilerSplitTimeTable as $entry)
        {
          $splitTimeTableEntryOutput=str_split(end($entry), $this->width-9);
          $this->appendLine(sprintf('      %07.7s %s',
            sprintf('%-.4f', round(reset($entry), 5)),
            array_shift($splitTimeTableEntryOutput)
          ));
          foreach($splitTimeTableEntryOutput as $line)
            $this->appendLine("       $line");
        }
      }

      if($result_->count(Test_Result::TYPE_ASSERTION, Test_Result::STATE_FAILED))
      {
        $this->appendLine();
        $this->appendLine('    + FAILED ASSERTIONS');
        foreach($result_->collect(Test_Result::TYPE_ASSERTION, Test_Result::STATE_FAILED) as $assertion)
        {
          $assertionOutput=str_split($assertion->output, $this->width-9);
          $this->appendLine('      '.array_shift($assertionOutput));
          foreach($assertionOutput as $line)
            $this->appendLine("        $line");
        }
      }

      if(trim($result_->output))
      {
        $output=wordwrap($result_->output, $this->width-8, Io::LINE_SEPARATOR_DEFAULT, true);
        $lines=explode(Io::LINE_SEPARATOR_DEFAULT, $output);

        $this->appendLine();
        $this->appendLine('    + OUTPUT');

        foreach($lines as $line)
        {
          if(trim($line))
            $this->appendLine("      $line");
        }
      }
    }

    public function appendAssertion($name_, $successful_, $message_)
    {
      if(true===$successful_)
        $this->m_console->append('.');
      else
        $this->m_console->append('x');

      $this->m_console->flush();
    }

    public function appendLine($string_='')
    {
      $this->m_console->appendLine($string_);
      $this->m_console->flush();
      $this->m_cursor=0;
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @var \Components\Io_Console
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
    //--------------------------------------------------------------------------
  }
?>
