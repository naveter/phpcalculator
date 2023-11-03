<?php
/**
 * Расширение для Exception
 *
 */
class WSException extends Exception {
	
	// Для сохранения необходимых данных
	protected $data;
	
	/**
	 * Добавление четвёртого параметра
	 * 
	 * @param string $message
	 * @param int $code
	 * @param Exception $previous
	 * @param mixed $data - некая переменная или структура
	 */
	 public function __construct($message, $code = null, $previous = null, $data = null) {
		$this->data = $data;
		parent::__construct($message, $code, $previous);
	 }
	
	/**
	 * Получить сохранённые данные данные
	 *
	 * @return string - строка данных
	 */
	 public function getData() {
		return $this->data;
	 }
	 
	/**
	 * Поместить в trace доп. поля
	 *
	 * @return array
	 */
	 public function getWSTrace() {
		$trace = parent::getTrace();
		$trace[ count($trace) - 1 ]['data'] = var_export($this->getData(), true);
	 }
	 
	/**
	 * Формирование стека вызовов для исключений
	 * 
	 * @param Exception $e
	 * @return array - стек вызовов 
	 */
	public static function generateTraceException($e) {
		// выключение генерации ошибок
		$curr_error_reporting = ini_get('error_reporting');
		error_reporting(0);
		
		$trace = $e->getTrace();
		$messageArr = array();
		$method = $trace[0]['class'] . $trace[0]['function'] . $trace[0]['line'];
		$messageArr[ $method ] = $e->getMessage();			

		while ($e = $e->getPrevious()) {
			$trace = $e->getTrace();								
			$method = $trace[0]['class'] . $trace[0]['function'] . $trace[0]['line'];
			$messageArr[ $method ] = $e->getMessage();	
		}

		// формирование списка вызовов с указанными полями
		$fieldKeyArr = array('file', 'line', 'function', 'class');
		$resultStack = array();

		for ( $i = 0; $i < count($trace); $i++ ) {
			$stack = array();
			foreach ( $fieldKeyArr as $key ) $stack[ $key ] = isset($trace[ $i ][ $key ]) ? $trace[ $i ][ $key ] : null;
			$uniqkey = $trace[$i]['class'] . $trace[$i]['function'] . $trace[$i]['line'];
			$stack['message'] = isset($messageArr[ $uniqkey ]) ? $messageArr[ $uniqkey ] : null;
			array_push($resultStack, $stack);
		}			
		
		// восстановление прежнего уровня ошибок
		error_reporting($curr_error_reporting);

		return $resultStack;
	}


}