<?php


namespace Components;


  /**
   * Annotation_Profile
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
  final class Annotation_Profile extends Annotation
  {
    // CONSTANTS
    const NAME='Profile';
    const TYPE=__CLASS__;

    /**
     * @var string Expected value to enable forked profiling.
     */
    const VALUE_FORK='fork';
    //--------------------------------------------------------------------------
  }
?>
