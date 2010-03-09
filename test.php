<?php

require_once 'classes/Bert.php';

$o = array(
	'abc' => 'def',
	123 => null,
);

var_dump($o);

$bert = Bert::encode($o);
var_dump($bert);

$obj = Bert::decode($bert);
var_dump($obj);



