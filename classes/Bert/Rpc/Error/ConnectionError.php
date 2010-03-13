<?php

class Bert_Rpc_Error_ConnectionError extends Bert_Rpc_Error
{
	public function __construct($host, $port, $message = '')
	{
		parent::__construct("Unable to connect to $host:$port '$message'");
	}
}
