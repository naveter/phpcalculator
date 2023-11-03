<?php

//if (ini_get('XDEBUG_IS_RUN')) print "XDEBUG_IS_RUN is defined";
//else print "go on";

function func() {
//	print xdebug_call_function();
	
	$var = 0;
	for ($i = 0; $i < 100; $i++) {
		$var += $i;
	}
}

func();

$var = 33;
$var2 = $var + 33;

print "\n". $var2;
// some www for libevent YES! NO!
 
