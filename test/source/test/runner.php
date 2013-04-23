<?php


  /**
   * Test_Runner
   *
   * @package net.evalcode.components
   * @subpackage test
   *
   * @since 1.0
   * @access public
   *
   * @author Carsten Schipke <carsten.schipke@evalcode.net>
   * @copyright Copyright (C) 2012 evalcode.net
   * @license GNU General Public License 3
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
     * @var string Path schema for test path inclusion.
     */
    public $includePathSchema;

    /**
     * @var string
     */
    public $typeTestCase;
    /**
     * @var string
     */
    public $typeTestSuite;

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

      $type=new ReflectionClass($type_);
      if(false===$type->isSubclassOf(__CLASS__))
      {
        throw new Test_Exception('test/runner', sprintf(
          'Passed class must extend %1$s [class: %2$s].', __CLASS__, $type_
        ));
      }

      return self::$m_instance=new $type_();
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
      $this->configure();
      $this->initialize();

      $this->runImpl();
      $this->invokeResultHandler($this->m_result);
    }

    public function configure()
    {
      if($this->m_configured)
        return;

      $this->setResult(new Test_Result());

      if(null!==$this->m_configuration)
        include $this->m_configuration;

      // search tests in project root path if test root path is not set
      if(null===$this->m_testRootPath && null!==$this->m_componentsRootPath)
        $this->setTestRootPath($this->m_componentsRootPath);

      $this->m_configured=true;
    }

    public function initialize()
    {
      if($this->m_initialized)
        return;

      static::registerAnnotations();

      $this->createBuildPath();
      $this->initializeImpl();

      $this->m_initialized=true;
    }

    public function setConfiguration($path_)
    {
      $this->m_configuration=$path_;
    }

    public function addClass($path_)
    {
      if(false===$this->validSourceFile($path_))
        throw new Exception_IllegalArgument('test/runner', 'Passed path does not point to a valid source file.');

      require_once $path_;
    }

    public function addTestSuite($class_)
    {
      if(false===array_key_exists($class_, $this->m_suitesAdded))
        $this->m_suitesAdded[$class_]=array_push($this->m_suites, $class_);
    }

    public function addTestPathToClassPath($path_)
    {
      $iterator=new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($path_),
          RecursiveIteratorIterator::SELF_FIRST
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
          $declared=array_flip(get_declared_classes());

          foreach($matches[1] as $type)
          {
            if(false===isset($declared[$type]))
              require_once $entry->getPathname();

            $this->m_testPaths[$type]=$entry->getPathname();
          }
        }
      }
    }

    public function addPathToClassPath($path_)
    {
      $iterator=new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($path_),
          RecursiveIteratorIterator::SELF_FIRST
      );

      foreach($iterator as $entry)
      {
        if(false===$this->validSourceFile($entry->getPathname()))
          continue;

        require_once $entry->getPathname();
      }
    }

    public function setComponentsRootPath($path_)
    {
      if(false===$this->validPath($path_))
        throw new Exception_IllegalArgument('test/runner', 'Invalid project root path.');

      $this->m_componentsRootPath=$path_;
    }

    public function getComponentsRootPath()
    {
      return $this->m_componentsRootPath;
    }

    public function setBuildPath($path_)
    {
      $this->m_buildPath=$path_;
    }

    public function getBuildPath()
    {
      return $this->m_buildPath;
    }

    public function setTestRootPath($path_)
    {
      if(false===$this->validPath($path_))
        throw new Exception_IllegalArgument('test/runner', 'Invalid test root path.');

      $this->m_testRootPath=$path_;
    }

    public function getTestRootPath()
    {
      return $this->m_testRootPath;
    }

    public function setTestSuite($class_)
    {
      $this->m_testSuite=$class_;
    }

    public function getTestSuite()
    {
      return $this->m_testSuite;
    }

    public function setRootPath($path_)
    {
      if(false===$this->validPath($path_))
        throw new Exception_IllegalArgument('test/runner', 'Invalid root path.');

      $this->m_rootPath=$path_;
    }

    public function getRootPath()
    {
      return $this->m_rootPath;
    }

    /**
     * @param Test_Result $result_
     */
    public function setResult(Test_Result $result_)
    {
      $this->m_result=$result_;
    }

    /**
     * @return Test_Result
     */
    public function getResult()
    {
      return $this->m_result;
    }

    public function addResultHandler(Test_Result_Handler $resultHandler_)
    {
      array_push($this->m_resultHandler, $resultHandler_);
    }

    public function getResultHandler()
    {
      return $this->m_resultHandler;
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

    public function setBindingModule(Binding_Module $bindingModule_)
    {
      $this->m_bindingModule=$bindingModule_;
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

    public function getTestPaths()
    {
      return $this->m_testPaths;
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
     * @var boolean
     */
    protected $m_configured=false;
    /**
     * @var boolean
     */
    protected $m_initialized=false;
    /**
     * @var Binding_Module
     */
    protected $m_bindingModule;
    /**
     * @var string
     */
    protected $m_configuration;
    /**
     * @var Injector
     */
    protected $m_injector;
    /**
     * @var string
     */
    protected $m_buildPath;
    /**
     * @var string
     */
    protected $m_componentsRootPath;
    /**
     * @var Test_Result
     */
    protected $m_result;
    /**
     * @var string
     */
    protected $m_rootPath;
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

    protected abstract function runImpl();
    protected abstract function initializeImpl();

    protected function invokeLifecycleListeners($method_)
    {
      foreach($this->m_lifecycleListeners as $listener)
        $listener->{$method_}($this);
    }

    protected function createBuildPath()
    {
      return Io::createDirectory($this->m_buildPath);
    }

    protected function discoverTests()
    {
      $iterator=new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($this->m_testRootPath),
          RecursiveIteratorIterator::SELF_FIRST
      );

      if(@is_dir($path=$this->m_testRootPath.'/'.$this->includePathSchema))
        $this->addTestPathToClassPath($path);
      else if(@is_dir($path=$this->m_testRootPath) && false!==strpos($path, $this->includePathSchema))
        $this->addTestPathToClassPath($path);

      foreach($iterator as $entry)
      {
        if(false===$entry->isDir())
          continue;

        if(@is_dir($path=$entry->getRealPath().'/'.$this->includePathSchema))
          $this->addTestPathToClassPath($path);
      }

      foreach(get_declared_classes() as $declaredClazz)
      {
        $reflectionClazz=new ReflectionClass($declaredClazz);

        if($reflectionClazz->isSubclassOf($this->typeTestSuite))
        {
          if(null===$this->m_testSuite || $this->m_testSuite==$declaredClazz)
            $this->addTestSuite($declaredClazz);
        }
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
