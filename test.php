<?php

$intervalo = date_diff(date_create(), date_create('01.10.2013'));
$out = $intervalo->format("-%M month -%Y year");

print_r($out);



