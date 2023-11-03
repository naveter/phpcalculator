<?php
/**
 * Тестирование работы getopt
 */

$shortopts  = "";
//$shortopts .= "f:";  // Required value
//$shortopts .= "v::"; // Optional value
//$shortopts .= "abc"; // These options do not accept values

$longopts  = array(
//    "required:",     // Required value
//    "group::",    // Optional value
    "send",        // No value
    "get",           // No value
);

$options = getopt($shortopts, $longopts);

//print_r($options);


putenv('TESTBOXNAME=newtestlocal_testerp');


//exec("php ./exec.php", $output, $return);
//print $return ."\n";
//print_r($output);

//print array_shift({array(1)});

$a = 1;
while( 1 == $a-- ){
	print "Hello\n";
}
