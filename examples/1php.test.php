<?php

// ��� ���������� __CLASS__
class a {
	public function m() {
		print __CLASS__ ." - my name\n";
	}
}

class b extends a {}

$b = new b();
$b->m();

// ��� ���������� ������� ��� return
function inc(&$a) {
	$a++;
}

$var = 10;
print "=". inc($var) ."=";



