<?php


namespace Components;


  /**
   * Test_IO_Output
   *
   * API: I/O Output.
   *
   * @package tncTestPlugin
   * @subpackage lib.io
   *
   * @author evalcode.net
   */
  abstract class Test_IO_Output
  {
    // PREDEFINED PROPERTIES
    const LINE_LENGTH=100;
    const LINE_INDENT=Test_Misc::TAB;
    const LINE_BREAK=Test_Misc::LINEFEED;

    const PRECISION_PROCESSING_TIME=5;

    const STATUS_FAILED='X';
    const STATUS_IGNORED='-';
    const STATUS_SUCCEEDED='O';
    //--------------------------------------------------------------------------


    // ACCESSORS/MUTATORS
    public abstract function open();
    public abstract function close();
    public abstract function isOpen();

    public abstract function buffer();
    public abstract function flush();
    public abstract function clear();

    public abstract function hashCode();

    public function addFilter(Test_IO_Filter $filter_)
    {
      $this->m_filters[$filter_->hashCode()]=$filter_;
    }

    public function removeFilter(Test_IO_Filter $filter_)
    {
      if(isset($this->m_filters[$filter_->hashCode()]))
        unset($this->m_filters[$filter_->hashCode()]);
    }

    public function append($stringText_,
      $intIndentSize_=0, $stringIndentText_=self::LINE_INDENT)
    {
      $this->appendFiltered(
        $this->textLine(
          $stringText_,
          self::LINE_LENGTH,
          '',
          $intIndentSize_,
          $stringIndentText_
      ));
    }

    public function appendLine($stringText_,
      $intLineLength_=self::LINE_LENGTH, $charLineBreak_=self::LINE_BREAK,
      $intIndentSize_=0, $stringIndentText_=self::LINE_INDENT)
    {
      $this->appendFiltered(
        $this->textLine(
          $stringText_,
          $intLineLength_,
          $charLineBreak_,
          $intIndentSize_,
          $stringIndentText_
      ));
    }

    public function appendLineEmpty($intLineLength_=self::LINE_LENGTH,
      $charLineBreak_=self::LINE_BREAK, $intIndentSize_=0,
      $stringIndentText_=self::LINE_INDENT)
    {
      $this->appendLine(
        ' ',
        $intLineLength_,
        $charLineBreak_,
        $intIndentSize_,
        $stringIndentText_
      );
    }

    public function appendLineIndented($stringText_,
      $stringIndentText_=self::LINE_INDENT, $intIndentSize_=1,
      $intLineLength_=self::LINE_LENGTH, $charLineBreak_=self::LINE_BREAK)
    {
      $this->appendLine(
        $stringText_,
        $intLineLength_,
        $charLineBreak_,
        $intIndentSize_,
        $stringIndentText_
      );
    }

    public function appendLines($stringTextOrArrayOfLines_,
      $intLineLength_=self::LINE_LENGTH, $charLineBreak_=self::LINE_BREAK,
      $intIndentSize_=0, $stringIndentText_=self::LINE_INDENT,
      $charTextLineBreak_=self::LINE_BREAK)
    {
      if(!is_array($stringTextOrArrayOfLines_))
      {
        $stringTextOrArrayOfLines_=explode(
          $charTextLineBreak_, $stringTextOrArrayOfLines_
        );
      }

      foreach($stringTextOrArrayOfLines_ as $line)
      {
        $this->appendLine(
          $line,
          $intLineLength_,
          $charLineBreak_,
          $intIndentSize_,
          $stringIndentText_
        );
      }
    }

    public function appendLinesIndented($stringText_,
      $stringIndentText_=self::LINE_INDENT, $intIndentSize_=1,
      $intLineLength_=self::LINE_LENGTH, $charLineBreak_=self::LINE_BREAK,
      $charTextLineBreak_=self::LINE_BREAK)
    {
      $this->appendLines(
        $stringText_,
        $intLineLength_,
        $charLineBreak_,
        $intIndentSize_,
        $stringIndentText_,
        $charTextLineBreak_
      );
    }

    public function appendSection($stringSectionTitle_,
      $stringSeparatorAbove_='-', $stringSeparatorBelow_='-',
      $intLineLength_=self::LINE_LENGTH, $charLineBreak_=self::LINE_BREAK,
      $intIndentSize_=0, $stringIndentText_=self::LINE_INDENT)
    {
      $this->appendFiltered(
        $this->fillLine(
          $stringSeparatorAbove_,
          $intLineLength_,
          $charLineBreak_,
          $intIndentSize_,
          $stringIndentText_
        ).
        $this->textLine(
          $stringSectionTitle_,
          $intLineLength_,
          $charLineBreak_,
          $intIndentSize_,
          $stringIndentText_
        ).
        $this->fillLine(
          $stringSeparatorBelow_,
          $intLineLength_,
          $charLineBreak_,
          $intIndentSize_,
          $stringIndentText_
      ));
    }

    public function appendSeparator($label_=null, $stringSeparator_='-',
      $intLineLength_=self::LINE_LENGTH, $charLineBreak_=self::LINE_BREAK,
      $intIndentSize_=0, $stringIndentText_=self::LINE_INDENT)
    {
      if(null!==$label_)
      {
        $intLineLength_-=mb_strlen($label_)+1;
        $label_.=' ';
      }

      $this->appendFiltered(
        $label_.
        $this->fillLine(
          $stringSeparator_,
          $intLineLength_,
          $charLineBreak_,
          $intIndentSize_,
          $stringIndentText_
      ));
    }

    public function appendTableRow(array $cells_,
      $stringCellBorderHorizontal_='', $stringCellBorderVertical_='',
      $intLineLength_=self::LINE_LENGTH, $charLineBreak_=self::LINE_BREAK,
      $intIndentSize_=0, $stringIndentText_=self::LINE_INDENT)
    {
      $indent=$this->textIndent($stringIndentText_, $intIndentSize_);
      $indentLength=strlen($indent);
      $lineLength=$intLineLength_-$indentLength;
      $cellLength=$lineLength/count($cells_)-strlen($stringCellBorderVertical_);

      $row=$stringCellBorderVertical_;
      foreach($cells_ as $cell)
        $row.=$cell.str_repeat(' ', $cellLength-strlen($cell)).$stringCellBorderVertical_;

      $this->appendSection(
        $row,
        $stringCellBorderHorizontal_,
        $stringCellBorderHorizontal_,
        $intLineLength_,
        $charLineBreak_,
        $intIndentSize_,
        $stringIndentText_
      );
    }

    public function appendResult(Test_Result $result_)
    {
      $status=self::STATUS_SUCCEEDED;
      if($failed=$result_->hasState(Test_Result::STATE_FAILED))
        $status=self::STATUS_FAILED;

      $this->appendResultHeader($result_, $status);

      if(count($result_->profilerSplitTimeTable))
        $this->appendResultSplitTimeTable($result_->profilerSplitTimeTable);

      foreach($result_->collect(Test_Result::TYPE_ASSERTION, Test_Result::STATE_ALL, true) as $assertion)
      {
        if($assertion->hasState(Test_Result::STATE_FAILED))
          $this->appendLineIndented(sprintf('%-90.90s', $assertion->output), ' ', 5);
      }

      if($failed && null!==$result_->exception)
        $this->appendResultException($result_->exception);
      if(null!==$result_->output)
        $this->appendResultOutput($result_->output);
    }

    public function appendResultIgnored(Test_Result $result_, $reason_=null)
    {
      $this->appendResultHeader($result_, self::STATUS_IGNORED);

      if(null!==$reason_)
      {
        $this->appendLinesIndented(
          $this->splitLines($reason_, (self::LINE_LENGTH-9)), ' ', 5
        );
      }
    }

    public function appendResultHeader(Test_Result $result_, $status_='')
    {
      if(0<(Test_Result::TYPE_TEST&$result_->type))
      {
        $path=implode('.', $result_->getIndex());
        $path.=' '.$result_->name;

        $assertions=array();
        foreach($result_->collect(Test_Result::TYPE_ASSERTION, Test_Result::STATE_ALL, true) as $assertion)
          $assertions[]=$assertion->hasState(Test_Result::STATE_FAILED)?'x':'.';

        $path.=' '.implode('', $assertions);
      }
      else
      {
        $path=$result_->name;
      }

      $consumption=array();
      if($result_->profilerMemoryConsumption)
        $consumption[]=$result_->profilerMemoryConsumption;

      if($result_->profilerProcessingTime)
      {
        $consumption[]=$result_->profilerProcessingTime;
      }
      else if($result_->processingTime)
      {
        $precision=self::PRECISION_PROCESSING_TIME;

        $consumption[]=sprintf('%08.8s', sprintf('%-'.$precision.'f',
          round($result_->processingTime, $precision)
        ));
      }

      $this->appendLineIndented(sprintf('%-72.72s %24.24s',
        $path, sprintf('[%18.18s] [%s]', implode(' ', $consumption), $status_)
      ), ' ', 3);
    }

    public function appendConsistencyCheck(Test_Result $result_, $indentation_=3)
    {
      if(0<($result_->type&Test_Consistency_Result::TYPE_EVALUATION))
      {
        $status=self::STATUS_SUCCEEDED;
        if($failed=$result_->hasState(Test_Result::STATE_FAILED))
          $status=self::STATUS_FAILED;

        $status='['.$status.']';
        $width=self::LINE_LENGTH-(4+$indentation_);
      }
      else
      {
        $status=null;
        $width=self::LINE_LENGTH;
      }

      $line=array();
      $line[]=sprintf('%-'.$width.'.'.$width.'s',
        implode('.', $result_->getIndex()).' '.$result_->name
      );

      if(null!==$status)
        $line[]=$status;

      $this->appendLineIndented(implode(' ', $line), ' ', $indentation_);

      foreach($result_->results as $result)
        $this->appendConsistencyCheck($result, $indentation_+2);
    }

    public function appendResultSplitTimeTable(array $splitTimeTable_)
    {
      $precision=self::PRECISION_PROCESSING_TIME;

      foreach($splitTimeTable_ as $splitTimeEntry)
      {
        $this->appendLineIndented(sprintf('%-70.70s %24.24s', $splitTimeEntry[1],
          sprintf('[%18.18s] [ ]', sprintf('%-'.$precision.'f',
            round($splitTimeEntry[0], $precision)
          ))
        ), ' ', 5);
      }
    }

    public function appendResultOutput($output_)
    {
      // TODO Indent, label, break overlapping lines.
      $this->appendLinesIndented(
        $this->splitLines($output_, (self::LINE_LENGTH-9)), ' ', 5
      );
    }

    public function appendResultException(Test_Result_Exception $exception_)
    {
      if(null===$exception_->traceAsString)
      {
        $this->appendLinesIndented(
          $this->splitLines($exception_->message, (self::LINE_LENGTH-9)), ' ', 5
        );
      }
      else
      {
        $location=null;
        if($exception_->file)
          $location=$exception_->file;
        if($location && $exception_->line)
          $location.=':'.$exception_->line;
        if($location)
          $location.=self::LINE_BREAK;

        $this->appendLinesIndented(
          $this->splitLines(
            sprintf('%2$s%1$s%3$s%4$s',
              self::LINE_BREAK,
              $exception_->message,
              $location,
              $exception_->traceAsString
            ),
            (self::LINE_LENGTH-9)
          ), ' ', 5
        );
      }
    }

    public function appendSummary(Test_Result $result_)
    {
      $count=$result_->count(
        Test_Result::TYPE_TEST|Test_Consistency_Result::TYPE_EVALUATION, Test_Result::STATE_ALL
      );

      $failed=$result_->count(
        Test_Result::TYPE_TEST|Test_Consistency_Result::TYPE_EVALUATION, Test_Result::STATE_FAILED
      );
      $skipped=$result_->count(
        Test_Result::TYPE_TEST|Test_Consistency_Result::TYPE_EVALUATION, Test_Result::STATE_SKIPPED
      );

      $precision=self::PRECISION_PROCESSING_TIME;

      $this->appendSeparator('', '_');
      $this->appendLineEmpty();

      $this->appendSection(sprintf(' %-74.60s %s',
          sprintf('%d Tested %d Succeeded %d Ignored %d Failed in %04.6s sec',
            $count,
            $count-($failed+$skipped),
            $skipped,
            $failed,
            sprintf('%08.8s', sprintf('%-02.'.$precision.'f',
              round($result_->getProcessingTime(), $precision)
            ))
          ),
          '[INCR PEAK TIME    ] [R]'
        )
        , ' ', ' ');

      $this->appendLine(' INCR: Increased Memory Consumption in MB due to Test.');
      $this->appendLine(' PEAK: Peak Memory Consumption in MB during Test.');
      $this->appendLine(' TIME: Processing Time in Seconds of Test.');
      $this->appendLine(' R   : (O) Succeeded (X) Failed (-) Ignored.');
      $this->appendLineEmpty();
    }

    public function appendException(\Exception $exception_)
    {
      $this->appendLinesIndented(
        sprintf('%2$s%1$s%3$s:%4$s%1$s%5$s',
          self::LINE_BREAK,
          $exception_->getMessage(),
          $exception_->getFile(),
          $exception_->getLine(),
          $exception_->getTraceAsString()
        ), ' ', 5
      );
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    protected $m_filters=array();
    //-----


    protected abstract function appendImpl($string_);

    protected function appendFiltered($string_)
    {
      $this->filter($string_);
      $this->appendImpl($string_);
    }

    protected function filter(&$string_)
    {
      foreach($this->m_filters as $filter)
        $filter->filter($string_);
    }

    protected function fillLine($stringFill_, $intLineLength_, $charLineBreak_, $intIndentSize_, $stringIndentText_)
    {
      if(!$stringFill_)
        return '';

      return ($indentText=$this->textIndent($stringIndentText_, $intIndentSize_)).
        substr(str_repeat($stringFill_, ($intLineLength_/strlen($stringFill_)+1)), 0, $intLineLength_-strlen($indentText)).
        $charLineBreak_;
    }

    protected function textLine($stringText_, $intLineLength_, $charLineBreak_, $intIndentSize_, $stringIndentText_)
    {
      return $this->textIndent($stringIndentText_, $intIndentSize_).
        $stringText_.
        $charLineBreak_;
    }

    protected function textIndent($stringIndentText_, $intIndentSize_)
    {
      return str_repeat($stringIndentText_, $intIndentSize_);
    }

    protected function splitLines($string_, $lineLength_=self::LINE_LENGTH, $linePrefix_='')
    {
      $lines=array();

      $length=0;
      $line=array();

      foreach(explode(self::LINE_BREAK, $string_) as $string)
      {
        $words=mb_split('\s', $string);

        foreach($words as $key=>$word)
        {
          $length+=mb_strlen($word)+1;
          $line[]=$word;

          if($length>=$lineLength_
            || (isset($words[$key+1]) && (($length+mb_strlen($words[$key+1]))>=$lineLength_)))
          {
            $lines[]=$linePrefix_.implode(' ', $line);
            $line=array();
            $length=0;
          }
        }

        $lines[]=$linePrefix_.implode(' ', $line);
        $line=array();
        $length=0;
      }

      return $lines;
    }
    //--------------------------------------------------------------------------
  }
?>
