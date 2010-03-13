<?php

class Bert_Rpc_Error_ReadTimeoutError extends Bert_Rpc_Error
{
	public $host;
	public $port;
	public $timeout;

	public function __construct($host, $port, $timeout)
	{
		$this->host = $host;
		$this->port = $port;
		$this->timeout = $timeout;

		parent::__construct("No response from $host:$port in $timeout");
	}
}
