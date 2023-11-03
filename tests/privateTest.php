<?php

/**
 * Класс для тестирования доступа к приватным членам 
 */
class a {
	
	private $hidden = 10;
	
	private static $hiddenStaticProperty = 20;

    public function b() {
        return 5 + $this->c();
    }
	
	public function getHidden() {
		return $this->hidden;
	}
	
	public function getHiddenStaticProperty() {
		return self::$hiddenStaticProperty;
	}

	public static function getStatic() {
		return 'static';
	}
	
    private function c() {
        return mt_rand(1,1);
    }
	
	private function privateWithArg($arg) {
		return $arg;
	}
	
	private static function privateStaticWithArg($arg) {
		return $arg;
	}
} 

interface ia {
	public static function staticMethod();
	public function Method();	
}


class aTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var a
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		$this->object = new a;
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown() {
		
	}

	/**
	 * @covers a::b
	 * @group a.testB
	 */
	public function testB() {
		$this->assertEquals(6, $this->object->b());
	}
	
	/**
	 * Check acces for private properties
	 * 
	 * @covers a::c
	 * @group a.testAccesForPrivateProperties
	 */
	public function testAccesForPrivateProperties() {
		
		// access for private property hidden
		$reflectionClass = new ReflectionClass('a');
		$a = new a;
		$reflectionProperty = $reflectionClass->getProperty('hidden');
		$reflectionProperty->setAccessible(true);
		
		// get private properties hidden
		$this->assertEquals(10, $reflectionProperty->getValue($a));
		
		// set private properties hidden
		$reflectionProperty->setValue($a, 15);
		$this->assertEquals(15, $reflectionProperty->getValue($a));
	}
	
	/**
	 * Check acces for static private properties
	 * 
	 * @covers a::c
	 * @group a.testAccesForStaticPrivateProperties
	 */
	public function testAccesForStaticPrivateProperties() {
		
		// access for private property hidden
		$reflectionClass = new ReflectionClass('a');
		$a = new a;		
		
		// get value from static private property
		$reflectionProperties = $reflectionClass->getStaticProperties();
		$this->assertEquals(20, $reflectionProperties['hiddenStaticProperty']);
		
		// other approach get access to private static property		
		$reflectionProperty = $reflectionClass->getProperty('hiddenStaticProperty');
		$reflectionProperty->setAccessible(true);
		
		$reflectionProperty->setValue($a, 25);
		$this->assertEquals(25, $reflectionProperty->getValue($a));
		
		// confirmation that static property was changed
		$a2 = new a();
		$this->assertEquals(25, $a2->getHiddenStaticProperty());
	}
	
	/**
	 * Check for acces to private method
	 * 
	 * @covers a::c
	 * @group a.testInvokePrivateMethod
	 */
	public function testInvokePrivateMethod() {
		
		$class = new ReflectionClass ('a');
		$method = $class->getMethod ('privateWithArg');
		$method->setAccessible(true);
		
		$a = new a;
		$arg = 'plainSupper';
		$this->assertEquals($arg, $method->invoke ($a, $arg) );		
	}
	
	/**
	 * Check for acces to private static method
	 * 
	 * @covers a::c
	 * @group a.testInvokePrivateStaticMethod
	 */
	public function testInvokePrivateStaticMethod() {
		
		$class = new ReflectionClass ('a');
		$method = $class->getMethod ('privateStaticWithArg');
		$method->setAccessible(true);
		
		$a = new a;
		$arg = 'plainSupper';
		$this->assertEquals($arg, $method->invoke ($a, $arg) );
	}
	
	
	/**
	 * Check for static method exists
	 * 
	 * @covers a
	 * @group a.testStaticMethodExists
	 */
	public function testStaticMethodExists() {		
//		$class = new ReflectionClass ('a');
//		$methodList = $class->getMethods();
		
		$this->assertTrue( method_exists('a', 'getStatic') );
	}
	
	/**
	 * Check for get a list of methods interface
	 * 
	 * @covers ia
	 * @group ia.testInterfaceMethodLists
	 */
	public function testInterfaceMethodLists() {		
		$class = new ReflectionClass ('ia');		
		$methodList = $class->getMethods();
		
		$this->assertEquals('Method', $methodList[1]->name );		
	}
	
	/**
	 * Simple
	 * 
	 * @covers a::c
	 * @group a.testSimple
	 */
	public function testSimple() {
		$this->assertTrue(TRUE);
	}
	
	
	

}


