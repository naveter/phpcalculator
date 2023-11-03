<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

ini_set('output_buffering', false);

include 'calculator.php';

$calc = new Calculator();


$var = "r";

// Merged strings:
print "Result from test11 branch: ";print "Result from test22 branch: ";
print $calc->add(2, 2);

