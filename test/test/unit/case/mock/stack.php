<?php


  /**
   * Unit_Case_Mock_Stack
   *
   * @package net.evalcode.components
   * @subpackage test.unit.case.mock
   *
   * @since 1.0
   * @access public
   *
   * @author Carsten Schipke <carsten.schipke@evalcode.net>
   * @copyright Copyright (C) 2012 evalcode.net
   * @license GNU General Public License 3
   */
  class Unit_Case_Mock_Stack
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
     * @return Unit_Case_Mock_Queue
     */
    public function offer($value_=null)
    {
      $this->offerImpl($value_);

      return $this;
    }

    /**
     * @param string $value_
     *
     * @return Unit_Case_Mock_Queue
     */
    public function offerString($value_='')
    {
      $this->offerImpl($value_);

      return $this;
    }

    /**
     * @return Unit_Case_Mock_Queue
     */
    public function take()
    {
      return $this->takeImpl();
    }

    /**
     * @return Unit_Case_Mock_Queue
     */
    public function toArray()
    {
      return clone $this->m_array;
    }

    /**
     * @param Unit_Case_Mock_Queue $queue_
     *
     * @return Unit_Case_Mock_Queue
     */
    public function append(Unit_Case_Mock_Queue &$queue_)
    {
      if($queue_->m_count>($this->m_capacity-$this->m_count))
      {
        throw new Exception_IllegalArgument(
          'test/unit/case/mock/queue', 'Given queue is too large.'
        );
      }

      foreach($queue_->toArray() as $value)
        $this->offer($value);

      return $this;
    }


    /**
     * @return int
     */
    public function count()
    {
      return $this->m_count;
    }

    /**
     * @return int
     */
    public function getCapacity()
    {
      return $this->m_capacity;
    }

    /**
     * Increase capacity by given int.
     *
     * @param int $by_
     *
     * @return int
     */
    public function increaseCapacity($by_=1)
    {
      return $this->m_capacity+=$by_;
    }

    /**
     * Decrease capacity by given int.
     *
     * @param int $by_
     *
     * @return int
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
     * @var Unit_Case_Mock_Queue|array
     */
    private $m_array=array();
    /**
     * @var int
     */
    private $m_capacity=0;
    /**
     * @var int
     */
    private $m_count=0;
    //-----


    /**
     * @param mixed $value_
     *
     * @throws Exception_IllegalState If queue is full.
     */
    private function offerImpl($value_)
    {
      if($this->m_capacity>$this->m_count)
      {
        throw new Exception_IllegalState(
          'test/unit/case/mock/queue', 'Queue is full.'
        );
      }

      $this->m_count=array_push($this->m_array, $value_);
    }

    /**
     * @return mixed
     *
     * @throws Exception_IllegalState If queue is full.
     */
    private function takeImpl()
    {
      if(1>$this->m_count)
      {
        throw new Exception_IllegalState(
          'test/unit/case/mock/queue', 'Queue is empty.'
        );
      }

      $this->m_count--;

      return array_shift($this->m_array);
    }
    //--------------------------------------------------------------------------
  }
?>
