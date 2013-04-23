<?php


  /**
   * Unit_Case_Common
   *
   * @package net.evalcode.components
   * @subpackage test.unit.case
   *
   * @since 1.0
   * @access public
   *
   * @author Carsten Schipke <carsten.schipke@evalcode.net>
   * @copyright Copyright (C) 2012 evalcode.net
   * @license GNU General Public License 3
   */
  class Unit_Case_Common implements Test_Unit_Case
  {
    // TESTS
    /**
     * @Test
     * @Profile(fork)
     */
    public function testAssertArray()
    {
      assertArray(array());
    }

    /**
     * @Test(expectedFail=true)
     * @Profile(fork)
     */
    public function testAssertArrayFailed()
    {
      assertArray(new stdClass());
    }

    /**
     * @Test
     * @Profile(fork)
     */
    public function testAssertArrayContains()
    {
      assertArrayContains(array('foo'=>array('bar'=>'needle')), 'needle');
    }

    /**
     * @Test(expectedFail=true)
     * @Profile(fork)
     */
    public function testAssertArrayContainsFailed()
    {
      assertArrayContains(array('foo'=>array('bar'=>'value')), 'needle');
    }

    /**
     * @Test
     * @Profile(fork)
     */
    public function testAssertNotArrayContains()
    {
      assertNotArrayContains(array('foo'=>array('bar'=>'value')), 'needle');
    }

    /**
     * @Test(expectedFail=true)
     * @Profile(fork)
     */
    public function testAssertNotArrayContainsFailed()
    {
      assertNotArrayContains(array('foo'=>array('bar'=>'needle')), 'needle');
    }

    /**
     * @Test
     * @Profile(fork)
     */
    public function testAssertArraySize()
    {
      assertArraySize(array(1, 2, 3), 3);
    }

    /**
     * @Test(expectedFail=true)
     * @Profile(fork)
     */
    public function testAssertArraySizeFailed()
    {
      assertArraySize(array(), 3);
    }

    /**
     * @Test
     * @Profile(fork)
     */
    public function testAssertArraySizeGreaterThan()
    {
      assertArraySizeGreaterThan(array(1, 2, 3, 4), 3);
    }

    /**
     * @Test(expectedFail=true)
     * @Profile(fork)
     */
    public function testAssertArraySizeGreaterThanFailed()
    {
      assertArraySizeGreaterThan(array(1, 2, 3), 3);
    }

    /**
     * @Test
     * @Profile(fork)
     */
    public function testAssertArraySizeLessThan()
    {
      assertArraySizeLessThan(array(1, 2), 3);
    }

    /**
     * @Test(expectedFail=true)
     * @Profile(fork)
     */
    public function testAssertArraySizeLessThanFailed()
    {
      assertArraySizeLessThan(array(1, 2, 3), 3);
    }

    /**
     * @Test
     * @Profile(fork)
     */
    public function testAssertBoolean()
    {
      assertBoolean(true);
      assertBoolean(false);
    }

    /**
     * @Test(expectedFail=true)
     * @Profile(fork)
     */
    public function testAssertBooleanFailed()
    {
      assertBoolean('true');
    }

    /**
     * @Test
     * @Profile(fork)
     */
    public function testAssertNotBoolean()
    {
      assertNotBoolean('true');
      assertNotBoolean('false');
    }

    /**
     * @Test(expectedFail=true)
     * @Profile(fork)
     */
    public function testAssertNotBooleanFailed()
    {
      assertNotBoolean(false);
    }

    /**
     * @Test
     * @Profile(fork)
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
     * @Test(expectedFail=true)
     * @Profile(fork)
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
     * @Test
     * @Profile(fork)
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
     * @Test(expectedFail=true)
     * @Profile(fork)
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
     * @Test
     * @Profile(fork)
     */
    public function testAssertVisibleString()
    {
      assertVisibleString('foo');
      assertVisibleString("\n\na");
    }

    /**
     * @Test(expectedFail=true)
     * @Profile(fork)
     */
    public function testAssertVisibleStringFailed()
    {
      assertVisibleString(' ');
      assertVisibleString(" \n \r\n ");
    }

    /**
     * @Test
     * @Profile(fork)
     */
    public function testAssertNotVisibleString()
    {
      assertNotVisibleString(' ');
      assertNotVisibleString(" \n \r\n ");
    }

    /**
     * @Test(expectedFail=true)
     * @Profile(fork)
     */
    public function testAssertNotVisibleStringFailed()
    {
      assertNotVisibleString('foo');
      assertNotVisibleString("\n\na");
    }

    /**
     * @Test
     * @Profile(fork)
     */
    public function testAssertTrue()
    {
      assertTrue(true);
    }

    /**
     * @Test(expectedFail=true)
     * @Profile(fork)
     */
    public function testAssertTrueFailed()
    {
      assertTrue(false);
    }

    /**
     * @Test
     * @Profile(fork)
     */
    public function testAssertNotTrue()
    {
      assertNotTrue(false);
    }

    /**
     * @Test(expectedFail=true)
     * @Profile(fork)
     */
    public function testAssertNotTrueFailed()
    {
      assertNotTrue(true);
    }

    /**
     * @Test
     * @Profile(fork)
     */
    public function testAssertFalse()
    {
      assertFalse(false);
    }

    /**
     * @Test(expectedFail=true)
     * @Profile(fork)
     */
    public function testAssertFalseFailed()
    {
      assertFalse(true);
    }

    /**
     * @Test
     * @Profile(fork)
     */
    public function testAssertNotFalse()
    {
      assertNotFalse(true);
    }

    /**
     * @Test(expectedFail=true)
     * @Profile(fork)
     */
    public function testAssertNotFalseFailed()
    {
      assertNotFalse(false);
    }

    /**
     * @Test
     * @Profile(fork)
     */
    public function testAssertNull()
    {
      assertNull(null);
    }

    /**
     * @Test(expectedFail=true)
     * @Profile(fork)
     */
    public function testAssertNullFailed()
    {
      assertNull(true);
    }

    /**
     * @Test
     * @Profile(fork)
     */
    public function testAssertNotNull()
    {
      assertNotNull('');
    }

    /**
     * @Test(expectedFail=true)
     * @Profile(fork)
     */
    public function testAssertNotNullFailed()
    {
      assertNotNull(null);
    }

    /**
     * @Test
     * @Profile(fork)
     */
    public function testAssertEmpty()
    {
      assertEmpty(0);
      assertEmpty('');
      assertEmpty(array());
    }

    /**
     * @Test(expectedFail=true)
     * @Profile(fork)
     */
    public function testAssertEmptyFailed()
    {
      assertEmpty(false);
    }

    /**
     * @Test
     * @Profile(fork)
     */
    public function testAssertNotEmpty()
    {
      assertNotEmpty(array(0));
      assertNotEmpty(' ');
      assertNotEmpty(1);
      assertNotEmpty(false);
    }

    /**
     * @Test(expectedFail=true)
     * @Profile(fork)
     */
    public function testAssertNotEmptyFailed()
    {
      assertNotEmpty(array());
    }

    /**
     * @Test
     * @Profile(fork)
     */
    public function testAssertEquals()
    {
      $a=1;
      $b=1;

      $c=new stdClass();
      $d=new stdClass();

      assertEquals($a, $a);
      assertEquals($a, $b);

      assertEquals($c, $c);
      assertEquals($c, $d);
    }

    /**
     * @Test(expectedFail=true)
     * @Profile(fork)
     */
    public function testAssertEqualsFailed()
    {
      assertEquals(
        Boolean::valueOf(true),
        Boolean::valueOf(false)
      );
    }

    /**
     * @Test
     * @Profile(fork)
     */
    public function testAssertNotEquals()
    {
      assertNotEquals(
        Boolean::valueOf(true),
        Boolean::valueOf(false)
      );
    }

    /**
     * @Test(expectedFail=true)
     * @Profile(fork)
     */
    public function testAssertNotEqualsFailed()
    {
      assertNotEquals(
        Boolean::valueOf(true),
        Boolean::valueOf(true)
      );
    }

    /**
     * @Test
     * @Profile(fork)
     */
    public function testAssertSame()
    {
      $a=new stdClass();

      assertSame($a, $a);
    }

    /**
     * @Test(expectedFail=true)
     * @Profile(fork)
     */
    public function testAssertSameFailed()
    {
      $a=new stdClass();
      $b=new stdClass();

      assertSame($a, $b);
    }

    /**
     * @Test
     * @Profile(fork)
     * @Ignore(ignoreCallback)
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
     * @see Unit_Case_Common::testIgnore()
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
