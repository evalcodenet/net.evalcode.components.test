<?php


  /**
   * Annotation_Test
   *
   * @package net.evalcode.components
   * @subpackage test.annotation
   *
   * @since 1.0
   * @access public
   *
   * @author Carsten Schipke <carsten.schipke@evalcode.net>
   * @copyright Copyright (C) 2012 evalcode.net
   * @license GNU General Public License 3
   */
  final class Annotation_Test extends Annotation
  {
    // CONSTANTS
    const NAME='Test';
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
