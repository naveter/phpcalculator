<?php

/**
 * Каким видит метод родительского объекта поле, изменённое в наследнике
 */
 
class Father {
	protected $First = 1;
	public $Second = 1;
	
	public function func(){
		print $this->First ." ". $this->Second ."\n";
	}
	
} 

class Child extends Father {
	protected $First = 10;
	public $Second = 10;	
}

$child = new Child();
$child->func();

print "...";
