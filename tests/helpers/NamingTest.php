<?php

use Asymptix\helpers\Naming;

/**
 * Asymptix\helpers\Naming  class unit tests.
 */
class NamingTest extends \PHPUnit_Framework_TestCase {

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        global $_FIELDS;

        $_POST = [];
        $_FIELDS = [];
    }

    /**
     * @covers \Asymptix\helpers\Naming::setValueWithComplexName
     */
    public function testSet() {
        Naming::setValueWithComplexName($_POST, "first[second][third]", 42);

        $this->assertArrayHasKey('first', $_POST);
        $this->assertArrayHasKey('second', $_POST['first']);
        $this->assertArrayHasKey('third', $_POST['first']['second']);
        $this->assertEquals($_POST['first']['second']['third'], 42);
    }

    /**
     * @covers \Asymptix\helpers\Naming::setValueWithComplexName
     */
    public function testSetNext() {
        $this->testSet();
        Naming::setValueWithComplexName($_POST, "first[second][third2]", 123);

        $this->assertArrayHasKey('first', $_POST);
        $this->assertArrayHasKey('second', $_POST['first']);
        $this->assertArrayHasKey('third', $_POST['first']['second']);
        $this->assertEquals($_POST['first']['second']['third'], 42);

        $this->assertArrayHasKey('third2', $_POST['first']['second']);
        $this->assertEquals($_POST['first']['second']['third2'], 123);
    }

    /**
     * @covers \Asymptix\helpers\Naming::getValueByComplexName
     */
    public function testGet() {
        $this->testSetNext();

        $this->assertTrue(is_array(Naming::getValueByComplexName($_POST, "first[second]")));
    }

    /**
     * @covers \Asymptix\helpers\Naming::unsetValueWithComplexName
     * @covers \Asymptix\helpers\Naming::getValueByComplexName
     */
    public function testUnset() {
        $this->testSetNext();

        Naming::unsetValueWithComplexName($_POST, "first[second][third]");

        $this->assertArrayHasKey('first', $_POST);
        $this->assertArrayHasKey('second', $_POST['first']);
        $this->assertArrayNotHasKey('third', $_POST['first']['second']);
        $this->assertTrue(is_array(Naming::getValueByComplexName($_POST, "first[second]")));
    }

    /**
     * @covers \Asymptix\helpers\Naming::unsetValueWithComplexName
     * @covers \Asymptix\helpers\Naming::getValueByComplexName
     */
    public function testUnset2() {
        $this->testUnset();

        Naming::unsetValueWithComplexName($_POST, "first[second]");

        $this->assertArrayHasKey('first', $_POST);
        $this->assertArrayNotHasKey('second', $_POST['first']);
        $this->assertTrue(is_array(Naming::getValueByComplexName($_POST, "first")));
    }

    /**
     * @covers \Asymptix\helpers\Naming::setValueWithComplexName
     * @covers \Asymptix\helpers\Naming::getValueByComplexName
     */
    public function testSet2() {
        $this->testUnset();

        Naming::setValueWithComplexName($_POST, "first[second]", 42);

        $this->assertArrayHasKey('first', $_POST);
        $this->assertArrayHasKey('second', $_POST['first']);
        $this->assertEquals(Naming::getValueByComplexName($_POST, "first[second]"), 42);
    }

    /**
     * @covers \Asymptix\helpers\Naming::setValueWithComplexName
     * @covers \Asymptix\helpers\Naming::getValueByComplexName
     */
    public function testSetRewrite() {
        $this->testSet2();

        Naming::setValueWithComplexName($_POST, "first[second][third]", 42, true);

        $this->assertArrayHasKey('first', $_POST);
        $this->assertArrayHasKey('second', $_POST['first']);
        $this->assertArrayHasKey('third', $_POST['first']['second']);
        $this->assertTrue(is_array(Naming::getValueByComplexName($_POST, "first[second]")));
        $this->assertEquals(Naming::getValueByComplexName($_POST, "first[second][third]"), 42);
    }

    /**
     * @covers \Asymptix\helpers\Naming::setValueWithComplexName
     */
    public function testSetNoRewrite() {
        $this->testSet();
        $this->testSetNext();
        $this->testUnset();
        $this->testSet2();

        try {
            Naming::setValueWithComplexName($_POST, "first[second][third]", 42);
        } catch (\Exception $ex) {
            $this->assertTrue(true);
            return;
        }
        $this->assertTrue(false);
    }

    /**
     * @covers \Asymptix\helpers\Naming::getValueByComplexName
     */
    public function testSetResult() {
        $this->testSetRewrite();

        $this->assertArrayHasKey('first', $_POST);
        $this->assertTrue(is_array(Naming::getValueByComplexName($_POST, "first")));

        $this->assertArrayHasKey('second', $_POST['first']);
        $this->assertTrue(is_array(Naming::getValueByComplexName($_POST, "first[second]")));

        $this->assertArrayHasKey('third', $_POST['first']['second']);
        $this->assertEquals(Naming::getValueByComplexName($_POST, "first[second][third]"), 42);
    }

}
