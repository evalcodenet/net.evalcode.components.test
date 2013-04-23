<?php


  /**
   * Unit_Case_Mock_Factory
   *
   * @package net.evalcode.components
   * @subpackage test.unit.case.mock
   *
   * @since 1.0
   * @access public
   *
   * @author Carsten Schipke <carsten.schipke@evalcode.net>
   * @copyright Copyright (C) 2012 evalcode.net
   * @license GNU General Public License 3
   */
  class Unit_Case_Mock_Factory implements Test_Unit_Case
  {
    // TESTS
    /**
     * @Test
     * @Profile(fork)
     */
    public function testMockOne()
    {
      $mockException=Mock_Factory::mock('Test_Exception',
        array('test/unit/case/mock/factory', 'Mocked Exception.')
      );

      $mockExceptionDefault=Mock_Factory::mock('Test_Exception');

      $mockRunner=Mock_Factory::mock('Test_Runner');
      $mockLL=Mock_Factory::mock('Test_LifecycleListener');

      $mockLL->when('execution')->doReturn(true);
      $mockLL->when('initialization')->doReturn(true);
      $mockLL->when('termination')->doNothing();

      assertTrue($mockLL->execution($mockRunner));
      assertTrue($mockLL->initialization($mockRunner));

      $mockLL->termination($mockRunner);

      assertEquals('test/unit/case/mock/factory', $mockException->getNamespace());
      assertEquals('Mocked Exception.', $mockException->getMessage());

      assertEquals('test/exception', $mockExceptionDefault->getNamespace());
      assertEquals('Test exception.', $mockExceptionDefault->getMessage());

      $mockBindingModule=Mock_Factory::mock('Binding_Module');

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
          $self_->bind('Test_Runner')->toInstance(Test_Runner::get());

          $self_->bind(Integer::TYPE)
            ->toInstance(22)
            ->named('boundInteger');
        }
      );

      $injector=Injector::create($mockBindingModule);

      assertSame(Test_Runner::get(), $injector->resolveInstance('Test_Runner'));
      assertEquals(22, $injector->resolveInstance(Integer::TYPE, 'boundInteger'));
    }

    /**
     * @Test
     * @Profile(fork)
     */
    public function testMockTwo()
    {
      $queue=Mock_Factory::mock('Unit_Case_Mock_Queue', array(100));
      $queue->when('increaseCapacity')->doNothing();
      $queue->increaseCapacity(1);
      assertEquals(100, $queue->getCapacity());

      $stack=Mock_Factory::mock('Unit_Case_Mock_Stack', array(100));
      $stack->when('increaseCapacity')->doNothing();
      $stack->increaseCapacity(1);
      assertEquals(100, $stack->getCapacity());
    }
    //--------------------------------------------------------------------------
  }
?>
