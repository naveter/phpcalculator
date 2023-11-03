<?php //

// инициализация переменных в условии
//if ( ($var = array('second')) && array_push($var, "first") ) print_r($var);

// пустой массив
//$array = array();
//if ( !$array ) print "<br>array is empty<br>";

// анонимные фугкции внутри массива
//$array = array(
//    'first' => function(){return "first return";},
//    'second' => function($param) { print $param . "<br>"; },
//);
//print $array["first"]()."<br>" ;
//$array["second"]("some string");

// формирование поточных вызовов в смарти
//{$user.city_id|lst:'lst_city'} {if $user.city_id != 1}({$user.city_id|lst:'lst_city':'region_id'|lst:'lst_region'}){/if}

// действие для каждого элемента массива. оригинальный способ записи
//if ( ($total = 1) 
//     && ( --$total ? 0 : 1)
//     && ($products = array('milk' => 20, 'sugar' => 30, 'meat' => 40)) 
//     && array_walk($products, function ($val, $key) use (&$total) { $total += $val; })) print $total; 




