<?php


namespace Components;


  /* @var $this Test_Runner */
  if(null===$this->getTestRootPath())
    $this->setTestRootPath(Environment::pathComponents());
  if(null===$this->getBuildPath())
    $this->setBuildPath(Environment::pathApplication().'/build');

  $this->addListener(new Test_Listener_Emma(
    $this->getTestRootPath(), $this->getBuildPath().'/emma.xml'
  ));
?>
