<?php

require_once 'classes/Bert.php';
require_once 'classes/Ernie.php';

Ernie::mod('calculator', array(
	'add' => function($a, $b) { return $a + $b; },
	'subtract' => function($a, $b) { return $a - $b; },
));

Ernie::start();
