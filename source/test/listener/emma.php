<?php


namespace Components;


  /**
   * Test_Listener_Emma
   *
   * Implementation: Lifecycle Listener to integrate
   * Emma code coverage report generation.
   *
   * @package net.evalcode.components
   * @subpackage test.listener
   *
   * @author evalcode.net
   */
  class Test_Listener_Emma implements Test_Listener
  {
    // PROPERTIES
    public $excludePattern;
    //--------------------------------------------------------------------------


    // CONSTRUCTION
    public function __construct($rootPath_, $buildPath_)
    {
      $this->m_rootPath=$rootPath_;
      $this->m_buildPath=$buildPath_;
    }
    //--------------------------------------------------------------------------


    // OVERRIDES
    /**
     * Starts xDebug code coverage analysis on test execution, if supported.
     *
     * @param \Components\Test_Result $runner_
     *
     * @see Components\Test_Listener::onExecute() Components\Test_Listener::onExecute()
     */
    public function onExecute(Test_Runner $runner_)
    {
      if(false===Xdebug::isSupported())
      {
        $runner_->output->appendLine(
          'Missing extension \'xdebug\': code coverage analysis will be skipped.'
        );

        return;
      }

      Xdebug::startCodeCoverageAnalysis();
    }

    /**
     * Stops xDebug code coverage analysis on test termination.
     *
     * Invokes EMMA code coverage report generation if
     * xDebug code coverage analysis results are available.
     *
     * @param Test_Runner $runner_
     *
     * @see Components\Test_Listener::onTerminate() Components\Test_Listener::onTerminate()
     */
    public function onTerminate(Test_Runner $runner_)
    {
      Xdebug::stopCodeCoverageAnalysis();

      if(count($report=Xdebug::getCodeCoverageReport()))
      {
        $runner_->output->appendLine(
          'Generating xDebug Code Coverage Analysis Report.'
        );

        $this->report($runner_, $report);

        $runner_->output->appendLine(
          "Wrote xDebug Code Coverage Analysis Report to {$this->m_buildPath}."
        );
      }
    }

    /**
     * @param Test_Runner $runner_
     *
     * @see Components\Test_Listener::onInitialize() Components\Test_Listener::onInitialize()
     */
    public function onInitialize(Test_Runner $runner_)
    {
      // Do nothing ...
    }

    /**     * @see Components\Object::hashCode() Components\Object::hashCode()
     */
    public function hashCode()
    {
      return object_hash($this);
    }

    /**     * @see Components\Object::equals() Components\Object::equals()
     */
    public function equals($object_)
    {
      if($object_ instanceof self)
        return $this->hashCode()===$object_->hashCode();

      return false;
    }

    /**     * @see Components\Object::__toString() Components\Object::__toString()
     */
    public function __toString()
    {
      return sprintf('%s@%s{}', __CLASS__, $this->hashCode());
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    protected $m_classPaths=array();
    protected $m_methods=array();
    protected $m_classes=array();
    protected $m_packages=array();
    protected $m_files=array();
    protected $m_lines=0;
    protected $m_buildPath;
    protected $m_rootPath;
    //-----


    /**
     * Generates EMMA code coverage report.
     *
     * @param array $xDebugReport_
     *
     * @todo Implement
     */
    protected function report(Test_Runner $runner_, array $xDebugReport_)
    {
      $testPaths=$runner_->getTestPaths();

      $classpaths=array();
      foreach(Runtime_Classloader::getClassloaders() as $classloader)
        $classpaths=array_merge($classpaths, $classloader->getClasspaths());

      $emmaReport=array();
      // TODO (CSH) Alter
      foreach($classpaths as $clazz=>$path)
      {
        if(isset($testPaths[$path]))
          continue;

        if($this->filterPathForPattern($path, $this->excludePattern))
          continue;

        $this->reportForClass($clazz, $path, $xDebugReport_, $emmaReport);
      }

      $document=new \DOMDocument('1.0', 'utf-8');
      $document->formatOutput=true;

      $report=$document->createElement('report');
      $document->appendChild($report);

      $stats=$document->createElement('stats');
      $report->appendChild($stats);

      $statsPackages=$document->createElement('packages');
      $statsPackages->setAttribute('value', count($this->m_packages));
      $stats->appendChild($statsPackages);

      $statsClasses=$document->createElement('classes');
      $statsClasses->setAttribute('value', count($this->m_classes));
      $stats->appendChild($statsClasses);

      $statsMethods=$document->createElement('methods');
      $statsMethods->setAttribute('value', count($this->m_methods));
      $stats->appendChild($statsMethods);

      $statsFiles=$document->createElement('srcfiles');
      $statsFiles->setAttribute('value', count($this->m_files));
      $stats->appendChild($statsFiles);

      $statsLines=$document->createElement('srclines');
      $statsLines->setAttribute('value', $this->m_lines);
      $stats->appendChild($statsLines);

      $data=$document->createElement('data');
      $report->appendChild($data);

      $all=$document->createElement('all');
      $all->setAttribute('name', 'all classes');
      $data->appendChild($all);

      $countLines=0;
      $countLinesCovered=0;
      $countClasses=0;
      $countClassesCovered=0;
      $countMethods=0;
      $countMethodsCovered=0;

      foreach($emmaReport as $package)
      {
        foreach($package as $files)
        {
          foreach($files as $clazz)
          {
            $countClasses++;

            $countLines+=$clazz['lines'];
            $countLinesCovered+=$clazz['covered'];

            if(0<$clazz['covered'])
              $countClassesCovered++;

            foreach($clazz['methods'] as $method)
            {
              $countMethods++;
              if(0<$method['covered'])
                $countMethodsCovered++;
            }
          }
        }
      }

      $this->appendCoverage($document, $all, 'class, %', $countClasses, $countClassesCovered);
      $this->appendCoverage($document, $all, 'method, %', $countMethods, $countMethodsCovered);
      $this->appendCoverage($document, $all, 'block, %', $countMethods, $countMethodsCovered);
      $this->appendCoverage($document, $all, 'line, %', $countLines, $countLinesCovered);

      foreach(array_keys($emmaReport) as $package)
        $this->appendPackage($document, $all, $package, $emmaReport[$package]);

      $document->save($this->m_buildPath);
    }

    protected function appendPackage(\DOMDocument $document_, \DOMElement $parent_, $package_, array $files_)
    {
      $package=$document_->createElement('package');
      $package->setAttribute('name', $package_);
      $parent_->appendChild($package);

      $countLines=0;
      $countLinesCovered=0;
      $countClasses=0;
      $countClassesCovered=0;
      $countMethods=0;
      $countMethodsCovered=0;

      foreach($files_ as $classes)
      {
        foreach($classes as $clazz)
        {
          $countClasses++;

          $countLines+=$clazz['lines'];
          $countLinesCovered+=$clazz['covered'];

          if(0<$clazz['covered'])
            $countClassesCovered++;

          foreach($clazz['methods'] as $method)
          {
            $countMethods++;
            if(0<$method['covered'])
              $countMethodsCovered++;
          }
        }
      }

      $this->appendCoverage($document_, $package, 'class, %', $countClasses, $countClassesCovered);
      $this->appendCoverage($document_, $package, 'method, %', $countMethods, $countMethodsCovered);
      $this->appendCoverage($document_, $package, 'block, %', $countMethods, $countMethodsCovered);
      $this->appendCoverage($document_, $package, 'line, %', $countLines, $countLinesCovered);

      foreach(array_keys($files_) as $file)
        $this->appendFile($document_, $package, $file, $files_[$file]);
    }

    protected function appendFile(\DOMDocument $document_, \DOMElement $parent_, $file_, array $classes_)
    {
      $file=$document_->createElement('srcfile');
      $file->setAttribute('name', $file_);
      $parent_->appendChild($file);


      $countLines=0;
      $countLinesCovered=0;
      $countClassesCovered=0;
      $countMethods=0;
      $countMethodsCovered=0;

      foreach($classes_ as $clazz)
      {
        $countLines+=$clazz['lines'];
        $countLinesCovered+=$clazz['covered'];

        if(0<$clazz['covered'])
          $countClassesCovered++;

        foreach($clazz['methods'] as $method)
        {
          $countMethods++;
          if(0<$method['covered'])
            $countMethodsCovered++;
        }
      }

      $this->appendCoverage($document_, $file, 'class, %', count($classes_), $countClassesCovered);
      $this->appendCoverage($document_, $file, 'method, %', $countMethods, $countMethodsCovered);
      $this->appendCoverage($document_, $file, 'block, %', $countMethods, $countMethodsCovered);
      $this->appendCoverage($document_, $file, 'line, %', $countLines, $countLinesCovered);

      foreach($classes_ as $clazzName=>$clazz)
        $this->appendClass($document_, $file, $clazzName, $clazz);
    }

    protected function appendClass(\DOMDocument $document_, \DOMElement $parent_, $clazzName_, array $clazz_)
    {
      $clazz=$document_->createElement('class');
      $clazz->setAttribute('name', $clazzName_);
      $parent_->appendChild($clazz);

      $countMethodsCovered=0;
      foreach($clazz_['methods'] as $method)
      {
        if(0<$method['covered'])
          $countMethodsCovered++;
      }

      $this->appendCoverage($document_, $clazz, 'class, %', 0==$clazz_['covered']?0:1, 1);
      $this->appendCoverage($document_, $clazz, 'method, %', count($clazz_['methods']), $countMethodsCovered);
      $this->appendCoverage($document_, $clazz, 'block, %', count($clazz_['methods']), $countMethodsCovered);
      $this->appendCoverage($document_, $clazz, 'line, %', $clazz_['lines'], $clazz_['covered']);

      foreach($clazz_['methods'] as $methodName=>$method)
        $this->appendMethod($document_, $clazz, $methodName, $method);
    }

    protected function appendMethod(\DOMDocument $document_, \DOMElement $parent_, $methodName_, array $method_)
    {
      $method=$document_->createElement('method');
      $method->setAttribute('name', $methodName_);
      $parent_->appendChild($method);

      $this->appendCoverage($document_, $method, 'method, %', 0==$method_['covered']?0:1, 1);
      $this->appendCoverage($document_, $method, 'block, %', 0==$method_['covered']?0:1, 1);
      $this->appendCoverage($document_, $method, 'line, %', $method_['lines'], $method_['covered']);
    }

    protected function appendCoverage(\DOMDocument $document_, \DOMElement $parent_, $type_, $valueTotal_, $valueCovered_)
    {
      $coverage=$document_->createElement('coverage');
      $coverage->setAttribute('type', $type_);
      $coverage->setAttribute('value', "{$this->getPercentage($valueTotal_, $valueCovered_)}%  ($valueCovered_/$valueTotal_)");
      $parent_->appendChild($coverage);
    }

    protected function reportForClass($clazz_, $clazzPath_, $xDebugReport_, &$emmaReport_)
    {
      $file=basename($clazzPath_);
      $package=$this->getPackageNameForClassPath($clazzPath_);

      $contents=file_get_contents($clazzPath_);
      $tokens=token_get_all($contents);
      $countTokens=count($tokens);

      if(1<count($chunks=explode('/', $clazz_)))
        $clazz_=trim(array_pop($chunks));

      $class=array();
      $methods=array();

      $depth=0;
      for($i=0; $i<$countTokens; $i++)
      {
        if('{'==$tokens[$i])
        {
          $depth++;

          continue;
        }

        if('}'==$tokens[$i])
        {
          $depth--;

          if(isset($classDepth) && $classDepth==$depth)
          {
            $j=$i;
            while($j>0)
            {
              if(is_array($tokens[--$j]))
                break;
            }

            $class['stop']=$tokens[$j][2];
          }
          else if(isset($methodDepth) && $methodDepth==$depth)
          {
            $line=null;

            $j=$i;
            while($j>0)
            {
              if(is_array($tokens[--$j]))
                break;
            }

            $methods[$method]['stop']=$tokens[$j][2];
            $method=null;
          }

          continue;
        }

        if(false===isset($tokens[$i+2]))
          continue;

        // FIXME (CSH) Support namespaces.
        if(in_array($tokens[$i][0], array(T_CLASS, T_INTERFACE)) && $clazz_==('Components\\'.$tokens[$i+2][1]))
        {
          $class=array('start'=>$tokens[$i][2]);
          $classDepth=$depth;
        }

        if(T_FUNCTION==$tokens[$i][0] && is_array($tokens[$i+2]))
        {
          $method=$tokens[$i+2][1];
          $methodDepth=$depth;

          $methods[$method]=array('start'=>$tokens[$i+2][2]);

          continue;
        }
      }

      if(!isset($class['start']) || !isset($class['stop']))
        return;

      foreach($methods as $methodName=>$lines)
      {
        $methods[$methodName]['covered']=0;

        if(!isset($lines['start']) || !isset($lines['stop']))
        {
          $methods[$methodName]['lines']=0;

          continue;
        }

        $methods[$methodName]['lines']=$lines['stop']-$lines['start'];
        if(isset($xDebugReport_[$clazzPath_]))
        {
          foreach(array_keys($xDebugReport_[$clazzPath_]) as $line)
          {
            if($line>=$lines['start'] && $line<=$lines['stop'])
              $methods[$methodName]['covered']++;
          }
        }

        $this->m_methods[$methodName]=$methodName;
      }

      if(false===array_key_exists($clazzPath_, $xDebugReport_))
      {
        $emmaReport_[$package][$file][$clazz_]=array(
          'lines'=>$class['stop']-$class['start'],
          'covered'=>0,
          'methods'=>$methods
        );
      }
      else
      {
        $emmaReport_[$package][$file][$clazz_]=array(
          'lines'=>$class['stop']-$class['start'],
          'covered'=>count($xDebugReport_[$clazzPath_]),
          'methods'=>$methods
        );
      }

      $this->m_packages[$package]=$package;
      $this->m_classes[$clazz_]=$clazz_;
      $this->m_lines+=$class['stop']-$class['start'];
      $this->m_files[$clazzPath_]=$clazzPath_;
    }

    protected function getPackageNameForClassPath($clazzPath_)
    {
      $clazzPath=realpath(dirname($clazzPath_));
      $rootPath=realpath($this->m_rootPath);

      return strtolower(str_replace(
        array($rootPath.'/', '/'),
        array('', '.'),
        $clazzPath)
      );
    }

    protected function getPercentage($x_, $y_)
    {
      if(0==$x_ || 0==$y_)
        return 0;

      return round((100/$x_)*$y_, 2);
    }

    protected function filterPathForPattern($path_, $pattern_=null)
    {
      if(null===$pattern_)
        return false;

      return 1==preg_match($pattern_, $path_);
    }
    //--------------------------------------------------------------------------
  }
?>
