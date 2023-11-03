<?php

class dataProviderTest extends PHPUnit_Framework_TestCase {

	protected $object;

	protected function setUp() {}

	protected function tearDown() {}
	
	/**
	 * @dataProvider additionProvider
	 * @group dataProvider.testAdd
	 */
	public function testAdd($a, $b, $expected){
		$this->assertEquals($expected, $a + $b);
	}

    public function additionProvider() {
        return array(
          array(0, 0, 0),
          array(0, 1, 1),
          array(1, 0, 1),
          array(1, 1, 2)
        );
    }
	
	/**
	 * @dataProvider provider_process
	 * @group dataProvider.process
	 */
	public function test_process($temperature) {
		print $temperature .' ';
	}

	public static function provider_process() {
		return array(
			'cold' => array(10),
			'warm' => array(20),
			'hot' => array(30),
		);
	}
	

}


