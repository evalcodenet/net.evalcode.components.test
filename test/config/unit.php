<?php


  /* @var $this Test_Unit_Runner */
  $this->setComponentsRootPath(Environment::pathComponents());
  $this->setBuildPath(Environment::pathApplication().'/build');

  $this->excludePattern='/build/';
  $this->includePathSchema='test/unit';

  $this->addResultHandler(new Test_Unit_Result_Handler($this->getBuildPath().'/test/unit'));
  //----------------------------------------------------------------------------
?>
