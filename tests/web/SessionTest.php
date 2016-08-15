<?php

use Asymptix\web\Session;

/**
 * Asymptix\web\Session class unit tests.
 */
class SessionTest extends \PHPUnit_Framework_TestCase {

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {}

    /**
     * @covers \Asymptix\web\Request::setFieldValue
     */
    public function testPostSet() {
        Session::set("_post[first]", 1);
        Session::set("_post[second]", 2);
        Session::set("_post[third]", 3);

        $this->assertArrayHasKey('_post', $_SESSION);

        $this->assertArrayHasKey('first', $_SESSION['_post']);
        $this->assertArrayHasKey('second', $_SESSION['_post']);
        $this->assertArrayHasKey('third', $_SESSION['_post']);

        $this->assertEquals($_SESSION['_post']['first'], 1);
        $this->assertEquals($_SESSION['_post']['second'], 2);
        $this->assertEquals($_SESSION['_post']['third'], 3);
    }

}
