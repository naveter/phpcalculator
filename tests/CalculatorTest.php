<?php

require_once dirname(__FILE__) . '/../calculator.php';

/**
 * Test class for Calculator.
 * Generated by PHPUnit on 2012-04-03 at 17:54:34.
 * 
 * @backupGlobals disabled
 * @backupStaticAttributes disabled
 */
class CalculatorTest extends PHPUnit_Framework_TestCase {

    /**
     * @var Calculator
     */
    protected $object;
    
    public static function setUpBeforeClass() {
        $_REQUEST['var'] = 'request var';
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->object = new Calculator;
        
        
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        
    }
    
    /**
     * Установка зависимости
     */
    public function testDepends() {
        $this->assertTrue(TRUE);
        
        return 222;
    }

    /**
     * @covers Calculator::add
     * @depends testDepends
     */
    public function testAdd($var) {              
        $_REQUEST['var'] = "other var";
        
        $this->assertEquals($var, 222);
    }
   


    /**
     * @covers Calculator::addnew
     * @todo Implement testAddnew().
     */
    public function testAddnew() {
        
        print Calculator::$static_var;
        
        $this->assertTrue(TRUE);
    }
    
	/**
	 * 
	 * @group xdebugTest
	 * @param type $arg
	 * @return int
	 */
	public function testSome() {
		print "Hello!";
		
//		print_r($GLOBALS);
		
//		if ( xdebug_is_enabled() ) print "enabled";
		
//		print_r( ini_get_all() );
		
		$calc = new Calculator();
		
		$calc->some(5);
	}


}


//$CalculatorTest = new CalculatorTest();
//$CalculatorTest->testSome();
