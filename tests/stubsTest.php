<?php

class StubClass
{
    public function doSomething() {
        return "Do something";
    }
}

class UseClass {
	public function getStubClass(StubClass $StubClass) {
		return $StubClass->doSomething();
	}
}

class StubsTest extends PHPUnit_Framework_TestCase {

	protected $object;

	protected function setUp() {}

	protected function tearDown() {}

	/**
	 * Когда тестируемый класс требует объект класса, 
	 * можно передать ему заглушку
	 * 
	 * @group StubsTest.doSomething
	 */
    public function testDoSomething() {
        // Create a stub for the SomeClass class.
        $stub = $this->getMock('StubClass');
 
        // Configure the stub.
        $stub->expects($this->any())
             ->method('doSomething')
             ->will($this->returnValue('foo'));
 
        // Calling $stub->doSomething() will now return
        // 'foo'.
        $this->assertEquals('foo', $stub->doSomething());
		
		$UseClass = new UseClass();
		$this->assertEquals('foo', $UseClass->getStubClass($stub));
    }
	
	
	
	

}


