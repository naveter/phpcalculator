<?php

abstract class Base {
    protected static $instance = null;
    
    public static function getInstance() {        
        if( empty(self::$instance) )
        {
            $className = get_called_class();
            self::$instance = new $className();
        }
        return self::$instance;
    }

}

class First extends Base {
//	protected static $instance = null;
}

class Second extends Base {
//	protected static $instance = null;
}

$first = First::getInstance();
print "first: ". get_class($first) ."\n";

$second = Second::getInstance();
print "second: ". get_class($second) ."\n";

print "first: ". get_class($first) ."\n";

/*
C:\work\phpcalculator.1\examples>php lateStaticBindingError.php
first: First
second: First
first: First

*/