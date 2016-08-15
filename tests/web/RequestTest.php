<?php

use Asymptix\web\Request;
use Asymptix\web\Http;

/**
 * Asymptix\web\Request class unit tests.
 */
class RequestTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var Request
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        global $_FIELDS;

        $_REQUEST['var'] = "REQUEST var";
        $_GET['var'] = "GET var";
        $_POST['var'] = "POST var";

        $_REQUEST['complex']['var']['value'] = 123;
        $_GET['complex']['var']['value'] = "abc";
        $_POST['complex']['var']['value'] = "qwerty";

        $_FIELDS = [
            'first' => 123,
            'second' => [
                'test' => 1,
                'test1' => 2
            ]
        ];

        $this->object = new Request;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {}

    /**
     * @covers \Asymptix\web\Request::isFormSubmitted
     * @todo   Implement testIsFormSubmitted().
     */
    public function testIsFormSubmitted() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers \Asymptix\web\Request::getFieldValue
     */
    public function testGetFieldValue_request() {
        $this->assertEquals(
                $_REQUEST['var'],
                Request::getFieldValue('var')
        );
    }

    /**
     * @covers \Asymptix\web\Request::getFieldValue
     */
    public function testGetFieldValue_get() {
        $this->assertEquals(
                $_GET['var'],
                Request::getFieldValue('var', Http::GET)
        );
    }

    /**
     * @covers \Asymptix\web\Request::getFieldValue
     */
    public function testGetFieldValue_post() {
        $this->assertEquals(
                $_POST['var'],
                Request::getFieldValue('var', Http::POST)
        );
    }

    /**
     * @covers \Asymptix\web\Request::getFieldValue
     */
    public function testGetFieldValue_complex() {
        $this->assertEquals(
                123,
                Request::getFieldValue('complex[var][value]')
        );
    }

    /**
     * @covers \Asymptix\web\Request::getFieldValue
     */
    public function testGetFieldValue_complex_get() {
        $this->assertEquals(
                "abc",
                Request::getFieldValue('complex[var][value]', Http::GET)
        );
    }

    /**
     * @covers \Asymptix\web\Request::getFieldValue
     */
    public function testGetFieldValue_complex_post() {
        $this->assertEquals(
                "qwerty",
                Request::getFieldValue('complex[var][value]', Http::POST)
        );
    }

    /**
     * @covers \Asymptix\web\Request::getFieldValue
     */
    public function testGetFieldValue_notExists() {
        $this->assertNull(Request::getFieldValue('name'));
    }

    /**
     * @covers \Asymptix\web\Request::getFieldValue
     */
    public function testGetFieldValue_complex_notExists() {
        $this->assertNull(Request::getFieldValue('name[test][var]'));
    }

    /**
     * @covers \Asymptix\web\Request::setFieldValue
     */
    public function testSetFieldValue() {
        global $_FIELDS;

        Request::setFieldValue('field_name', 123);

        $this->assertArrayHasKey('field_name', $_FIELDS);
        $this->assertEquals($_FIELDS['field_name'], 123);
    }

    /**
     * @covers \Asymptix\web\Request::setFieldValue
     */
    public function testSetFieldValueComplex() {
        global $_FIELDS;

        Request::setFieldValue('field_name[test][test1]', 123);

        $this->assertArrayHasKey('field_name', $_FIELDS);
        $this->assertArrayHasKey('test', $_FIELDS['field_name']);
        $this->assertArrayHasKey('test1', $_FIELDS['field_name']['test']);
        $this->assertEquals($_FIELDS['field_name']['test']['test1'], 123);
    }

    /**
     * @covers \Asymptix\web\Request::rememberField
     */
    public function testRememberField() {
        Request::rememberField('test', "data");

        $this->assertArrayHasKey('_post', $_SESSION);
        $this->assertArrayHasKey('test', $_SESSION['_post']);
        $this->assertEquals($_SESSION['_post']['test'], serialize("data"));
    }

    /**
     * @covers \Asymptix\web\Request::forgetField
     */
    public function testForgetField() {
        $_SESSION['_post'] = [
            'test' => "some data"
        ];
        $this->assertArrayHasKey('_post', $_SESSION);
        $this->assertArrayHasKey('test', $_SESSION['_post']);
        $this->assertEquals($_SESSION['_post']['test'], "some data");

        Request::forgetField('test');
        $this->assertArrayHasKey('_post', $_SESSION);
        $this->assertArrayNotHasKey('test', $_SESSION['_post']);
    }

    /**
     * @covers \Asymptix\web\Request::forgetFields
     */
    public function testForgetFields() {
        $_SESSION['_post'] = "some data";
        $this->assertArrayHasKey('_post', $_SESSION);

        Request::forgetFields();
        $this->assertArrayNotHasKey('_post', $_SESSION);
    }

    /**
     * @covers \Asymptix\web\Request::changeFieldValue
     * @todo   Implement testChangeFieldValue().
     */
    public function testChangeFieldValue() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers \Asymptix\web\Request::castFieldValue
     * @todo   Implement testCastFieldValue().
     */
    public function testCastFieldValue() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers \Asymptix\web\Request::normalizeCheckboxes
     * @todo   Implement testNormalizeCheckboxes().
     */
    public function testNormalizeCheckboxes() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers \Asymptix\web\Request::removeFields
     */
    public function testRemoveFields() {
        global $_FIELDS;

        $this->testSetFieldValueComplex();

        Request::removeFields([
            'field_name[test]',
            'field_name[test][test1]'
        ]);
        $this->assertArrayHasKey('field_name', $_FIELDS);
        $this->assertArrayNotHasKey('test', $_FIELDS['field_name']);
        $this->assertEquals($_FIELDS['field_name'], []);
    }

}
