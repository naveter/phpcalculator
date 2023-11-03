<?php

interface IObserver
{
  function onChanged( $sender, $args );
}

interface IObservable
{
  function addObserver( $observer );
}

class Observable implements IObservable {
	
	private static $requiredInterface = 'IObserver';
	private $_observers = array();
	
	public function __call($name, $arguments)
    {
		if (!method_exists($this, $name)) throw new Exception("Method ". $name ." not exists"); 
		if ( empty($this->_observers) ) return;
		
		$this->$name();
		
		foreach ( $this->_observers as $obj) {
			$obj->onChanged($name, $arguments);
		}

    }
	
	function __construct() {
		
	}
	
	private function firstMethod() {
		print __METHOD__. "\n";
	}
	
	private function secondMethod() {
		print __METHOD__. "\n";
	}
	
	public function addObserver( $obj ) {
		// Проверка, что этот класс использует интерфейс 
		$reflector = new ReflectionClass(get_class($obj));
		$interfaceArr = $reflector->getInterfaceNames();
		if ( !in_array(self::$requiredInterface, $interfaceArr) ) 
			throw new Exception("Отсутствует обязательный интерфейс ". self::$requiredInterface ." у ". get_class($obj));
		
		array_push($this->_observers, $obj);
	}
}

class firstObserver implements IObserver {
	public function onChanged( $sender, $args ) {
		print __CLASS__ ." I know ". $sender ." changed with params ". var_export($args, true) ."\n";
	}
}

class secondObserver implements IObserver {
	public function onChanged( $sender, $args ) {
		print __CLASS__ ." I know ". $sender ." changed with params ". var_export($args, true) ."\n";
	}
}

$firstObserver = new firstObserver();
$secondObserver = new secondObserver();

$Observable = new Observable();
$Observable->addObserver($firstObserver);
$Observable->addObserver($secondObserver);

$Observable->firstMethod();
$Observable->secondMethod();


