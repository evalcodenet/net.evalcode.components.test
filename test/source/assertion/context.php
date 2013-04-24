<?php


  /**
   * Assertion_Context
   *
   * @package net.evalcode.components
   * @subpackage test.assertion
   *
   * @since 1.0
   * @access public
   *
   * @author Carsten Schipke <carsten.schipke@evalcode.net>
   * @copyright Copyright (C) 2012 evalcode.net
   * @license GNU General Public License 3
   */
  final class Assertion_Context
  {
    // STATIC ACCESSRS
    /**
     * @return Assertion_Context
     */
    public static function current()
    {
      return end(self::$m_instances);
    }

    /**
     * @param Test_Result $result_
     *
     * @return Assertion_Counter
     */
    public static function push(Assertion_Context $instance_)
    {
      array_push(self::$m_instances, $instance_);

      return $instance_;
    }

    /**
     * @return Assertion_Context
     */
    public static function pop()
    {
      return array_pop(self::$m_instances);
    }
    //--------------------------------------------------------------------------


    // ACCESSORS
    /**
     * @param string $name_
     * @param boolean $successful_
     * @param string $message_
     */
    public function add($name_, $successful_=false, $message_=null)
    {
      array_push($this->m_assertions, array(
        'name'=>$name_,
        'result'=>$successful_,
        'message'=>$message_
      ));

      Test_Runner::get()->output->appendAssertion($name_, $successful_, $message_);
    }

    public function getAssertions()
    {
      return $this->m_assertions;
    }

    public function setAssertions(array $assertions_)
    {
      $this->m_assertions=$assertions_;
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @var Assertion_Context|array
     */
    private static $m_instances=array();

    private $m_assertions=array();
    //--------------------------------------------------------------------------
  }
?>
