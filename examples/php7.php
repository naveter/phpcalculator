<?

// Scalar type declarations 
// Return type declarations
function sumOfInts(int $int ): array
{
	
}

// Null coalescing operator 
$username = $_GET['user'] ?? 'nobody';

// Spaceship operator
echo 1 <=> 1; // 0
echo 1 <=> 2; // -1
echo 2 <=> 1; // 1


// Anonimous classes
$app = new Application;
$app->setLogger(new class implements Logger {
    public function log(string $msg) {
        echo $msg;
    }
});

// Closure::call
$getX = function() {return $this->x;};
echo $getX->call(new A);

// Filtered unserialize()
$data = unserialize($foo, ["allowed_classes" => ["MyClass", "MyClass2"]]);






