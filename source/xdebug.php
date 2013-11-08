<?php


namespace Components;


  /**
   * Xdebug
   *
   * @package net.evalcode.components.test
   *
   * @author evalcode.net
   */
  class Xdebug
  {
    // STATIC ACCESSORS
    public static function getCodeCoverageReport()
    {
      if(self::isCodeCoverageAnalysisRunning())
        self::collectCodeCoverageReport();

      return self::$m_codeCoverageReport;
    }

    public static function startCodeCoverageAnalysis()
    {
      if(self::isCodeCoverageAnalysisRunning())
        return;

      self::clearCodeCoverageReport();
      self::perparePhpIni();

      xdebug_start_code_coverage();

      self::$m_isCodeCoverageAnalysisRunning=true;
    }

    public static function stopCodeCoverageAnalysis()
    {
      if(false===self::isCodeCoverageAnalysisRunning())
        return;

      self::collectCodeCoverageReport();
      xdebug_stop_code_coverage(true);

      self::restorePhpIni();

      self::$m_isCodeCoverageAnalysisRunning=false;
    }

    public static function pauseCodeCoverageAnalysis()
    {
      if(false===self::isCodeCoverageAnalysisRunning())
        return;

      self::collectCodeCoverageReport();
      xdebug_stop_code_coverage(false);

      self::$m_isCodeCoverageAnalysisRunning=false;
    }

    public static function isCodeCoverageAnalysisRunning()
    {
      return self::$m_isCodeCoverageAnalysisRunning;
    }

    public static function clearCodeCoverageReport()
    {
      self::$m_codeCoverageReport=[];
    }

    public static function isSupported()
    {
      return extension_loaded('xdebug');
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    private static $m_phpIni=array(
      'xdebug.coverage_enable'=>'true'
    );
    private static $m_codeCoverageReport=[];
    private static $m_isCodeCoverageAnalysisRunning=false;
    //-----


    // HELPERS
    private static function collectCodeCoverageReport()
    {
      self::$m_codeCoverageReport=xdebug_get_code_coverage();
    }

    /**
     * Do necessary php.ini modifications.
     */
    private static function perparePhpIni()
    {
      foreach(self::$m_phpIni as $property=>$value)
        ini_set($property, $value);
    }

    /**
     * Restore original php.ini settings.
     */
    private static function restorePhpIni()
    {
      foreach(self::$m_phpIni as $property=>$value)
        ini_restore($property);
    }
    //--------------------------------------------------------------------------
  }
?>
