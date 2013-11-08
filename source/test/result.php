<?php


namespace Components;


  /**
   * Test_Result
   *
   * @package net.evalcode.components.test
   *
   * @author evalcode.net
   */
  class Test_Result
  {
    // PREDEFINED PROPERTIES
    const TYPE_ROOT=1;
    const TYPE_SUITE=2;
    const TYPE_CASE=4;
    const TYPE_TEST=8;
    const TYPE_ASSERTION=16;
    const TYPE_ALL=127;

    const STATE_FAILED=1;
    const STATE_SKIPPED=2;
    const STATE_ALL=3;

    const PRECISION_PROCESSING_TIME=4;
    //--------------------------------------------------------------------------


    // PROEPRTIES
    public $results=[];

    /**
     * @var \Components\Test_Result
     */
    public $parent;

    public $index;
    public $name;
    public $type;
    public $path;
    public $output;
    /**
     * @var \Components\Exception_Flat
     */
    public $exception;

    // TODO Remove/merge with profilerProcessingTime or remove profiler* property prefixes
    public $processingTime;
    public $profilerSplitTimeTable=[];
    public $profilerMemoryConsumption;
    public $profilerPosixTimes;
    public $profilerProcessingTime;
    //--------------------------------------------------------------------------


    // CONSTRUCTION
    public function __construct($type_=self::TYPE_ROOT)
    {
      $this->type=$type_;
    }
    //--------------------------------------------------------------------------


    // ACCESSORS
    /**
     * Creates and appends a child result.
     *
     * @return Test_Result
     */
    public function create($type_=self::TYPE_TEST, $clazz_=null)
    {
      if(null===$clazz_)
        $clazz_=get_class($this);
      else
        $this->validateResultType($clazz_);

      $result=new $clazz_($type_);
      $this->addChild($result);

      return $result;
    }

    /**
     * Appends a child result.
     *
     * @return Test_Result
     */
    public function addChild(Test_Result $result_)
    {
      $result_->parent=$this;
      $result_->index=array_push($this->results, $result_);

      return $this;
    }

    /**
     * @return Test_Result
     */
    public function getRoot()
    {
      $parent=$this;
      while($parent->parent)
        $parent=$parent->parent;

      return $parent;
    }

    public function getPath()
    {
      return implode('/', $this->getPathNodes());
    }

    public function getPathNodes()
    {
      $parent=$this;

      $path=[];
      while($parent->name)
      {
        array_unshift($path, $parent->name);
        $parent=$parent->parent;
      }

      return $path;
    }

    public function getIndex()
    {
      $parent=$this;

      $index=[];
      while($parent->index)
      {
        array_unshift($index, $parent->index);
        $parent=$parent->parent;
      }

      return $index;
    }

    public function collect($type_=self::TYPE_TEST, $state_=self::STATE_ALL, $recursive_=true, array &$results_=[])
    {
      if(0<($this->type&$type_) && (self::STATE_ALL===$state_?true:$this->hasState($state_)))
        array_push($results_, $this);

      if($recursive_)
      {
        foreach($this->results as $result)
          $result->collect($type_, $state_, $recursive_, $results_);
      }

      return $results_;
    }

    public function count($type_=self::TYPE_TEST, $state_=self::STATE_ALL, $recursive_=true, &$count_=0)
    {
      if(0<($this->type&$type_) && (self::STATE_ALL===$state_?true:$this->hasState($state_)))
        $count_++;

      if($recursive_)
      {
        foreach($this->results as $result)
          $result->count($type_, $state_, $recursive_, $count_);
      }

      return $count_;
    }

    public function addState($state_)
    {
      $this->m_state=$this->m_state|$state_;
    }

    public function removeState($state_)
    {
      $this->m_state-=$this->m_state&$state_;
    }

    public function hasState($state_)
    {
      return 0<($this->m_state&$state_);
    }

    public function getProcessingTime()
    {
      $processingTime=$this->processingTime;
      foreach($this->collect(self::TYPE_TEST) as $result)
        $processingTime+=$result->processingTime;

      return $processingTime;
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    protected $m_state=0;
    //-----


    protected function validateResultType($class_)
    {
      if(__CLASS__==$class_)
        return;

      $class=new \ReflectionClass($class_);

      if(false===$class->isSubclassOf(__CLASS__))
      {
        throw new Exception_IllegalArgument('test/result',
          sprintf('Sub-class of %1$s expected [class: %2$s].',
            __CLASS__, $class_
          )
        );
      }
    }
    //--------------------------------------------------------------------------
  }
?>
