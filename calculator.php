<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Calculator
{
    public static $static_var = "static variable";
	
	public function __construct() {
		
	}
    
    /**
     * 
     * @assert (1, 2) == 4
     */
    public function add($a, $b) {
        return $a + $b;
    }
    
    public function addnew($a, $b) {
        return $a + $b;
    }
	
	public function some($arg) {
		$var = $arg * 2;
		$arr = array(1,2,3,4);
		
		foreach ( $arr as $a ) {
			$var += $a;
		}
		
		return $var;
	}
}





