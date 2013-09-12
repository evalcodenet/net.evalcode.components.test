<?php


namespace Components;


  /**
   * Annotation_Profile
   *
   * @api
   * @package net.evalcode.components.test
   * @subpackage annotation
   *
   * @author evalcode.net
   */
  final class Annotation_Profile extends Annotation
  {
    // PREDEFINED PROPERTIES
    const NAME='profile';
    const TYPE=__CLASS__;
    /**
     * @var string Expected value to enable forked profiling.
     */
    const VALUE_FORK='fork';
    //--------------------------------------------------------------------------
  }
?>
