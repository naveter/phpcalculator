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

// �������� �� ��, ��� static $fields � ����������� ������
// ���������� ������ � ������� �� ������ ����������
$b = new b();
$c = new c();

$b->func('iam from b');
print "Direct response to b::fields:". b::$fields ."\n";
$b->getFields();

$c->func('iam from c');
print "Direct response to c::fields:". c::$fields ."\n";
$c->getFields();

$b->getFields();

// ��������, ��� get_called_class �������� ��������� � � ����������
// �������� ������
$d = new d();
$d->func('iam from d');
print "Direct response to d::fields:". d::$fields ."\n";
$d->getFields();


