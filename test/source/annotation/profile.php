<?php


namespace Components;


  /**
   * Annotation_Profile
   *
   * @package net.evalcode.components
   * @subpackage test.annotation
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
