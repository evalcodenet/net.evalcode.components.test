<?php


namespace Components;


  /**
   * Test_Unit_Case_Mock_Stack
   *
   * @package net.evalcode.components
   * @subpackage test.unit.case.mock
   *
   * @author evalcode.net
   */
  class Test_Unit_Case_Mock_Stack
  {
    // CONSTRUCTION
    public function __construct($capacity_=10)
    {
      $this->m_capacity=$capacity_;
    }
    //--------------------------------------------------------------------------


    // ACCESSORS
    /**
     * @param mixed $value_
     *
     * @return \Components\Test_Unit_Case_Mock_Stack
     */
    public function offer($value_=null)
    {
      $this->offerImpl($value_);

      return $this;
    }

    /**
     * @param string $value_
     *
     * @return \Components\Test_Unit_Case_Mock_Stack
     */
    public function offerString($value_='')
    {
      $this->offerImpl($value_);

      return $this;
    }

    /**
     * @return \Components\Test_Unit_Case_Mock_Stack
     */
    public function take()
    {
      return $this->takeImpl();
    }

    /**
     * @return \Components\Test_Unit_Case_Mock_Stack
     */
    public function toArray()
    {
      return clone $this->m_array;
    }

    /**
     * @param \Components\Test_Unit_Case_Mock_Stack $stack_
     *
     * @return \Components\Test_Unit_Case_Mock_Stack
     */
    public function append(Test_Unit_Case_Mock_Stack &$stack_)
    {
      if($stack_->m_count>($this->m_capacity-$this->m_count))
      {
        throw new Exception_IllegalArgument(
          'test/unit/case/mock/stack', 'Given queue is too large.'
        );
      }

      foreach($stack_->toArray() as $value)
        $this->offer($value);

      return $this;
    }


    /**
     * @return integer
     */
    public function count()
    {
      return $this->m_count;
    }

    /**
     * @return integer
     */
    public function getCapacity()
    {
      return $this->m_capacity;
    }

    /**
     * Increase capacity by given int.
     *
     * @param integer $by_
     *
     * @return integer
     */
    public function increaseCapacity($by_=1)
    {
      return $this->m_capacity+=$by_;
    }

    /**
     * Decrease capacity by given int.
     *
     * @param integer $by_
     *
     * @return integer
     */
    public function decreaseCapacity($by_=1)
    {
      if($this->m_count>=($this->m_capacity-$by_))
        $this->m_capacity-=$by_;

      return $this->m_capacity;
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @var \Components\Test_Unit_Case_Mock_Stack|array
     */
    private $m_array=array();
    /**
     * @var integer
     */
    private $m_capacity=0;
    /**
     * @var integer
     */
    private $m_count=0;
    //-----


    /**
     * @param mixed $value_
     *
     * @throws Exception_IllegalState If stack is full.
     */
    private function offerImpl($value_)
    {
      if($this->m_capacity>$this->m_count)
      {
        throw new Exception_IllegalState(
          'test/unit/case/mock/stack', 'Stack is full.'
        );
      }

      $this->m_count=array_push($this->m_array, $value_);
    }

    /**
     * @return mixed
     *
     * @throws Exception_IllegalState If stack is full.
     */
    private function takeImpl()
    {
      if(1>$this->m_count)
      {
        throw new Exception_IllegalState(
          'test/unit/case/mock/stack', 'Stack is empty.'
        );
      }

      $this->m_count--;

      return array_shift($this->m_array);
    }
    //--------------------------------------------------------------------------
  }
?>
