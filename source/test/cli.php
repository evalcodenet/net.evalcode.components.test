<?php


namespace Components;


  /**
   * Test_Cli
   *
   * @package net.evalcode.components.test
   *
   * @author evalcode.net
   */
  class Test_Cli extends Io_Console
  {
    // STATIC ACCESSORS
    /**
     * @return \Components\Test_Cli
     */
    public static function get()
    {
      $instance=new static();

      $year=date('Y');
      $version=(string)Runtime::version();

      $instance->addOption('p', true, null, 'test root path', 'path');
      $instance->addOption('b', true, null, 'build path', 'build');
      $instance->addOption('c', true, null, 'configuration path', 'config');

      $instance->addEmptyOption();
      $instance->addOption('a', true, null, 'enable static code analyzers [emma,..]', 'analyzers');

      $instance->addEmptyOption();
      $instance->addOption('h', false, null, 'print command line instructions', 'help');
      $instance->addOption('v', false, null, 'print program version & license', 'version');

      $instance->setInfo(sprintf('%1$s%3$s%2$s%3$s',
        "Test Executor $version, net.evalcode.components",
        "Copyright (C) $year evalcode.net",
        Io::LINE_SEPARATOR_DEFAULT
      ));

      return $instance;
    }
    //--------------------------------------------------------------------------


    // ACCESSORS
    public function run()
    {
      if(false===$this->isAttached())
        $this->attach(new Io_Pipe_Stdin(), new Io_Pipe_Stdout(), new Io_Pipe_Stderr());

      $this->open();

      $mandatoryDefined=$this->hasArgument('path') && $this->hasArgument('build');

      if($this->hasArgument('help') || $this->hasArgument('version') || !$mandatoryDefined)
      {
        if($this->hasArgument('help') || !$mandatoryDefined)
          $this->appendOptions();
        else
          $this->appendInfo();

        $this->flush();
        $this->close();

        return;
      }

      Cache::disable();

      $test=Test_Unit_Runner::create();
      $test->output=new Test_Output_Console($this);

      if($this->hasArgument('config'))
        $test->configuration=$this->getArgument('config');
      if($this->hasArgument('path'))
        $test->setTestRootPath($this->getArgument('path'));
      if($this->hasArgument('build'))
        $test->setBuildPath($this->getArgument('build'));

      if($this->hasArgument('analyzers'))
      {
        $analyzers=explode(',', strtolower($this->getArgument('analyzers')));

        if(in_array('emma', $analyzers))
          $test->addListener(new \Components\Test_Listener_Emma($test->getTestRootPath()));
      }

      $test->run();
      $this->close();

      Cache::enable();
    }
    //--------------------------------------------------------------------------
  }
?>
