<?php

class Bert_Rpc_Action_RpcError extends Exception
{
	public function __construct($code, $msg = null, $class = null, $bt = array())
	{
		parent::__construct("RPC Error - Code: '$code', Message: '$msg', Class: '$class', Trace: ".print_r($bt, true));
	}
}
