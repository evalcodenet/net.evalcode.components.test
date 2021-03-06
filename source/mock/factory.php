<?php


namespace Components;


  /**
   * Mock_Factory
   *
   * @package net.evalcode.components.test
   * @subpackage mock
   *
   * @author evalcode.net
   */
  class Mock_Factory
  {
    // STATIC ACCESSORS
    /**
     * @return \Components\Mock
     */
    public static function mock($type_, array $args_=[])
    {
      if(false===@class_exists($type_) && false===@interface_exists($type_))
      {
        throw new Exception_IllegalArgument('mock/factory',
          sprintf('Unable to mock undeclared type %1$s.', $type_)
        );
      }

      return static::mockImpl($type_, $args_);
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    private static $m_tokensMethodSignature=array(
      T_ARRAY,
      T_CONSTANT_ENCAPSED_STRING,
      T_DOUBLE_ARROW,
      T_DOUBLE_COLON,
      T_DNUMBER,
      T_LNUMBER,
      T_STRING,
      T_VARIABLE
    );

    private static $m_tmpPath;
    //-----


    /**
     * @return \Components\Mock
     */
    // FIXME (CSH) Support namespaces correctly.
    protected static function mockImpl($type_, array $args_=[])
    {
      $type=new \ReflectionClass($type_);

      if($type->isFinal())
        throw new Exception_IllegalArgument('mock/factory', 'Can not mock final class.');

      $mtime=@filemtime($type->getFileName());
      $classMock='Mock_'.str_replace('\\', '_', $type->getNamespaceName()).'_'.$type->getShortName()."_$mtime";

      if(false===@class_exists($classMock))
      {
        if(null===self::$m_tmpPath)
          self::$m_tmpPath=(string)Test_Runner::get()->getTempPath();

        $fileName=self::$m_tmpPath."/$classMock.php";

        if(false===@file_exists($fileName))
        {
          $source=self::weaveMock($classMock, new \ReflectionClass('Components\\Mock'), $type);

          if(false===@file_put_contents($fileName, $source, 0644))
          {
            throw new Exception_IllegalState('mock/factory',
              sprintf('Unable to create mock [type: %1$s, path: %2$s].', $type_, $fileName)
            );
          }
        }

        require_once $fileName;
      }

      $classMock="Components\\$classMock";

      if(0<count($args_))
      {
        $refl=new \ReflectionClass($classMock);
        $mock=$refl->newInstanceArgs($args_);
      }
      else
      {
        $mock=new $classMock();
      }

      $mock->mockType=$type;

      return $mock;
    }

    // FIXME Respect methods defined by inherited interfaces / classes / traits.
    protected static function weaveMock($classNameMock_, \ReflectionClass $mock_, \ReflectionClass $type_)
    {
      $mock=@file_get_contents($mock_->getFileName());
      $type=@file_get_contents($type_->getFileName());
      $typeMTime=@filemtime($type_->getFileName());

      $method=[];
      $methods=[];
      foreach(token_get_all($type) as $token)
      {
        if(count($method) && ('{'==$token || ';'==$token))
        {
          $methods[]=$method;
          $method=[];
        }
        else if(count($method))
        {
          $method[]=$token;
        }

        if(T_FUNCTION==$token[0])
          $method[]=$token;
      }

      $signatures=[];
      foreach($methods as $tokens)
      {
        $tokens=array_slice($tokens, 2);
        $nameToken=array_shift($tokens);
        $name=$nameToken[1];

        $signature='';
        foreach($tokens as $token)
        {
          if(false===is_array($token))
          {
            $signature.=$token;

            continue;
          }

          if(T_WHITESPACE==$token[0])
            continue;

          if(in_array($token[0], self::$m_tokensMethodSignature))
          {
            $signature.=$token[1];

            if(T_STRING==$token[0])
              $signature.=' ';
          }
        }

        $signatures[$name]=$signature;
      }

      $source=explode(Io::LINE_SEPARATOR_DEFAULT, $mock);
      $source=array_slice($source, 0, $mock_->getEndLine()-1);

      foreach($signatures as $methodName=>$signature)
      {
        $method=$type_->getMethod($methodName);

        if($method->isStatic() || $method->isFinal())
          continue;

        $parameters=[];
        foreach($method->getParameters() as $parameter)
          $parameters[]='$'.$parameter->name;

        if($method->isConstructor())
        {
          $source[]="public function $methodName$signature {parent::$methodName(".implode(', ', $parameters).");}";

          continue;
        }

        $visibility='public';
        if($method->isPrivate())
          $visibility='private';
        else if($method->isProtected())
          $visibility='protected';

        $sourceMethod="$visibility function $methodName$signature {if(\$this->mocked('$methodName')) return \$this->__call('$methodName', func_get_args());";

        if(false===$type_->isInterface() && false===$method->isAbstract())
          $sourceMethod.=" return parent::$methodName(".implode(', ', $parameters).");";

        $source[]=$sourceMethod."}";
      }

      $source=implode(Io::LINE_SEPARATOR_DEFAULT, $source).Io::LINE_SEPARATOR_DEFAULT.'  }'.Io::LINE_SEPARATOR_DEFAULT.'?>';
      $inheritance=$type_->isInterface()?'implements':'extends';

      return str_replace('class Mock', "class $classNameMock_ $inheritance \\{$type_->name}", $source);
    }
    //--------------------------------------------------------------------------
  }
?>
