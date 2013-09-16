<?php


namespace Components;


  /**
   * Test_Runner
   *
   * @package net.evalcode.components.test
   *
   * @author evalcode.net
   */
  abstract class Test_Runner
  {
    // PROPERTIES
    /**
     * @var string PCRE/Perl-compatible regex pattern for test path inclusion.
     */
    public $includePattern;
    /**
     * @var string PCRE/Perl-compatible regex pattern for test path exclusion.
     */
    public $excludePattern;
    /**
     * @var string
     */
    public $configuration;
    /**
     * @var string
     */
    public $typeTestCase;
    /**
     * @var string
     */
    public $typeTestSuite;
    /**
     * @var \Components\Test_Output
     */
    public $output;
    /**
     * @var string[]
     */
    public static $fileExtensionsPhp=array('php', 'phps');
    //--------------------------------------------------------------------------


    // STATIC ACCESSORS
    /**
     * @param string $type_
     *
     * @return \Components\Test_Runner
     */
    public static function create($type_=null)
    {
      if(null!==self::$m_instance)
        throw new Exception_IllegalState('test/runner', 'Instance already exists.');

      if(null===$type_)
        $type_=get_called_class();

      $type=new \ReflectionClass($type_);
      if(false===$type->isSubclassOf(__CLASS__))
      {
        throw new Exception_IllegalArgument('test/runner', sprintf(
          'Passed class must extend %1$s [class: %2$s].', __CLASS__, $type_
        ));
      }

      static::registerAnnotations();
      self::$m_instance=new $type_();

      return self::$m_instance;
    }

    /**
     * @return \Components\Test_Runner
     */
    public static function get()
    {
      return self::$m_instance;
    }
    //--------------------------------------------------------------------------


    // ACCESSORS
    /**
     * @throws Exception_IllegalState
     */
    public function run()
    {
      if(null!==$this->configuration)
        include_once $this->configuration;

      if(null!==$this->m_buildPath)
        $this->m_buildPath=realpath($this->m_buildPath);

      if(!Io::directoryCreate($this->m_buildPath))
        throw new Exception_IllegalState('test/runner', 'Missing/invalid build path parameter.');

      if(null!==$this->m_testRootPath)
        $this->m_testRootPath=realpath($this->m_testRootPath);

      if(!Io::directoryCreate($this->m_testRootPath))
        throw new Exception_IllegalState('test/runner', 'Missing/invalid test root path.');

      if(null===$this->output)
        $this->output=new Test_Output_Null();

      if(null===$this->m_result)
        $this->m_result=new Test_Result();

      $this->invokeListeners(Test_Listener::INITIALIZATION);
      $this->initialize();
      $this->discoverTests($this->m_testRootPath);

      $tmp=$this->getTempPath();
      $tmp->create();

      $this->invokeListeners(Test_Listener::EXECUTION);
      $this->execute();

      $this->invokeResultHandler($this->m_result);
      $this->invokeListeners(Test_Listener::TERMINATION);

      $tmp->delete(true);
    }

    /**
     * @param string $path_
     *
     * @throws \Components\Exception_IllegalArgument
     */
    public function addClass($path_)
    {
      if(false===$this->validSourceFile($path_))
        throw new Exception_IllegalArgument('test/runner', 'Passed path does not point to a valid source file.');

      require_once $path_;
    }

    /**
     * @param string $path_
     */
    public function addPathToClassPath($path_)
    {
      $iterator=new \RecursiveIteratorIterator(
        new \RecursiveDirectoryIterator($path_),
        \RecursiveIteratorIterator::SELF_FIRST
      );

      foreach($iterator as $entry)
      {
        if(false===$this->validSourceFile($entry->getPathname()))
          continue;

        require_once $entry->getPathname();
      }
    }

    /**
     * @param string $class_
     */
    public function addTestSuite($class_)
    {
      if(false===array_key_exists($class_, $this->m_suitesAdded))
        $this->m_suitesAdded[$class_]=array_push($this->m_suites, $class_);
    }

    /**
     * @param string $namespace_
     * @param string $path_
     */
    public function addTestPathToClassPath($path_, $namespace_=null)
    {
      $iterator=new \RecursiveIteratorIterator(
        new \RecursiveDirectoryIterator($path_),
          \RecursiveIteratorIterator::SELF_FIRST
      );

      foreach($iterator as $entry)
      {
        if(false===$this->filterFile($entry->getPathname()))
          continue;

        $matches=array();
        preg_match_all('/\n\s*(?:(?:abstract|final)+\s+)*(?:class|interface|trait)\s*(\w+)\s/',
          file_get_contents($entry->getPathname()), $matches
        );

        if(isset($matches[1]))
        {
          foreach($matches[1] as $type)
            $this->m_testPaths[null===$namespace_?$type:"$namespace_\\$type"]=$entry->getPathname();
        }
      }
    }

    /**
     * @return \Components\Io_Path
     */
    public function getTempPath()
    {
      return Io::path($this->getBuildPath())->tmp;
    }

    /**
     * @return string
     */
    public function getBuildPath()
    {
      return $this->m_buildPath;
    }

    /**
     * @param string $path_
     */
    public function setBuildPath($path_)
    {
      $this->m_buildPath=$path_;
    }

    /**
     * @return string
     */
    public function getTestRootPath()
    {
      return $this->m_testRootPath;
    }

    /**
     * @param string $path_
     *
     * @throws \Components\Exception_IllegalArgument
     */
    public function setTestRootPath($path_)
    {
      if(false===$this->validPath($path_))
        throw new Exception_IllegalArgument('test/runner', 'Invalid test root path.');

      $this->m_testRootPath=$path_;
    }

    /**
     * @return string
     */
    public function getTestSuite()
    {
      return $this->m_testSuite;
    }

    /**
     * @param string $class_
     */
    public function setTestSuite($class_)
    {
      $this->m_testSuite=$class_;
    }

    /**
     * @return string[]
     */
    public function getTestPaths()
    {
      return $this->m_testPaths;
    }

    /**
     * @return \Components\Test_Result
     */
    public function getResult()
    {
      return $this->m_result;
    }

    /**
     * @param \Components\Test_Result $result_
     */
    public function setResult(Test_Result $result_)
    {
      $this->m_result=$result_;
    }

    /**
     * @return \Components\Test_Result_Handler
     */
    public function getResultHandlers()
    {
      return $this->m_resultHandlers;
    }

    /**
     * @param \Components\Test_Result_Handler $resultHandler_
     */
    public function addResultHandler(Test_Result_Handler $resultHandler_)
    {
      array_push($this->m_resultHandlers, $resultHandler_);
    }

    /**
     * @param \Components\Test_Result $result_
     */
    public function invokeResultHandler(Test_Result $result_)
    {
      foreach($this->m_resultHandlers as $resultHandler)
        $resultHandler->handleResult($result_);
    }

    /**
     * @return \Components\Injector
     */
    public function getInjector()
    {
      if(null===$this->m_injector)
        $this->m_injector=Injector::create($this->getBindingModule());

      return $this->m_injector;
    }

    /**
     * @param \Components\Injector $injector_
     */
    public function setInjector(Injector $injector_)
    {
      $this->m_injector=$injector_;
    }

    /**
     * @return \Components\Binding_Module
     */
    public function getBindingModule()
    {
      if(null===$this->m_bindingModule)
        $this->m_bindingModule=new Test_Binding_Module();

      return $this->m_bindingModule;
    }

    /**
     * @param \Components\Binding_Module $bindingModule_
     */
    public function setBindingModule(Binding_Module $bindingModule_)
    {
      $this->m_bindingModule=$bindingModule_;
    }

    /**
     * @param \Components\Test_Listener $listener_
     */
    public function addListener(Test_Listener $listener_)
    {
      array_push($this->m_listeners, $listener_);
    }

    /**
     * @param \Components\Test_Listener $listener_
     */
    public function removeListener(Test_Listener $listener_)
    {
      $listenerId=$listener_->hashCode();

      /* @var $listener Test_Listener */
      foreach($this->m_listeners as $key=>$listener)
      {
        if($listenerId===$listener->hashCode())
          unset($this->m_listeners[$key]);
      }
    }
    //--------------------------------------------------------------------------


    // OVERRIDES
    /**
     * @see \Components\Object::hashCode() \Components\Object::hashCode()
     */
    public function hashCode()
    {
      return object_hash($this);
    }

    /**
     * @see \Components\Object::__toString() \Components\Object::__toString()
     */
    public function __toString()
    {
      return sprintf('%s@%s{}', __CLASS__, $this->hashCode());
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @var boolean
     */
    protected static $m_annotationsRegistered=false;
    /**
     * @var \Components\Test_Runner
     */
    private static $m_instance;
    /**
     * @var \Components\Test_Listener[]
     */
    protected $m_listeners=array();
    /**
     * @var \Components\Test_Result_Handler[]
     */
    protected $m_resultHandlers=array();
    /**
     * @var string[]
     */
    protected $m_suites=array();
    /**
     * @var string[]
     */
    protected $m_suitesAdded=array();
    /**
     * @var string[]
     */
    protected $m_testPaths=array();
    /**
     * @var \Components\Binding_Module
     */
    protected $m_bindingModule;
    /**
     * @var \Components\Injector
     */
    protected $m_injector;
    /**
     * @var string
     */
    protected $m_buildPath;
    /**
     * @var \Components\Test_Result
     */
    protected $m_result;
    /**
     * @var string
     */
    protected $m_testRootPath;
    /**
     * @var string
     */
    protected $m_testSuite;
    //-----


    protected abstract function getTitle();

    protected abstract function initialize();
    protected abstract function execute();


    protected function discoverTestPaths($path_)
    {
      if(is_file("$path_/.manifest"))
      {
        try
        {
          $manifest=Manifest::forComponent(basename($path_));
        }
        catch(Runtime_Exception $e)
        {
          return;
        }

        if(is_dir($path=$manifest->getClasspath(Manifest::SOURCE_TYPE_TEST_UNIT)))
          $this->addTestPathToClassPath($path, $manifest->getNamespace(Manifest::SOURCE_TYPE_TEST_UNIT));
      }

      $iterator=new \RecursiveIteratorIterator(
        new \RecursiveDirectoryIterator($path_),
        \RecursiveIteratorIterator::CHILD_FIRST
      );

      foreach($iterator as $entry)
      {
        if($entry->isDir() && 0!==strpos($entry->getBasename(), '.'))
          $this->discoverTestPaths($entry->getRealpath());
      }
    }

    protected function discoverTests($path_)
    {
      $this->discoverTestPaths($path_);

      spl_autoload_register(array($this, '__autoload'));

      foreach($this->m_testPaths as $clazz=>$path)
      {
        $type=new \ReflectionClass($clazz);

        if($type->isSubclassOf($this->typeTestSuite))
          $this->addTestSuite($clazz);
      }
    }

    protected function filterFile($filePath_)
    {
      if(null!==$this->excludePattern)
      {
        if($this->filterFileForPattern($filePath_, $this->excludePattern))
          return false;
      }

      if(null!==$this->includePattern)
      {
        if(false===$this->filterFileForPattern($filePath_, $this->includePattern))
          return false;
      }

      return $this->validSourceFile($filePath_);
    }

    protected function filterFileForPattern($filePath_, $pattern_)
    {
      return 1===(int)preg_match($pattern_, $filePath_);
    }

    protected function validPath($path_)
    {
      return Io_Path::resolve($path_)->exists();
    }

    protected function validSourceFile($path_)
    {
      return in_array(strtolower(substr($path_, strrpos($path_, '.')+1)), self::$fileExtensionsPhp);
    }

    protected function invokeListeners($method_)
    {
      foreach($this->m_listeners as $listener)
        $listener->{$method_}($this);
    }

    public function __autoload($type_)
    {
      if(false===isset($this->m_testPaths[$type_]))
        return false;

      require_once $this->m_testPaths[$type_];

      return true;
    }


    // HELPERS
    protected static function registerAnnotations()
    {
      if(false===self::$m_annotationsRegistered)
      {
        Annotations::registerAnnotations(array(
          Annotation_AfterClass::NAME=>Annotation_AfterClass::TYPE,
          Annotation_AfterMethod::NAME=>Annotation_AfterMethod::TYPE,
          Annotation_AfterSuite::NAME=>Annotation_AfterSuite::TYPE,
          Annotation_BeforeClass::NAME=>Annotation_BeforeClass::TYPE,
          Annotation_BeforeMethod::NAME=>Annotation_BeforeMethod::TYPE,
          Annotation_BeforeSuite::NAME=>Annotation_BeforeSuite::TYPE,
          Annotation_Ignore::NAME=>Annotation_Ignore::TYPE,
          Annotation_Profile::NAME=>Annotation_Profile::TYPE,
          Annotation_Test::NAME=>Annotation_Test::TYPE
        ));

        self::$m_annotationsRegistered=true;
      }
    }
    //--------------------------------------------------------------------------
  }
?>
