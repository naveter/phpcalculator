<?php
/**
 * Получение доступа ко всем приватным методам и полям, включая конструктор.
 * 
 * @author ilya.gulevskiy
 * @date 15.05.2015
 */

class Father {
	public $opened = 'open';
	protected $defend = 'defend';
	private $denied = 'denied';
	protected static $static_defend = 'static_defend';
	private static $static_denied = 'static_denied';
	
	protected function __construct($first, $second){
		print __METHOD__. " ". $first ."\n";
		print_r($second);
	}
	
	public function common(){
		print __METHOD__."\n";
	}
	
	protected function defend(){
		print __METHOD__."\n";
	}
	
	private function denied(){
		print __METHOD__."\n";
		
		$this->_denied();
	}
	
	private function _denied(){
		print __METHOD__."\n";
	}
	
	protected static function static_defend(){
		print __METHOD__."\n";
	}
	
	private static function static_denied(){
		print __METHOD__."\n". self::$static_denied."\n";
	} 
}

class PublicMorozov {
	protected $refl = null;
	protected $class = null;
	protected $classname = null;
	
	/**
	 * Создаёт и возвращает экземпляр ReflectionClass
	 * 
	 * @return ReflectionClass
	 */
	protected function getRefClass(){
		if (!is_null($this->refl)) return $this->refl;
		
		$this->refl = new ReflectionClass($this->classname);
		return $this->refl;
	}
	
	/**
	 * @param string $classname - название класса-отца, которого нужно "выдать"
	 * @param array $args - список аргументов для инициализации объекта отца
	 */
	public function __construct($classname, $args) {
		$classname = trim($classname);
		if (!class_exists($classname)) throw new Exception("Class $classname does not exists");
		$this->classname = $classname;
		
		// Генерируем новое не существующее имя для класса-наследника
		$classname_new = $this->classname;
		do {
			$classname_new = $this->classname ."_". rand();
		} while (class_exists($classname_new));
		
		// Создаём класс-наследник, т.к. в PHP 5.3 я не нашёл иной 
		// способ получить доступ к private конструктору
		eval ("
			class ". $classname_new ." extends ". $this->classname ." {
				public function __construct() {
					\$ref = new ReflectionClass (get_parent_class());
					\$construct = \$ref->getConstructor();
					\$construct->setAccessible(true);					
					\$args = func_get_args();
					
					return eval(\"\\\$construct->invokeArgs(\\\$this, \\\$args );\");
				}
			}
		");

		// Подготовка аргументов, без преобразования в string
		$argsArr = array();
		if (count($args) > 0) {
			for ($i = 0; $i < count($args); $i++) $argsArr[] = "\$args[". $i ."]";
		}
		
		eval("\$class = new $classname_new(". implode(',', $argsArr) .");");
		$this->class = $class;
		
	}
	
	/**
	 * Вызов private, protected и static методов
	 * 
	 * @param string $name - название метода
	 * @param array $arg - аргументы
	 * @return *
	 */
	public function __call($name, $arg) {
		$method = $this->getRefClass()->getMethod($name);
		$method->setAccessible(true);
		
		return $method->invoke($this->class, $arg);
	}
	
	/**
	 * Получить значение private, protected и static свойств
	 * 
	 * @param string $name - название свойства
	 * @return *
	 */
	public function __get($name) {
		$reflectionProperty = $this->getRefClass()->getProperty($name);
		$reflectionProperty->setAccessible(true);
		
		return $reflectionProperty->getValue($this->class);
	}
	
	/**
	 * Установить значение private, protected и static свойств
	 * 
	 * @param string $name - название свойства
	 * @param $val - значение
	 * @return *
	 */
	public function __set($name, $val) {
		$reflectionProperty = static::getRefClass()->getProperty($name);
		$reflectionProperty->setAccessible(true);
		
		$reflectionProperty->setValue($this->class, $val);
	} 
	
	/**
	 * Вызов private, protected static методов
	 * 
	 * @param string $name - название метода
	 * @param array $arg - аргументы
	 * @return *
	 */
//	public static function __callStatic($name, $arg) {
//		$method = static::getRefClass()->getMethod ($name);
//		$method->setAccessible(true);
//		
//		return $method->invoke(get_parent_class(), $arg);
//	}
	
}

$std = new stdClass();
$obj = new PublicMorozov("Father", array('Karl', $std));
$obj->denied();
$obj->defend();
$obj->static_denied();
$obj->static_defend();

$obj->defend = "set defend from code";
print $obj->defend ."\n";
print $obj->denied ."\n";

$obj->static_defend = "set static defend from code";
print $obj->static_defend ."\n";
print $obj->static_denied ."\n";
