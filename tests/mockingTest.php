<?php

class Subject
{
    protected $observers = array();
    protected $name;
    
    public function __construct($name)
    {
        $this->name = $name;
    }
    
    public function getName()
    {
        return $this->name;
    }
 
    public function attach(Observer $observer)
    {
        $this->observers[] = $observer;
    }
 
    public function doSomething()
    {
        // Do something.
        // ...
 
        // Notify observers that we did something.
        return $this->notify('I feel good');
    }
 
    public function doSomethingBad()
    {
        foreach ($this->observers as $observer) {
            $observer->reportError(42, 'Something bad happened', $this);
        }
    }
 
    protected function notify($argument) {
		$returnArr = array();
		
        foreach ($this->observers as $observer) {
            $returnArr[] = $observer->update($argument);
        }
		
		return $returnArr;
    }
 
    // Other methods.
}
 
class Observer
{
    public function update($argument)
    {
        return $argument;
    }
 
    public function reportError($errorCode, $errorMessage, Subject $subject)
    {
        // Do something
    }
 
    // Other methods.
}

class MyClass {
    protected function showWord($word) { /* отображает указанное слово на абстрактном устройстве */ }
    protected function getTemperature() { /* обращение к датчику температуры */ }
    public function getWord($temparature) {
        $temperature = (int)$temparature;
        if ($temperature < 15) { return 'cold'; }
        if ($temperature > 25) { return 'hot'; }
        return 'warm';
    }
    public function process() {
        $temperature = $this->getTemperature();
        $word = $this->getWord($temperature);
        $this->showWord($word);
    }
}

class SomeClass
{
    public function doSomething()
    {
        // Do something.
    }
}

class mockingTest extends PHPUnit_Framework_TestCase {

	protected $object;

	protected function setUp() {}

	protected function tearDown() {}
	
	/**
	 * 
	 * @group mockingTest.testMyClass
	 */
	public function testMyClass(){
        $stub = $this->getMockBuilder('SomeClass')
                     ->getMock();

        $stub->expects($this->any())
             ->method('doSomething')
             ->will($this->returnValue('foo'));

        // Calling $stub->doSomething() will now return
        // 'foo'.
        $this->assertEquals('foo', $stub->doSomething());
		
		
		
	}

	/**
	 * Когда тестируемый класс требует объект класса, 
	 * можно передать ему заглушку
	 * 
	 * @group mockingTest.testObserversAreUpdated
	 */
    public function testObserversAreUpdated() {
        $observer = $this->getMock('Observer');
		
		// Делаем моку, которая при получении чего-то будет возврщать что-то
        $observer->expects($this->any())
                 ->method('update')
                 ->with($this->equalTo('I feel good'))
				->will($this->returnValue('I feel good 2'));
 
		// Присоединяем к тестируемому объекту
        $subject = new Subject('My subject');
        $subject->attach($observer);
        $result = $subject->doSomething();
		
		$this->assertEquals($result[0], 'I feel good 2');
		
        $observer2 = $this->getMock('Observer');
		
		// Ещё одна мока, которая будет всегда возвращать одно и то-же
        $observer2->expects($this->any())
                 ->method('update')
				 ->will($this->returnValue('I feel good 3'));
		
        $subject->attach($observer2);
        $result = $subject->doSomething();
		
		$this->assertEquals($result[1], 'I feel good 3');
    }
	
	
	
	

}


