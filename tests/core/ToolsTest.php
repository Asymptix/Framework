<?php

use Asymptix\core\Tools;

/**
 * Asymptix\core\Tools class unit tests.
 */
class ToolsTest extends \PHPUnit_Framework_TestCase {

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {}

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {}

    /**
     * @covers \Asymptix\core\Tools::isFilterExists
     * @todo   Implement testIsFilterExists().
     */
    public function testIsFilterExists() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers \Asymptix\core\Tools::getFilterValue
     * @todo   Implement testGetFilterValue().
     */
    public function testGetFilterValue() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers \Asymptix\core\Tools::isInteger
     */
    public function testIsInteger_Positive() {
        $this->assertTrue(Tools::isInteger(123));
    }

    /**
     * @covers \Asymptix\core\Tools::isInteger
     */
    public function testIsInteger_Negative() {
        $this->assertTrue(Tools::isInteger(-456));
    }

    /**
     * @covers \Asymptix\core\Tools::isInteger
     */
    public function testIsInteger_PositiveString() {
        $this->assertTrue(Tools::isInteger("1234"));
    }

    /**
     * @covers \Asymptix\core\Tools::isInteger
     */
    public function testIsInteger_NegativeString() {
        $this->assertTrue(Tools::isInteger("-1234"));
    }

    /**
     * @covers \Asymptix\core\Tools::isInteger
     */
    public function testIsInteger_Zero() {
        $this->assertTrue(Tools::isInteger(0));
    }

    /**
     * @covers \Asymptix\core\Tools::isInteger
     */
    public function testIsInteger_ZeroWithMinus() {
        $this->assertTrue(Tools::isInteger(-0));
    }

    /**
     * @covers \Asymptix\core\Tools::isInteger
     */
    public function testIsInteger_ZeroString() {
        $this->assertTrue(Tools::isInteger("0"));
    }

    /**
     * @covers \Asymptix\core\Tools::isInteger
     */
    public function testIsInteger_ZeroStringWithMinus() {
        $this->assertTrue(Tools::isInteger("-0"));
    }

    /**
     * @covers \Asymptix\core\Tools::isInteger
     */
    public function testIsInteger_LeadingZero() {
        $this->assertTrue(Tools::isInteger(0123));
    }

    /**
     * @covers \Asymptix\core\Tools::isInteger
     */
    public function testIsInteger_LeadingZeroString() {
        $this->assertTrue(Tools::isInteger("0456"));
    }

    /**
     * @covers \Asymptix\core\Tools::isInteger
     */
    public function testIsInteger_Float() {
        $this->assertFalse(Tools::isInteger(12.45));
    }

    /**
     * @covers \Asymptix\core\Tools::isInteger
     */
    public function testIsInteger_FloatString() {
        $this->assertFalse(Tools::isInteger("12.45"));
    }

    /**
     * @covers \Asymptix\core\Tools::isInteger
     */
    public function testIsInteger_SomeString() {
        $this->assertFalse(Tools::isInteger("kljaljflkfj"));
    }

    /**
     * @covers \Asymptix\core\Tools::isDouble
     */
    public function testIsDouble_Positive() {
        $this->assertTrue(Tools::isDouble(123.345));
    }

    /**
     * @covers \Asymptix\core\Tools::isDouble
     */
    public function testIsDouble_Negative() {
        $this->assertTrue(Tools::isDouble(-123.345));
    }

    /**
     * @covers \Asymptix\core\Tools::isDouble
     */
    public function testIsDouble_PositiveString() {
        $this->assertFalse(Tools::isDouble("123.345"));
    }

    /**
     * @covers \Asymptix\core\Tools::isDouble
     */
    public function testIsDouble_NegativeString() {
        $this->assertFalse(Tools::isDouble("-123.345"));
    }

    /**
     * @covers \Asymptix\core\Tools::isDouble
     */
    public function testIsDouble_Pi() {
        $this->assertTrue(Tools::isDouble(M_PI));
        $this->assertTrue(Tools::isDouble(M_PI_2));
        $this->assertTrue(Tools::isDouble(M_PI_4));
    }

    /**
     * @covers \Asymptix\core\Tools::isFloat
     */
    public function testIsFloat_Positive() {
        $this->assertTrue(Tools::isDouble(123.345));
    }

    /**
     * @covers \Asymptix\core\Tools::isFloat
     */
    public function testIsFloat_Negative() {
        $this->assertTrue(Tools::isDouble(-123.345));
    }

    /**
     * @covers \Asymptix\core\Tools::isFloat
     */
    public function testIsFloat_PositiveString() {
        $this->assertFalse(Tools::isDouble("123.345"));
    }

    /**
     * @covers \Asymptix\core\Tools::isFloat
     */
    public function testIsFloat_NegativeString() {
        $this->assertFalse(Tools::isDouble("-123.345"));
    }

    /**
     * @covers \Asymptix\core\Tools::isFloat
     */
    public function testIsFloat_Pi() {
        $this->assertTrue(Tools::isDouble(M_PI));
        $this->assertTrue(Tools::isDouble(M_PI_2));
        $this->assertTrue(Tools::isDouble(M_PI_4));
    }

    /**
     * @covers \Asymptix\core\Tools::isDoubleString
     */
    public function testIsDoubleString_Positive() {
        $this->assertFalse(Tools::isDouble("123.345"));
    }

    /**
     * @covers \Asymptix\core\Tools::isDoubleString
     */
    public function testIsDoubleString_Negative() {
        $this->assertFalse(Tools::isDouble("-123.345"));
    }

    /**
     * @covers \Asymptix\core\Tools::isDoubleString
     */
    public function testIsDoubleString_PositiveComa() {
        $this->assertFalse(Tools::isDouble("123,345"));
    }

    /**
     * @covers \Asymptix\core\Tools::isDoubleString
     */
    public function testIsDoubleString_NegativeCome() {
        $this->assertFalse(Tools::isDouble("-123,345"));
    }

    /**
     * @covers \Asymptix\core\Tools::isDoubleString
     */
    public function testIsDoubleString_PositiveDouble() {
        $this->assertTrue(Tools::isDouble(123.345));
    }

    /**
     * @covers \Asymptix\core\Tools::isDoubleString
     */
    public function testIsDoubleString_NegativeDouble() {
        $this->assertTrue(Tools::isDouble(-123.345));
    }

    /**
     * @covers \Asymptix\core\Tools::isDoubleString
     */
    public function testIsDoubleString_Pi() {
        $this->assertTrue(Tools::isDouble(M_PI));
        $this->assertTrue(Tools::isDouble(M_PI_2));
        $this->assertTrue(Tools::isDouble(M_PI_4));
    }

    /**
     * @covers \Asymptix\core\Tools::toDouble
     */
    public function testToDouble_PositiveString() {
        $this->assertEquals(Tools::toDouble("123.456"), 123.456);
    }

    /**
     * @covers \Asymptix\core\Tools::toDouble
     */
    public function testToDouble_NegativeString() {
        $this->assertEquals(Tools::toDouble("-123.456"), -123.456);
    }

    /**
     * @covers \Asymptix\core\Tools::toDouble
     */
    public function testToDouble_Positive() {
        $this->assertEquals(Tools::toDouble(123.456), 123.456);
    }

    /**
     * @covers \Asymptix\core\Tools::toDouble
     */
    public function testToDouble_Negative() {
        $this->assertEquals(Tools::toDouble(-123.456), -123.456);
    }

    /**
     * @covers \Asymptix\core\Tools::toDouble
     */
    public function testToDouble_PositiveInteger() {
        $this->assertEquals(Tools::toDouble(123), 123.0);
    }

    /**
     * @covers \Asymptix\core\Tools::toDouble
     */
    public function testToDouble_NegativeInteger() {
        $this->assertEquals(Tools::toDouble(-123), -123.0);
    }

    /**
     * @covers \Asymptix\core\Tools::toDouble
     */
    public function testToDouble_PositiveStringComa() {
        $this->assertEquals(Tools::toDouble("123,456"), 123.456);
    }

    /**
     * @covers \Asymptix\core\Tools::toDouble
     */
    public function testToDouble_NegativeStringComa() {
        $this->assertEquals(Tools::toDouble("-123,456"), -123.456);
    }

    /**
     * @covers \Asymptix\core\Tools::toDouble
     */
    public function testToDouble_PositiveStringWithoutLeading() {
        $this->assertEquals(Tools::toDouble(".123"), 0.123);
    }

    /**
     * @covers \Asymptix\core\Tools::toDouble
     */
    public function testToDouble_PositiveStringComaWithoutLeading() {
        $this->assertEquals(Tools::toDouble(",123"), 0.123);
    }

    /**
     * @covers \Asymptix\core\Tools::toFloat
     */
    public function testToFloat_PositiveString() {
        $this->assertEquals(Tools::toDouble("123.456"), 123.456);
    }

    /**
     * @covers \Asymptix\core\Tools::toFloat
     */
    public function testToFloat_NegativeString() {
        $this->assertEquals(Tools::toDouble("-123.456"), -123.456);
    }

    /**
     * @covers \Asymptix\core\Tools::toFloat
     */
    public function testToFloat_Positive() {
        $this->assertEquals(Tools::toDouble(123.456), 123.456);
    }

    /**
     * @covers \Asymptix\core\Tools::toFloat
     */
    public function testToFloat_Negative() {
        $this->assertEquals(Tools::toDouble(-123.456), -123.456);
    }

    /**
     * @covers \Asymptix\core\Tools::toFloat
     */
    public function testToFloat_PositiveInteger() {
        $this->assertEquals(Tools::toDouble(123), 123.0);
    }

    /**
     * @covers \Asymptix\core\Tools::toFloat
     */
    public function testToFloat_NegativeInteger() {
        $this->assertEquals(Tools::toDouble(-123), -123.0);
    }

    /**
     * @covers \Asymptix\core\Tools::toFloat
     */
    public function testToFloat_PositiveStringComa() {
        $this->assertEquals(Tools::toDouble("123,456"), 123.456);
    }

    /**
     * @covers \Asymptix\core\Tools::toFloat
     */
    public function testToFloat_NegativeStringComa() {
        $this->assertEquals(Tools::toDouble("-123,456"), -123.456);
    }

    /**
     * @covers \Asymptix\core\Tools::toFloat
     */
    public function testToFloat_PositiveStringWithoutLeading() {
        $this->assertEquals(Tools::toDouble(".123"), 0.123);
    }

    /**
     * @covers \Asymptix\core\Tools::toFloat
     */
    public function testToFloat_PositiveStringComaWithoutLeading() {
        $this->assertEquals(Tools::toDouble(",123"), 0.123);
    }

    /**
     * @covers \Asymptix\core\Tools::isNumeric
     * @todo   Implement testIsNumeric().
     */
    public function testIsNumeric() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers \Asymptix\core\Tools::isBoolean
     * @todo   Implement testIsBoolean().
     */
    public function testIsBoolean() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers \Asymptix\core\Tools::isString
     * @todo   Implement testIsString().
     */
    public function testIsString() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers \Asymptix\core\Tools::isObject
     * @todo   Implement testIsObject().
     */
    public function testIsObject() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers \Asymptix\core\Tools::isInstanceOf
     * @todo   Implement testIsInstanceOf().
     */
    public function testIsInstanceOf() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

}
