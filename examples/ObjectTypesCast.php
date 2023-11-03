<?php

abstract class main {
	abstract public function first();
}

class junior extends main {
	public $field = "field\n";

	public function first() {
		print __METHOD__. "\n";
	}
	
	public function second() {
		print __METHOD__. "\n";
	}
}

class client {
	public function make(main $main) {
		$main->first();
		$main->second();
		print $main->field;
	}
}

$client = new client();
$junior = new junior();

$client->make( $junior );


