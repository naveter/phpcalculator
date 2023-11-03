<?php


$to_array = "ABCDE"; //FGHIJKLMNOPQRSTUVWXYZ";
$base = strlen($to_array);

$src = intval($argv[1],10);

$res = '';
$current_rank = $base;
do
{
        $index = $src%$base;
        $res = $to_array[$index].$res;
        $src = floor(($src-$base)/$base);
} while($src >= 0);

print_r($res);
echo "\n";


function fillLettersArray($need_count = 0) {
	if (0 == $need_count) throw new Exception("Не возможно работать с нулевым массивом");
	if (676 < $need_count) throw new Exception("Слишком большой размер массива");
	$abc1Arr = range('A', 'Z');
	$abc2Arr = $abc1Arr;
	array_unshift($abc2Arr, '');
	$letters = array();
	$count = 0;
	
	while (1) {
		$key2 = intval($count/count($abc1Arr));
		foreach ( $abc1Arr as $lett ) {
			array_push($letters, $abc2Arr[$key2].$lett);
			$count++;
			
			if ($count == $need_count) break 2;
		}
	}

	return $letters;
}

//print_r( fillLettersArray(500) );

// супер-алгоритм перевода 10-го в 26-ое число
function do10to26($need_count) {
	$abcArr = range('A', 'Z');
	$count = 0;
	$letters = array();
	
	for ($i = 1; $i <= $need_count; $i++) {
		$div = 0;
		$letter = '';
		
		do {
			$div = intval($i/count($abcArr));
			
			if ($div <= count($abcArr)) {
				$letter .= $abcArr[ $div ];
			}
			else {
				$key = $i - count($abcArr)*count($abcArr);
			}
		}
		while ($key > 0);
	
	}

}

//print_r( do10to26(100) );

function test1() {
	$letters = array('A', 'B', 'C', 'D', 'E');
	$number = 19530; // 5 в 6 степени + остальные члены. Должно получиться EEEEEE
	$cnumber = 1;
    $collections = array();
	
    foreach ($number as $num) {
        $strResult = '';
        while ($num != $cnumber) {








        }
        
        array_push($collections, $strResult);
    }
}

function fillLettersArray2($need_count = 0, $debug = false) {
	$abc1Arr = range('A', 'E');
	$abc2Arr = $abc1Arr;
	array_unshift($abc2Arr, '');
	$letters = array();
	$count = 1;
    $cntLettersArr = count($abc1Arr);
    $max1 = $cntLettersArr;
    $max2 = pow($cntLettersArr, 2) + $cntLettersArr;
    $max3 = pow($cntLettersArr, 3) + pow($cntLettersArr,2) + $cntLettersArr;
    $max4 = pow($cntLettersArr, 4) + pow($cntLettersArr, 3) + pow($cntLettersArr,2) + $cntLettersArr;
    $max5 = pow($cntLettersArr, 5) + pow($cntLettersArr, 4) + pow($cntLettersArr, 3) + pow($cntLettersArr,2) + $cntLettersArr;
    
    if ($debug) print "max: ". implode(' ', array($max2, $max3, $max4, $max5)) ."\n";
	
	while (1) {
        $key2 = $key3 = $key4 = $key5 = 0;
        $num = $count;
        if ($debug)  print 'num='. $num .' ';
//        if ( $num > $max4 && $num <= $max5 ) {
//            $key5 = intval($count/$max4);
//            $num -= $key5 * $max4;
//        }
//        if ( $num > $max3 && $num <= $max4 ) {
//            $key4 = intval($count/$max3);
//            $num -= $key4 * $max3;
//        }
        if ( $num > $max2 && $num <= $max3 ) {
            if ($debug) print "num3b=". $num ." ";
            $key3 = intval($num/pow($cntLettersArr, 2));
            $num -= $key3 * pow($cntLettersArr, 2);
//            $key2 = $key1 = 1;
//            $num += $max1;
            
            if ($debug) print "num3=". $num ." ";
        }
        if ( $num > $max1 && $num <= $max2 ) {
            if ($debug) print "num2b=". $num ." ";
            $key2 = intval($num/pow($cntLettersArr, 1));
            $num -= $key2 * pow($cntLettersArr, 1);
//            $key1 = 1;
            
            if ($debug) print "num2=". $num ." ";
        }
        
        print $count ." ". implode('.', array($key5, $key4, $key3, $key2)) .".1\n";
        
		foreach ( $abc1Arr as $lett ) {
			//array_push($letters, $abc2Arr[$key5].$abc2Arr[$key4].$abc2Arr[$key3].$abc2Arr[$key2].$lett);
			$count++;
			
			if ($count == $need_count) break 2;
		}
	}

	return $letters;
}
 
//print_r( fillLettersArray2(125, true) );





