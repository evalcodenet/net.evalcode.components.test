<?php


namespace Components;


  /**
   * Test_Unit_Case_Common
   *
   * @package net.evalcode.components.test
   * @subpackage test.unit.case
   *
   * @author evalcode.net
   */
  class Test_Unit_Case_Common implements Test_Unit_Case
  {
    // TESTS
    /**
     * @test
     * @profile(fork)
     */
    public function testAssertArray()
    {
      assertArray(array());
    }

    /**
     * @test(expectedFail=true)
     * @profile(fork)
     */
    public function testAssertArrayFailed()
    {
      assertArray(new \stdClass());
    }

    /**
     * @test
     * @profile(fork)
     */
    public function testAssertArrayContains()
    {
      assertArrayContains(array('foo'=>array('bar'=>'needle')), 'needle');
    }

    /**
     * @test(expectedFail=true)
     * @profile(fork)
     */
    public function testAssertArrayContainsFailed()
    {
      assertArrayContains(array('foo'=>array('bar'=>'value')), 'needle');
    }

    /**
     * @test
     * @profile(fork)
     */
    public function testAssertNotArrayContains()
    {
      assertNotArrayContains(array('foo'=>array('bar'=>'value')), 'needle');
    }

    /**
     * @test(expectedFail=true)
     * @profile(fork)
     */
    public function testAssertNotArrayContainsFailed()
    {
      assertNotArrayContains(array('foo'=>array('bar'=>'needle')), 'needle');
    }

    /**
     * @test
     * @profile(fork)
     */
    public function testAssertArraySize()
    {
      assertArraySize(array(1, 2, 3), 3);
    }

    /**
     * @test(expectedFail=true)
     * @profile(fork)
     */
    public function testAssertArraySizeFailed()
    {
      assertArraySize(array(), 3);
    }

    /**
     * @test
     * @profile(fork)
     */
    public function testAssertArraySizeGreaterThan()
    {
      assertArraySizeGreaterThan(array(1, 2, 3, 4), 3);
    }

    /**
     * @test(expectedFail=true)
     * @profile(fork)
     */
    public function testAssertArraySizeGreaterThanFailed()
    {
      assertArraySizeGreaterThan(array(1, 2, 3), 3);
    }

    /**
     * @test
     * @profile(fork)
     */
    public function testAssertArraySizeLessThan()
    {
      assertArraySizeLessThan(array(1, 2), 3);
    }

    /**
     * @test(expectedFail=true)
     * @profile(fork)
     */
    public function testAssertArraySizeLessThanFailed()
    {
      assertArraySizeLessThan(array(1, 2, 3), 3);
    }

    /**
     * @test
     * @profile(fork)
     */
    public function testAssertBoolean()
    {
      assertBoolean(true);
      assertBoolean(false);
    }

    /**
     * @test(expectedFail=true)
     * @profile(fork)
     */
    public function testAssertBooleanFailed()
    {
      assertBoolean('true');
    }

    /**
     * @test
     * @profile(fork)
     */
    public function testAssertNotBoolean()
    {
      assertNotBoolean('true');
      assertNotBoolean('false');
    }

    /**
     * @test(expectedFail=true)
     * @profile(fork)
     */
    public function testAssertNotBooleanFailed()
    {
      assertNotBoolean(false);
    }

    /**
     * @test
     * @profile(fork)
     */
    public function testAssertContains()
    {
      assertContains('needleinhaystack', 'needle');
      assertContains('hayaroundneedleinstack', 'needle');
      assertContains('haystackinfrontofneedle', 'needle');
      assertContains(array('foo'=>array('key'=>'needle')), 'needle');
      assertContains(array('foo'=>array('needle'=>'value')), 'needle');
    }

    /**
     * @test(expectedFail=true)
     * @profile(fork)
     */
    public function testAssertContainsFailed()
    {
      assertContains('needleinhaystack', 'bar');
      assertContains('hayaroundneedleinstack', 'bar');
      assertContains('haystackinfrontofneedle', 'bar');
      assertContains(array('foo'=>array('key'=>'needle')), 'bar');
      assertContains(array('foo'=>array('needle'=>'value')), 'bar');
    }

    /**
     * @test
     * @profile(fork)
     */
    public function testAssertNotContains()
    {
      assertNotContains('needleinhaystack', 'bar');
      assertNotContains('hayaroundneedleinstack', 'bar');
      assertNotContains('haystackinfrontofneedle', 'bar');
      assertNotContains(array('foo'=>array('key'=>'needle')), 'bar');
      assertNotContains(array('foo'=>array('needle'=>'value')), 'bar');
    }

    /**
     * @test(expectedFail=true)
     * @profile(fork)
     */
    public function testAssertNotContainsFailed()
    {
      assertNotContains('needleinhaystack', 'needle');
      assertNotContains('hayaroundneedleinstack', 'needle');
      assertNotContains('haystackinfrontofneedle', 'needle');
      assertNotContains(array('foo'=>array('key'=>'needle')), 'needle');
      assertNotContains(array('foo'=>array('needle'=>'value')), 'needle');
    }

    /**
     * @test
     * @profile(fork)
     */
    public function testAssertVisibleString()
    {
      assertVisibleString('foo');
      assertVisibleString("\n\na");
    }

    /**
     * @test(expectedFail=true)
     * @profile(fork)
     */
    public function testAssertVisibleStringFailed()
    {
      assertVisibleString(' ');
      assertVisibleString(" \n \r\n ");
    }

    /**
     * @test
     * @profile(fork)
     */
    public function testAssertNotVisibleString()
    {
      assertNotVisibleString(' ');
      assertNotVisibleString(" \n \r\n ");
    }

    /**
     * @test(expectedFail=true)
     * @profile(fork)
     */
    public function testAssertNotVisibleStringFailed()
    {
      assertNotVisibleString('foo');
      assertNotVisibleString("\n\na");
    }

    /**
     * @test
     * @profile(fork)
     */
    public function testAssertTrue()
    {
      assertTrue(true);
    }

    /**
     * @test(expectedFail=true)
     * @profile(fork)
     */
    public function testAssertTrueFailed()
    {
      assertTrue(false);
    }

    /**
     * @test
     * @profile(fork)
     */
    public function testAssertNotTrue()
    {
      assertNotTrue(false);
    }

    /**
     * @test(expectedFail=true)
     * @profile(fork)
     */
    public function testAssertNotTrueFailed()
    {
      assertNotTrue(true);
    }

    /**
     * @test
     * @profile(fork)
     */
    public function testAssertFalse()
    {
      assertFalse(false);
    }

    /**
     * @test(expectedFail=true)
     * @profile(fork)
     */
    public function testAssertFalseFailed()
    {
      assertFalse(true);
    }

    /**
     * @test
     * @profile(fork)
     */
    public function testAssertNotFalse()
    {
      assertNotFalse(true);
    }

    /**
     * @test(expectedFail=true)
     * @profile(fork)
     */
    public function testAssertNotFalseFailed()
    {
      assertNotFalse(false);
    }

    /**
     * @test
     * @profile(fork)
     */
    public function testAssertNull()
    {
      assertNull(null);
    }

    /**
     * @test(expectedFail=true)
     * @profile(fork)
     */
    public function testAssertNullFailed()
    {
      assertNull(true);
    }

    /**
     * @test
     * @profile(fork)
     */
    public function testAssertNotNull()
    {
      assertNotNull('');
    }

    /**
     * @test(expectedFail=true)
     * @profile(fork)
     */
    public function testAssertNotNullFailed()
    {
      assertNotNull(null);
    }

    /**
     * @test
     * @profile(fork)
     */
    public function testAssertEmpty()
    {
      assertEmpty(0);
      assertEmpty('');
      assertEmpty(array());
    }

    /**
     * @test(expectedFail=true)
     * @profile(fork)
     */
    public function testAssertEmptyFailed()
    {
      assertEmpty(false);
    }

    /**
     * @test
     * @profile(fork)
     */
    public function testAssertNotEmpty()
    {
      assertNotEmpty(array(0));
      assertNotEmpty(' ');
      assertNotEmpty(1);
      assertNotEmpty(false);
    }

    /**
     * @test(expectedFail=true)
     * @profile(fork)
     */
    public function testAssertNotEmptyFailed()
    {
      assertNotEmpty(array());
    }

    /**
     * @test
     * @profile(fork)
     */
    public function testAssertEquals()
    {
      $a=1;
      $b=1;

      $c=new \stdClass();
      $d=new \stdClass();

      assertEquals($a, $a);
      assertEquals($a, $b);

      assertEquals($c, $c);
      assertEquals($c, $d);
    }

    /**
     * @test(expectedFail=true)
     * @profile(fork)
     */
    public function testAssertEqualsFailed()
    {
      assertEquals(
        Boolean::valueOf(true),
        Boolean::valueOf(false)
      );
    }

    /**
     * @test
     * @profile(fork)
     */
    public function testAssertNotEquals()
    {
      assertNotEquals(
        Boolean::valueOf(true),
        Boolean::valueOf(false)
      );
    }

    /**
     * @test(expectedFail=true)
     * @profile(fork)
     */
    public function testAssertNotEqualsFailed()
    {
      assertNotEquals(
        Boolean::valueOf(true),
        Boolean::valueOf(true)
      );
    }

    /**
     * @test
     * @profile(fork)
     */
    public function testAssertSame()
    {
      $a=new \stdClass();

      assertSame($a, $a);
    }

    /**
     * @test(expectedFail=true)
     * @profile(fork)
     */
    public function testAssertSameFailed()
    {
      split_time('a');
      $a=new \stdClass();
      split_time('b');
      $b=new \stdClass();

      echo 'Here could be a reason or a hint on how to activate the skipped test. Here could be a reason or a hint on how to activate the skipped test. Here could be a reason or a hint on how to activate the skipped test. Here could be a reason or a hint on how to activate the skipped test. Here could be a reason or a hint on how to activate the skipped test. Here could be a reason or a hint on how to activate the skipped test.';

      split_time('before same');
      assertSame($a, $b);
      split_time('after same');
    }

    /**
     * @test
     * @profile(fork)
     * @ignore(ignoreCallback)
     */
    public function testIgnore()
    {
      assertFalse(true);
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    public function testIgnorePlainMethod()
    {
      assertFalse(true);
    }
    //--------------------------------------------------------------------------


    // HELPERS
    /**
     * <p> Dynamic ignore-evaluation. Any return value other than 'false'
     * will lead to ignoring annotated test method.
     * <p> If a string is returned, it will be printed with the test results
     * as the reason for ignoring annotated test method.
     *
     * <p> Check if a certain dependency is fulfilled to run the test.
     * Return an explaining string for the user to allow him to fix the
     * problem and activate annotated test method.
     *
     * @see Unit_Case_Common::testIgnore() Unit_Case_Common::testIgnore()
     *
     * @return string|boolean
     */
    public static function ignoreCallback()
    {
      return 'Here could be a reason or a hint on how to activate the skipped test. Here could be a reason or a hint on how to activate the skipped test. Here could be a reason or a hint on how to activate the skipped test. Here could be a reason or a hint on how to activate the skipped test. Here could be a reason or a hint on how to activate the skipped test. Here could be a reason or a hint on how to activate the skipped test.';
    }
    //--------------------------------------------------------------------------
  }
?>
