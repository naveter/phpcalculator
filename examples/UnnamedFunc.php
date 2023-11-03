<?php
/* 
 * Попытка использования в объекте анонимных функций
 * 
 * @author ilya.gulevskiy
 */

$UnnamedFunc = new UnnamedFunc();
$UnnamedFunc->method();

print "Hello\n";


class UnnamedFunc {
	public $fieldA = 100;
	public $fieldB = 50;
	public $funcList = array();
	
	public function __construct() {
		$this->funcList = array(
			'first' => function(&$obj, $p = null) {  
				$obj->fieldB += 1;
				print $obj->fieldA ." I am first\n";
			},
		);
	}
	
	public function method() {
		$this->funcList['first']($this);
		
		print $this->fieldB ."\n";
	}
	
	
	
	
}


