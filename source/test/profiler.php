<?php


namespace Components;


  /**
   * Test_Profiler
   *
   * @package net.evalcode.components
   * @subpackage test
   *
   * @author evalcode.net
   */
  class Test_Profiler extends Debug_Profiler
  {
    /**     * @see Components\Debug_Profiler::profileCallForked() Components\Debug_Profiler::profileCallForked()
     *
     * @return \Components\Test_Profiler
     */
    public static function profileCallForked(array $callable_, array $args_=array())
    {
      if(false===static::isForkedProfilingSupported() || false===Memory_Shared_Shm::isSupported())
      {
        throw new Runtime_Exception('test/profiler',
          'Forked profiling is not supported on this platform\'s configuration.'
        );
      }

      if(false===is_callable($callable_))
        throw new Runtime_Exception('test/profiler', 'Valid callback expected.');

      $shm=Memory_Shared_Shm_Temporary::create();
      $shm->attach();

      self::$m_profileForkedArgs=$args_;
      self::$m_profileForkedCallable=$callable_;
      self::$m_profileForkedSegmentId=$shm->getSegmentId();

      $pid=pcntl_fork();

      if(-1==$pid)
      {
        throw new Runtime_Exception('test/profiler',
          'Unable to fork child process. Forked profiling failed.'
        );
      }

      if($pid)
      {
        $pid=pcntl_wait($status);
      }
      else
      {
        ob_start();

        // FIXME (CSH) Find elegant solution to transfer state of global scope & context.
        Assertion_Context::push(new Assertion_Context());

        $sessionId=static::start();

        try
        {
          $returnValue=call_user_func_array(
            self::$m_profileForkedCallable, self::$m_profileForkedArgs
          );

          $profiler=static::stop($sessionId);
          $profiler->m_returnValue=$returnValue;
        }
        catch(\ErrorException $e)
        {
          $profiler=static::stop($sessionId);
          $profiler->m_exception=Exception_Flat::create($e);
        }
        catch(\Exception $e)
        {
          $profiler=static::stop($sessionId);
          $profiler->m_exception=Exception_Flat::create($e);
        }

        $assertions=Assertion_Context::pop()->getAssertions();

        self::$m_profileForkedArgs=null;
        self::$m_profileForkedCallable=null;

        $segment=Memory_Shared_Shm::forSegment(self::$m_profileForkedSegmentId);
        $segment->attach();

        $segment->set(1, ob_get_clean());
        $segment->set(2, $assertions);
        $segment->set(3, $profiler->result()->m_exception);
        $segment->set(4, $profiler->result()->m_memoryConsumptionAfter);
        $segment->set(5, $profiler->result()->m_memoryConsumptionBefore);
        $segment->set(6, $profiler->result()->m_memoryConsumptionPeak);
        $segment->set(7, $profiler->result()->m_posixSystemTime);
        $segment->set(8, $profiler->result()->m_posixUserTime);
        $segment->set(9, $profiler->result()->m_returnValue);
        $segment->set(10, $profiler->result()->m_timeStart);
        $segment->set(11, $profiler->result()->m_timeStop);
        $segment->set(12, $profiler->result()->m_splitTimeTable);

        exit(0);
      }

      echo $shm->get(1);

      Assertion_Context::current()->addAssertions((array)$shm->get(2));

      $profiler=new static();
      $profiler->m_profiling=false;
      $profiler->m_exception=$shm->get(3);
      $profiler->m_memoryConsumptionAfter=$shm->get(4);
      $profiler->m_memoryConsumptionBefore=$shm->get(5);
      $profiler->m_memoryConsumptionPeak=$shm->get(6);
      $profiler->m_posixSystemTime=$shm->get(7);
      $profiler->m_posixUserTime=$shm->get(8);
      $profiler->m_returnValue=$shm->get(9);
      $profiler->m_timeStart=$shm->get(10);
      $profiler->m_timeStop=$shm->get(11);
      $profiler->m_splitTimeTable=$shm->get(12);

      $shm->clear();

      return $profiler;
    }
    //--------------------------------------------------------------------------
  }


  // GLOBAL HELPERS
  function split_time($description_)
  {
    Test_Profiler::split($description_);
  }
?>
