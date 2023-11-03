<?php
/**
 * #15465: Получение почтовых статусов от Беты
 * 
 * Отправка в Бету список ШПИ, по которым нужно произвести розыск на Почте России,
 * и запрос результатов розыска.
 * В зависимости от результатов розыска у ордеров меняются статусы
 * 
 * Использование:
 * 
 * php getPostOrdersStatusFromBeta.php [--send --get --limit --debug]
 * --send - отправить ШПИ, запрос 503
 * --get - получить результаты поиска ШПИ, запрос 505
 * --limit=1000 - какой лимит выборки записей делать. Если не указывать - лимита не будет
 * --debug - писать отправляемое и полученное в лог getPostOrdersStatusFromBeta.php.log
 * 
 * @author ilya.gulevskiy
 */

if (!defined('SECTIONS')) define('SECTIONS', 'erp');
$_SERVER['HTTP_HOST'] = '';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';

require_once __DIR__."/../../caru/__autoload.php";


class getPostOrdersStatusFromBeta2 {
	public static $partner_id = 131;
	public static $password = 's^A8-d';
	public static $recipients = array(
		'sergey.solopov@studio-moderna.com',
		'ilya.gulevskiy@studio-moderna.com',
		'maksim.rodikov@studio-moderna.com',
	);
	public static $limit = '';
	public static $nameOfTblSetting = 'getPostOrdersStatusFromBeta_upd_seq';
	public static $debug = false;
	public static $log = null;

	// название поля с датой перевода в 25-й субстатус
	public static $set25substatus_name = 'set25substatus';

	// коллекция ШПИ, которые получили конечные статусы
	public $EndStatusBarcodes = array();

	public function __construct() {
		self::$log = basename(__FILE__) .".log";
	}

	/**
	 * 503 Отправить на отслеживание
	 */
	public function putToTrack() {

		$codes = file('32073_data.txt');

		$package = array();
		$assoc = array();
		foreach($codes as $code)
		{
			$code = str_replace(array("\r","\n"),'',$code);
			$package[] = $code;
			if(count($package) > 500)
			{
				$xml_respond = $this->sendHTTPRequest(503, $package);
//				$xml = new XMLReader();
//				$xml->xml($xml_respond);
//				$result = $this->xml2assoc($xml);
//				$xml->close();
//				$package = array();
				echo 1;
			}
		}
		if($package)
		{
			$xml_respond = $this->sendHTTPRequest(503, $package);
//			$xml = new XMLReader();
//			$xml->xml($xml_respond);
//			$result = $this->xml2assoc($xml);
//			$xml->close();
//			$package = array();
			echo 1;
		}
	}

	/**
	 * 502 Получить операции по отслеживаемым отправлениям
	 */
	public function get() {

		$codes = file('32073_data.txt');

		$package = array();
		$assoc = array();
		foreach($codes as $code)
		{
			$code = str_replace(array("\r","\n"),'',$code);
			$package[] = $code;
			if(count($package) > 500)
			{
				$xml_respond = $this->sendHTTPRequest(502, $package);
				$xml = new XMLReader();
				$xml->xml($xml_respond);
				$result = $this->xml2assoc($xml);
				$xml->close();

				foreach($result[0]['val'] as $parcel)
				{
					foreach($parcel['val'] as $operation)
					{
						$operation['atr']['barcode'] = $parcel['atr']['barcode'];
						$assoc[] = $operation;
					}
				}

				$package = array();
				echo 1;
			}
		}
		if($package)
		{
			$xml_respond = $this->sendHTTPRequest(502, $package);
			$xml = new XMLReader();
			$xml->xml($xml_respond);
			$result = $this->xml2assoc($xml);
			$xml->close();

			foreach($result[0]['val'] as $parcel)
			{
				foreach($parcel['val'] as $operation)
				{
					$operation['atr']['barcode'] = $parcel['atr']['barcode'];
					$assoc[] = $operation;
				}
			}

			$package = array();
			echo 1;
		}

		echo "\n\n";
		echo "status count: ".count($assoc);
		echo "\n\n";
		$assoc = array(array('val'=>$assoc));


		// проход по всем operation
		$i=0;
		$j=0;
		foreach ( $assoc[0]['val'] as $parcel ) {
			$i++;
			if($i>=1000)
			{
				$j += $i;
				$i=0;
				echo $j."\n";
			}
			if ( !isset($parcel['atr']['barcode']) || empty($parcel['atr']['barcode'])
				|| !isset($parcel['atr']['type']) || empty($parcel['atr']['type'])
				|| !isset($parcel['atr']['category']) || !isset($parcel['atr']['zip'])
			) continue;
			$barcode = intval($parcel['atr']['barcode']);
			$type = intval($parcel['atr']['type']);
			$category = intval($parcel['atr']['category']);
			$zip = intval($parcel['atr']['zip']);
			$date = 0;
			if (isset($parcel['atr']['date']) && !empty($parcel['atr']['date'])) {
				$date = date('d.m.Y', strtotime($parcel['atr']['date']));
			}

			$order_id = coreDB::dbGetValue("
				SELECT o.id
				FROM tbl_order as o
				JOIN tbl_order_delivery as od ON od.id = o.id
				WHERE od.post_barcode = '{$barcode}'
			");
			if ( !$order_id ) {
				array_push($this->EndStatusBarcodes, $barcode);
				continue;
			}

			$order = new OrderDB($order_id);

			// #23867 если текущий статус заказ >=30 , то ничего не меняем.
			if ( $order->status >= 30 ) {
				array_push($this->EndStatusBarcodes, $barcode);
				continue;
			}

			// Приём
			if ( 1 == $type ) {
				$order->substatus = 24; // Посылка: принят на почту
			}
			// Вручение
			else if ( 2 == $type ) {
				// Вручение адресату
				if ( 1 == $category ) {
					// наложеный платёж
					if ( in_array($order->payment_type, array(7, 11)) ) {
						$order->substatus = 27; // Посылка: вручен получателю
					}
					// предоплаченные #30381
					else if ( in_array($order->payment_type, array(2, 3, 4, 5, 6, 9))
						|| -1 == $order->getDelivery()->type_id ) {
						$order->substatus = null; // сброс
						$order->status = 50;
					}
				}
				// Вручение отправителю
				else if ( 2 == $category ) {
					// TODO Вопрос: этот ордер будет запрашиваться каждый раз у Беты?
				}
			}
			// Возврат
			else if ( 3 == $type ) {
				// Истёк срок хранения
				if ( 1 == $category ) {
					$order->substatus = 42;
				}
				// Заявление отправителя
				else if ( 2 == $category ) {
					$order->substatus = 43;
				}
				// Отсутствие адресата по указанному адресу
				else if ( 3 == $category ) {
					$order->substatus = 44;

					// TODO Какая очередь?
				}
				// Отказ адресата
				else if ( 4 == $category ) {
					$order->substatus = 45;

					// TODO Какая очередь?
				}
				// Смерть адресата
				else if ( 5 == $category ) {
					$order->substatus = 46;

					// TODO какая очередь?
				}
				// Невозможно прочесть адрес адресата
				else if ( 6 == $category ) {
					$order->substatus = 47;
				}
				// Возврат таможни
				else if ( 7 == $category ) {
					$order->substatus = 48;
				}
				// Адресат, а\я указан не правильно
				else if ( 8 == $category ) {
					$order->substatus = 49;

					// TODO Надо ли в очередь?
				}
				// Иные обстоятельства
				else if ( 9 == $category ) {
					$order->substatus = 50;
				}
				// Новая причина
				else {
					$order->substatus = 51;
				}

				array_push($this->EndStatusBarcodes, $barcode);
			}
			// Обработка
			else if ( 8 == $type ) {
				// Прибыло в место вручения
				if ( 2 == $category ) {
					// #23563 Только если индексы совпадают
					if ( $order->getDelivery()->post_index != $zip ) continue;

					$order->substatus = 25; // Посылка: в месте вручения

					// Расслыка по 57 шаблону и по 59 по прошествии 14,14, 21 дней. Отключил по #25020
//					Mail::send(array('order_id' => $order->id), 57, $order->customer_id );

					// #25020
					if ($date) $order->getDelivery()->delivery_date = $date;

					// #22375 Записываем дату установки 25 подстатуса при получении такого подстатуса
					$additional = $order->getDelivery()->additional;
					$additional += array(self::$set25substatus_name => date('d.m.Y'));
					$order->getDelivery()->additional = $additional;
					$order->getDelivery()->update();
				}
			}
			// если не было изменений, переход к следующему элементу
			else {
				continue;
			}

			// #22375 Если пришел от почты другой подстатус
			if ( 25 != $order->substatus ) $this->clearAdditional($order);

			$order->update();

		} // end foreach

		// выключение обслуживания для ШПИ с конечным статусом
		$this->unsubscriber();
	}

	/**
	 * 504 Выключить отслеживание отправления
	 */
	public function unsubscriber() {
		if ( 0 == count($this->EndStatusBarcodes) ) return;

		$xml_respond = $this->sendHTTPRequest(504, $this->EndStatusBarcodes);

		$xml = new XMLReader();
		$xml->xml($xml_respond);
		$assoc = $this->xml2assoc($xml);
		$xml->close();

		// Проверка ошибок
		if ( !isset($assoc[0]['atr']['state']) || $assoc[0]['atr']['state'] != 0 ) {
			Mail::sendMessageToEmail(basename(__FILE__) ." unsubscriber", 'Ошибка при отписке ШПИ в Бету (504).
				Получен ответ: '. var_export($assoc, true), self::$recipients);
			return;
		}
	}

	/**
	 * Удаление указанной даты стмены статуса
	 *
	 * @param OrderDB $order
	 */
	protected function clearAdditional(&$order) {
		$additional = $order->getDelivery()->additional;
		unset($additional[self::$set25substatus_name]);
		$order->getDelivery()->additional = (count($additional) ? $additional : null);
		$order->getDelivery()->update();
	}

	/**
	 * Отправка запроса и возврат ответа
	 *
	 * @param int $request_type - тип запроса
	 * @param array $barcodeArr - массив ШПИ
	 * @return string
	 */
	protected function sendHTTPRequest($request_type, $barcodeArr = array(), $upd_seq = 0) {
		$xml = "";
		$request = '';

		// запрос данных о поиске
		if ( 505 == $request_type ) {
			$request = '<?xml version="1.0" encoding="UTF-8"?>
<request partner_id="'. self::$partner_id .'" password="'. self::$password .'" request_type="'
				. $request_type .'" immediate="1" upd_seq="'. $upd_seq .'"/>';
		}
		else {
			if ( count($barcodeArr) == 0 ) return $xml;

			// формирование запроса
			foreach ( $barcodeArr as $code ) $request .= '<parcel barcode="'. $code .'"/>'. "\n";
			$request = '<?xml version="1.0" encoding="UTF-8"?>
<request partner_id="'. self::$partner_id .'" password="'. self::$password .'" request_type="'. $request_type .'" immediate="1">
'. $request .'</request>';
		}

		if (self::$debug) LogErrors::add($request, self::$log);

		// инициализация curl
		$curl = curl_init();
		curl_setopt($curl,CURLOPT_URL,'https://fb.mirknigi.ru:8080/cgi-bin/fb.pl');
		curl_setopt($curl,CURLOPT_POST,1);
		curl_setopt($curl,CURLOPT_CONNECTTIMEOUT,5);
		curl_setopt($curl,CURLOPT_HTTPHEADER,array(
			'Content-Type: text/xml',
			'Content-Length: '.mb_strlen($request),
			'Connection: close'
		));
		curl_setopt($curl,CURLOPT_POSTFIELDS,$request);
		curl_setopt($curl,CURLOPT_HEADER,0);
		curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
		curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
		$answer = curl_exec($curl);
		curl_close($curl);

		// удаляю поле из response, т.к. XMLReader здесь падает
		$xml = preg_replace('#xmlns="FBox/XMLSchema"#', '', $answer);

		if (self::$debug) LogErrors::add($xml, self::$log);

		return $xml;
	}

	/**
	 * Парсинг XML в PHP-array
	 *
	 * Пример:
	 * $xml = new XMLReader();
	 * $xml->xml($xml_string);
	 * $assoc = $this->xml2assoc($xml);
	 * $xml->close();
	 *
	 * @param XMLReader $xml - экземпляр с XML
	 * @return array
	 */
	protected function xml2assoc(&$xml){
		$assoc = array();
		$n = 0;
		while($xml->read()){
			if($xml->nodeType == XMLReader::END_ELEMENT) break;
			if($xml->nodeType == XMLReader::ELEMENT and !$xml->isEmptyElement){
				$assoc[$n]['name'] = $xml->name;
				if($xml->hasAttributes) while($xml->moveToNextAttribute()) $assoc[$n]['atr'][$xml->name] = $xml->value;
				$assoc[$n]['val'] = $this->xml2assoc($xml);
				$n++;
			}
			else if($xml->isEmptyElement){
				$assoc[$n]['name'] = $xml->name;
				if($xml->hasAttributes) while($xml->moveToNextAttribute()) $assoc[$n]['atr'][$xml->name] = $xml->value;
				$assoc[$n]['val'] = "";
				$n++;
			}
			else if($xml->nodeType == XMLReader::TEXT) {

			}
		}

		return $assoc;
	}
}

$c = new getPostOrdersStatusFromBeta2();

if ($argv[1] == 'get')
	$c->get();

if ($argv[1] == 'put')
	$c->putToTrack();
