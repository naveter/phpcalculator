<?php

class first {
	
	static $staticField = 'static field first';
	
	public static function staticMethod(){
		print __METHOD__ ."\n";
	}
	
	public static function doWork() {
		
		static::staticMethod();
		
	}
	
}

class second extends first {
	static $staticField = 'static field second';
	
	public static function staticMethod(){
		print __METHOD__ ."\n";
	}	
}

first::doWork();
second::doWork();