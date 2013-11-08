<?php


namespace Components;


  /**
   * Mock
   *
   * @api
   * @package net.evalcode.components.test
   *
   * @author evalcode.net
   */
  class Mock
  {
    // PROPERTIES
    /**
     * @var \ReflectionClass
     */
    public $mockType;
    //--------------------------------------------------------------------------


    // ACCESSORS
    /**
     * @return Mock_Stub
     */
    public function when($name_)
    {
      if(false===$this->mockType->hasMethod($name_))
      {
        throw new Exception_IllegalArgument('mock',
          'Unable to mock non-existing method "'.$name_.'".'
        );
      }

      return $this->m_stubs[$name_]=new Mock_Stub();
    }

    public function reset($name_)
    {
      unset($this->m_stubs[$name_]);
    }

    public function __call($name_, $args_)
    {
      if(false===$this->mockType->hasMethod($name_))
      {
        throw new Exception_IllegalArgument('mock',
          'Called non-existing method "'.$name_.'".'
        );
      }

      if(isset($this->m_stubs[$name_]))
      {
        if($this->m_stubs[$name_]->answer instanceof \Closure)
        {
          $answer=$this->m_stubs[$name_]->answer;

          $stub=$this->m_stubs[$name_];
          unset($this->m_stubs[$name_]);

          $result=call_user_func_array($answer,
            array_merge(array($this), (array)$args_)
          );

          $this->m_stubs[$name_]=$stub;

          return $result;
        }

        return $this->m_stubs[$name_]->call();
      }

      return call_user_func_array(array($this, $name_), $args_);
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @var \Components\Mock_Stub[]
     */
    private $m_stubs=[];
    //-----


    private function mocked($name_)
    {
      return isset($this->m_stubs[$name_]);
    }
    //--------------------------------------------------------------------------
  }
?>
