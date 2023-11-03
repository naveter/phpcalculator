<?php

// что возвращает __CLASS__
class a {
	public function m() {
		print __CLASS__ ." - my name\n";
	}
}

class b extends a {}

$b = new b();
$b->m();

// что возвращает функция без return
function inc(&$a) {
	$a++;
}

$var = 10;
print "=". inc($var) ."=";



