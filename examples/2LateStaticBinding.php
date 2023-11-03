<?php

abstract class a {
	protected static $fields = null; 
	
	function func($arr){
		static::$fields = $arr;
	}
	
	function getFields(){
		print get_called_class() ." ". static::$fields ."\n";
	}
}

class b extends a {
	static $fields;
}

class c extends a {
	static $fields;
}

class d extends c {
	static $fields;
}

// проверка на то, что static $fields в абстрактном классе
// обращается именно к статике из класса наследника
$b = new b();
$c = new c();

$b->func('iam from b');
print "Direct response to b::fields:". b::$fields ."\n";
$b->getFields();

$c->func('iam from c');
print "Direct response to c::fields:". c::$fields ."\n";
$c->getFields();

$b->getFields();

// проверка, что get_called_class работает корректно и в наследнике
// третьего уровня
$d = new d();
$d->func('iam from d');
print "Direct response to d::fields:". d::$fields ."\n";
$d->getFields();


