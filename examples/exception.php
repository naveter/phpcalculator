<?php

/**
* Ёксперимент по передаче exception наверх и поледующем перехвате
* 3-х мерна€ вложенность
*/

include_once 'WSException.php';

//Fourth::doException();
Fourth::doBacktrace();

class First {
	
	public static function doException() {
		$db = debug_backtrace();
		
		print_r($db);
	
		throw new Exception("ќшибка из First::doException");
	}
}

class FirstHalf {
	
	public static function doException() {
		try {
			First::doException();
		}
		catch (Exception $e) {
			throw new WSException("ќшибка из FirstHalf::doException", null, $e, array("First"));
		}
	}
}

class Second {
	
	public static function doException() {
		try {
			FirstHalf::doException();
		}
		catch (Exception $e) {
			throw new WSException("ќшибка из Second::doException", null, $e, array("FirstHalf"));
		}
	}
}

class Third {
	
	public static function doException() {
		try {
			Second::doException();
		}
		catch (Exception $e) {
			throw new WSException("ќшибка из Third::doException", null, $e, "Second");
		}
	}
}

class Fourth {

	public static function doBacktrace() {
		try{
			Third::doException();
		}
		catch ( WSException $e ) {
		
		}
		
		
	}
	
	public static function doException() {
		try {
			Third::doException();
		}
		catch (WSException $e) {
			print get_class($e);
			
			$trace = $e->getWSTrace();
			$method = __METHOD__;
			$messageArr = array($e->getMessage(), "Caught final exception");
			$method = $trace[0]['class'] .'::'. $trace[0]['function'];
			
			while ($e = $e->getPrevious()) {
				$trace = $e->getTrace();				
				array_unshift($messageArr, $e->getMessage());
				$method = $trace[0]['class'] .'::'. $trace[0]['function'];
			}
			
			// формирование списка вызовов с указанными пол€ми
			$fieldKeyArr = array('file', 'line', 'function', 'class');
			$resultStack = array();
			
			for ( $i = 0; $i < count($messageArr); $i++ ) {
				$stack = array();
				foreach ( $fieldKeyArr as $key ) $stack[ $key ] = isset($trace[ $i ][ $key ]) ? $trace[ $i ][ $key ] : null;
				$stack['message'] = $messageArr[$i];
				array_push($resultStack, $stack);
			}
			
			print $method .": ". $resultStack[0]['message'];
			print_r($resultStack);
			
		}
	}
}





