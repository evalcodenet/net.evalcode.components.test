<?php


namespace Components;


  /**
   * Annotation_Test
   *
   * @package net.evalcode.components
   * @subpackage test.annotation
   *
   * @author evalcode.net
   */
  final class Annotation_Test extends Annotation
  {
    // PREDEFINED PROPERTIES
    const NAME='test';
    const TYPE=__CLASS__;
    //--------------------------------------------------------------------------


    // PROPERTIES
    /**
     * @var string Class name of expected exception.
     */
    public $expectedException;
    /**
     * @var boolean Expect test to fail to succeed.
     */
    public $expectedFail;
    //--------------------------------------------------------------------------
  }
?>
