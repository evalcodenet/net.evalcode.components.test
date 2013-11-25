<?php


namespace Components;


  /**
   * Test_Unit_Case_Mock
   *
   * @package net.evalcode.components.test
   * @subpackage test.unit.case
   *
   * @author evalcode.net
   */
  class Test_Unit_Case_Mock implements Test_Unit_Case
  {
    // TESTS
    /**
     * @test
     * @profile
     * @ignore(ignoreUntilFixed)
     */
    public function mockFactory()
    {
      $mockException=Mock_Factory::mock('Components\\Test_Exception',
        array('test/unit/case/mock', 'Mocked Exception.')
      );

      $mockExceptionDefault=Mock_Factory::mock('Components\\Test_Exception');

      $mockRunner=Mock_Factory::mock('Components\\Test_Runner');
      $mockListener=Mock_Factory::mock('Components\\Test_Listener');

      $mockListener->when('onInitialize')->doReturn(true);
      $mockListener->when('onExecute')->doReturn(true);
      $mockListener->when('onTerminate')->doNothing();

      assertTrue($mockListener->onExecute($mockRunner));
      assertTrue($mockListener->onInitialize($mockRunner));

      $mockLL->onTerminate($mockRunner);

      assertEquals('test/unit/case/mock', $mockException->getNamespace());
      assertEquals('Mocked Exception.', $mockException->getMessage());

      assertEquals('test/exception', $mockExceptionDefault->getNamespace());
      assertEquals('Test exception.', $mockExceptionDefault->getMessage());

      $mockBindingModule=Mock_Factory::mock('Components\\Binding_Module');

      $mockBindingModule->when('bind')->doAnswer(
        function(Binding_Module $self_, $type_)
        {
          echo "Bound $type_\r\n";

          return $self_->bind($type_);
        }
      );

      $mockBindingModule->when('configure')->doAnswer(
        function(Binding_Module $self_)
        {
          $self_->bind('Components\\Test_Runner')->toInstance(Test_Runner::get());

          $self_->bind(Integer::TYPE)
            ->toInstance(22)
            ->named('boundInteger');
        }
      );

      $injector=Injector::create($mockBindingModule);

      assertSame(Test_Runner::get(), $injector->resolveInstance('Components\\Test_Runner'));
      assertEquals(22, $injector->resolveInstance(Integer::TYPE, 'boundInteger'));
    }

    /**
     * @test
     * @profile
     * @ignore(ignoreUntilFixed)
     */
    public function mockMethods()
    {
      // FIXME Mock inherited methods of parent class(es).
      $queue=Mock_Factory::mock('Components\\Test_Unit_Case_Mock_Queue', array(100));
      $queue->when('increaseCapacity')->doNothing();
      $queue->increaseCapacity(1);
      assertEquals(100, $queue->getCapacity());

      $stack=Mock_Factory::mock('Components\\Test_Unit_Case_Mock_Stack', array(100));
      $stack->when('increaseCapacity')->doNothing();
      $stack->increaseCapacity(1);
      assertEquals(100, $stack->getCapacity());
    }
    //--------------------------------------------------------------------------


    // STATIC ACCESSORS
    public static function ignoreUntilFixed()
    {
      return 'Fix class weaving, support namespaces and inheritance of parent type hierarchy';
    }
    //--------------------------------------------------------------------------
  }
?>
