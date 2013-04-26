<?php


namespace Components;


  /**
   * Test_Runner
   *
   * @package net.evalcode.components
   * @subpackage test
   *
   * @author evalcode.net
   */
  abstract class Test_Runner implements Runtime_Error_Handler
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
    public $typeTestCase;
    /**
     * @var string
     */
    public $typeTestSuite;

    /**
     * @var Test_Output
     */
    public $output;

    public static $fileExtensionsPhp=array('php', 'phps');
    //--------------------------------------------------------------------------


    // STATIC ACCESSORS
    /**
     * @return Test_Runner
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
        throw new Test_Exception('test/runner', sprintf(
          'Passed class must extend %1$s [class: %2$s].', __CLASS__, $type_
        ));
      }

      static::registerAnnotations();
      self::$m_instance=new $type_();

      Runtime::addRuntimeErrorHandler(self::$m_instance);

      return self::$m_instance;
    }

    /**
     * @return Test_Runner
     */
    public static function get()
    {
      return self::$m_instance;
    }
    //--------------------------------------------------------------------------


    // ACCESSORS
    public function run()
    {
      if(null===$this->m_buildPath)
        throw new Exception_IllegalState('test/runner', 'Build path must be specified.');
      if(null===$this->m_testRootPath)
        throw new Exception_IllegalState('test/runner', 'Test root path must be specified.');

      if(null===$this->output)
        $this->output=new Test_Output_Null();

      if(null===$this->m_result)
        $this->m_result=new Test_Result();

      $this->invokeLifecycleListeners(Test_LifecycleListener::INITIALIZATION);
      $this->initialize();
      $this->discoverTests($this->m_testRootPath);
      $this->getTempPath()->create();

      $this->invokeLifecycleListeners(Test_LifecycleListener::EXECUTION);
      $this->execute();

      $this->invokeResultHandler($this->m_result);

      $this->invokeLifecycleListeners(Test_LifecycleListener::TERMINATION);
      $this->getTempPath()->delete(true);
    }

    public function addClass($path_)
    {
      if(false===$this->validSourceFile($path_))
        throw new Exception_IllegalArgument('test/runner', 'Passed path does not point to a valid source file.');

      require_once $path_;
    }

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

    public function addTestSuite($class_)
    {
      if(false===array_key_exists($class_, $this->m_suitesAdded))
        $this->m_suitesAdded[$class_]=array_push($this->m_suites, $class_);
    }

    public function addTestPathToClassPath($namespace_=null, $path_)
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
     * @return Io_Path
     */
    public function getTempPath()
    {
      return Io::path($this->m_buildPath)->tmp;
    }

    /**
     * @return Io_Path
     */
    public function getBuildPath()
    {
      return Io::path($this->m_buildPath);
    }

    public function setBuildPath($path_)
    {
      $this->m_buildPath=$path_;
    }

    /**
     * @return Io_Path
     */
    public function getTestRootPath()
    {
      return Io::path($this->m_testRootPath);
    }

    public function setTestRootPath($path_)
    {
      if(false===$this->validPath($path_))
        throw new Exception_IllegalArgument('test/runner', 'Invalid test root path.');

      $this->m_testRootPath=$path_;
    }

    public function getTestSuite()
    {
      return $this->m_testSuite;
    }

    public function setTestSuite($class_)
    {
      $this->m_testSuite=$class_;
    }

    public function getTestPaths()
    {
      return $this->m_testPaths;
    }

    /**
     * @return Test_Result
     */
    public function getResult()
    {
      return $this->m_result;
    }

    /**
     * @param Test_Result $result_
     */
    public function setResult(Test_Result $result_)
    {
      $this->m_result=$result_;
    }

    public function getResultHandler()
    {
      return $this->m_resultHandler;
    }

    public function addResultHandler(Test_Result_Handler $resultHandler_)
    {
      array_push($this->m_resultHandler, $resultHandler_);
    }

    public function invokeResultHandler(Test_Result $result_)
    {
      foreach($this->m_resultHandler as $resultHandler)
        $resultHandler->handleResult($result_);
    }

    public function setInjector(Injector $injector_)
    {
      $this->m_injector=$injector_;
    }

    /**
     * @return Injector
     */
    public function getInjector()
    {
      if(null===$this->m_injector)
        $this->m_injector=Injector::create($this->getBindingModule());

      return $this->m_injector;
    }

    /**
     * @return Binding_Module
     */
    public function getBindingModule()
    {
      if(null===$this->m_bindingModule)
        $this->m_bindingModule=new Test_Binding_Module();

      return $this->m_bindingModule;
    }

    public function setBindingModule(Binding_Module $bindingModule_)
    {
      $this->m_bindingModule=$bindingModule_;
    }

    public function addLifecycleListener(Test_LifecycleListener $lifecycleListener_)
    {
      array_push($this->m_lifecycleListeners, $lifecycleListener_);
    }

    public function removeLifecycleListener(Test_LifecycleListener $lifecycleListener_)
    {
      $lifecycleListenerId=$lifecycleListener_->hashCode();

      foreach($this->m_lifecycleListeners as $key=>$lifecycleListener)
      {
        if($lifecycleListenerId==$lifecycleListener->hashCode())
          unset($this->m_lifecycleListeners[$key]);
      }
    }
    //--------------------------------------------------------------------------


    // OVERRIDES/IMPLEMENTS
    public function onError(Runtime_ErrorException $e_)
    {
      die("Tests Failed: ".$e_->getMessage());
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @var boolean
     */
    protected static $m_annotationsRegistered=false;

    /**
     * @var Test_LifecycleListener|array
     */
    protected $m_lifecycleListeners=array();
    /**
     * @var Test_Result_Handler|array
     */
    protected $m_resultHandler=array();
    /**
     * @var string|array
     */
    protected $m_suites=array();
    /**
     * @var string|array
     */
    protected $m_suitesAdded=array();
    /**
     * @var Binding_Module
     */
    protected $m_bindingModule;
    /**
     * @var Injector
     */
    protected $m_injector;
    /**
     * @var string
     */
    protected $m_buildPath;
    /**
     * @var Test_Result
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
    protected $m_testPaths=array();

    /**
     * @var Test_Runner
     */
    private static $m_instance;
    //-----


    protected abstract function getTitle();

    protected abstract function initialize();
    protected abstract function execute();

    protected function invokeLifecycleListeners($method_)
    {
      foreach($this->m_lifecycleListeners as $listener)
        $listener->{$method_}($this);
    }

    protected function discoverTestPaths($path_)
    {
      if(is_file($path_.'/.manifest'))
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
          $this->addTestPathToClassPath($manifest->getNamespace(Manifest::SOURCE_TYPE_TEST_UNIT), $path);
      }

      $iterator=new \RecursiveIteratorIterator(
        new \RecursiveDirectoryIterator($path_),
        \RecursiveIteratorIterator::CHILD_FIRST
      );

      foreach($iterator as $entry)
      {
        if($entry->isDir())
          $this->discoverTestPaths($entry->getPathname());
      }
    }

    public function __autoload($type_)
    {
      if(isset($this->m_testPaths[$type_]))
        require_once $this->m_testPaths[$type_];
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
