<?php
require_once 'classes/Bert.php';
$svc = new Bert_Rpc_Service('localhost', 8000);
$r = $svc->call()->calculator()->add(1, 2);
var_dump($r);
