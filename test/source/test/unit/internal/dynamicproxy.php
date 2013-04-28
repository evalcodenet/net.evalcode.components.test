<?php


namespace Components;


  /**
   * Test_Unit_Internal_DynamicProxy
   *
   * @package net.evalcode.components
   * @subpackage test.unit.internal
   *
   * @author evalcode.net
   */
  class Test_Unit_Internal_DynamicProxy
  {
    // CONSTRUCTION
    public function __construct($class_, Test_Unit_Runner $runner_=null)
    {
      $this->m_runner=$runner_;
      $this->m_class=new \ReflectionClass($class_);

      $rootPath=realpath($runner_->getTestRootPath());
      $testClassPath=realpath($this->m_class->getFileName());

      $this->m_path=str_replace("$rootPath/", '', $testClassPath);

      $annotations=Annotations::get($class_);
      foreach($this->m_class->getMethods(\ReflectionMethod::IS_PUBLIC) as $method)
      {
        if($ignoreAnnotation=$annotations->getMethodAnnotation($method->name, Annotation_Ignore::NAME))
        {
          if(null!==$ignoreAnnotation->value
            && false===@method_exists($this->m_class->name, $ignoreAnnotation->value))
          {
            throw new Exception_IllegalArgument('test/unit/internal/dynamicproxy', sprintf(
              'Illegal argument in @%1$s(value=%4$s) %2$s::%3$s().',
                Annotation_Ignore::NAME,
                $this->m_class->name,
                $method->name,
                $ignoreAnnotation->value
            ));
          }
          else if(null!==$ignoreAnnotation->value)
          {
            if(false!==($reason=call_user_func(array($this->m_class->name, $ignoreAnnotation->value))))
            {
              if(is_string($reason))
                $this->m_skippedTestsReasons[$method->name]=$reason;

              $this->m_skippedTests[$method->name]=true;
            }
          }
          else
          {
            $this->m_skippedTests[$method->name]=true;
          }
        }

        if($testAnnotation=$annotations->getMethodAnnotation($method->name, Annotation_Test::NAME))
        {
          array_push($this->m_tests, $method);

          $expectedFail=$testAnnotation->expectedFail;
          $expectedException=$testAnnotation->expectedException;

          if(null!==$expectedException && false===@class_exists($expectedException))
          {
            throw new Exception_IllegalArgument('test/unit/internal/dynamicproxy', sprintf(
              'Illegal argument in @%1$s(expectedException=%4$s) %2$s::%3$s().',
                Annotation_Test::NAME,
                $this->m_class->name,
                $method->name,
                $expectedException
            ));
          }

          if(null!==$expectedException)
            $this->m_exceptionsExpected[$method->name]=$expectedException;

          if(null!=$expectedFail && 'false'!=trim(strtolower($expectedFail)))
            $this->m_failedExpected[$method->name]=true;
        }

        /*
         * Search for before-/AfterMethod/-Class/-Suite annotations
         * Only the first occurence per method will be respected.
         */
        foreach(self::$m_staticAnnotations as $staticAnnotation)
        {
          if($annotations->hasMethodAnnotation($method->name, $staticAnnotation))
          {
            $this->m_mappedMethods[$staticAnnotation]=$method->name;

            break;
          }
        }

        $this->m_methods[$method->name]=$method;

        if($profileAnnotation=$annotations->getMethodAnnotation($method->name, Annotation_Profile::NAME))
        {
          $this->m_profileMethods[$method->name]=null;
          if(Annotation_Profile::VALUE_FORK==$profileAnnotation->value)
            $this->m_profileMethodsForked[$method->name]=null;
        }
      }
    }

    public function isInstanceOf($class_)
    {
      return $this->m_class->isSubclassOf($class_) || $this->m_class->implementsInterface($class_);
    }
    //--------------------------------------------------------------------------


    // ACCESSORS
    /**
     * @return \ReflectionClass
     */
    public function getClass()
    {
      return $this->m_class;
    }

    public function createInstance()
    {
      if(null===$this->m_instance)
      {
        $this->m_instance=$this->m_class->newInstance();
        if(null!==$this->m_runner && null!==$this->m_runner->getInjector())
          $this->m_runner->getInjector()->injectMembers($this->m_instance);
      }

      return $this->m_instance;
    }
    public function destroyInstance()
    {
      unset($this->m_instance);

      $this->m_instance=null;
    }

    public function getTests()
    {
      return $this->m_tests;
    }

    public function countTests()
    {
      return count($this->m_tests);
    }

    public function skipTest(\ReflectionMethod $method_)
    {
      return array_key_exists($method_->name, $this->m_skippedTests);
    }

    public function skipTestReason(\ReflectionMethod $method_)
    {
      if(isset($this->m_skippedTestsReasons[$method_->name]))
        return $this->m_skippedTestsReasons[$method_->name];

      return null;
    }

    public function hasOutput(\ReflectionMethod $method_)
    {
      return array_key_exists($method_->name, $this->m_output);
    }
    public function getOutput(\ReflectionMethod $method_)
    {
      if(false===$this->hasOutput($method_))
        return null;

      return $this->m_output[$method_->name];
    }

    public function isFailed(\ReflectionMethod $method_)
    {
      if(false===array_key_exists($method_->name, $this->m_failed))
        return true;

      return $this->m_failed[$method_->name];
    }

    public function isFailedExpected(\ReflectionMethod $method_)
    {
      return array_key_exists($method_->name, $this->m_failedExpected);
    }

    /**
     * @return Test_Result_Exception
     */
    public function getException(\ReflectionMethod $method_)
    {
      if(false===isset($this->m_exceptions[$method_->name]))
        return null;

      return $this->m_exceptions[$method_->name];
    }

    /**
     * @return Test_Result_Exception
     */
    public function getExpectedExceptionClass(\ReflectionMethod $method_)
    {
      if(false===isset($this->m_exceptionsExpected[$method_->name]))
        return null;

      return $this->m_exceptionsExpected[$method_->name];
    }

    public function getProfilerResult(\ReflectionMethod $method_)
    {
      if(false===isset($this->m_profileMethods[$method_->name]))
        return null;

      return $this->m_profileMethods[$method_->name];
    }

    /**
     * @return \ReflectionMethod
     */
    public function getMethod($name_)
    {
      if(isset($this->m_mappedMethods[$name_])
        && isset($this->m_methods[$this->m_mappedMethods[$name_]]))
        return $this->m_methods[$this->m_mappedMethods[$name_]];
      else if(isset($this->m_methods[$name_]))
        return $this->m_methods[$name_];
      return null;
    }

    public function getPath()
    {
      return $this->m_path;
    }
    //--------------------------------------------------------------------------


    // OVERRIDES/IMPLEMENTS
    public function __call($key_, $args_)
    {
      if(null===($method=$this->getMethod($key_)))
        return null;

      // FIXME Re-integrate profiling.
      $profileMethod='profileCall';
      //if(Debug_Profiler::forkSupported() && array_key_exists($method->name, $this->m_profileMethodsForked))
        //$profileMethod='profileCallForked';

      ob_start();

      $returnValue=null;
/*
      if(true===array_key_exists($method->name, $this->m_profileMethods))
      {
        $this->m_profileMethods[$method->name]=Debug_Profiler::$profileMethod(
          array($this->m_instance, $this->m_methods[$method->name]->name)
        );

        $this->m_exceptions[$method->name]=
          $this->m_profileMethods[$method->name]->exception();

        $returnValue=$this->m_profileMethods[$method->name]->returnValue();
      }
      else
      {*/
        try
        {
          if($this->m_methods[$method->name]->isStatic())
            $returnValue=$this->m_methods[$method->name]->invoke($this->m_class);
          else
            $returnValue=$this->m_instance->{$this->m_methods[$method->name]->name}();
        }
        catch(\Exception $e)
        {
          $this->m_exceptions[$method->name]=Exception_Flat::create($e);
        }
     // }

      $this->m_failed[$method->name]=true;

      if(false===isset($this->m_exceptionsExpected[$method->name]) && false===isset($this->m_exceptions[$method->name]))
      {
        $this->m_failed[$method->name]=false;
      }
      else if(isset($this->m_exceptionsExpected[$method->name]) && isset($this->m_exceptions[$method->name]))
      {
        if($this->m_exceptionsExpected[$method->name]==$this->m_exceptions[$method->name]->type)
        {
          $this->m_failed[$method->name]=false;
        }
        else
        {
          $exceptionReflection=new \ReflectionClass($this->m_exceptions[$method->name]);
          if($exceptionReflection->isSubclassOf($this->m_exceptionsExpected[$method->name]))
            $this->m_failed[$method->name]=false;
        }
      }

      if(trim($output=ob_get_clean()))
        $this->m_output[$method->name]=$output;

      return $returnValue;
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    private static $m_staticAnnotations=array(
      Annotation_AfterClass::NAME,
      Annotation_AfterMethod::NAME,
      Annotation_AfterSuite::NAME,
      Annotation_BeforeClass::NAME,
      Annotation_BeforeMethod::NAME,
      Annotation_BeforeSuite::NAME
    );

    private $m_output=array();
    private $m_exceptions=array();
    private $m_exceptionsExpected=array();
    private $m_failedExpected=array();
    private $m_failed=array();
    private $m_methods=array();
    private $m_mappedMethods=array();
    private $m_profileMethods=array();
    private $m_profileMethodsForked=array();
    private $m_skippedTests=array();
    private $m_skippedTestsReasons=array();
    private $m_tests=array();
    private $m_class;
    private $m_instance;
    private $m_path;
    private $m_runner;
    //--------------------------------------------------------------------------
  }
?>
