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
	 



}
