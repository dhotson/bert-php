<?php

class Bert_Rpc_Service
{
	public $host;
	public $port;
	public $timeout;

	public function __construct($host, $port, $timeout = null)
	{
		$this->host = $host;
		$this->port = $port;
		$this->timeout = $timeout;
	}

	public function call($options = null)
	{
		$this->_verifyOptions($options);
		return new Bert_Rpc_Request($this, Bert::a('call'), $options);
	}

	public function cast($options = null)
	{
		$this->_verifyOptions($options);
		return new Bert_Rpc_Request($this, Bert::a('cast'), $options);
	}

	// --

	private function _verifyOptions($options)
	{
		if (isset($options))
		{
			if ($cache = $options['cache'])
			{
				if ($cache[0] != 'validation' || !is_string($cache[1]))
				{
					throw new Exception('Valid :cache args are ["validation", String]');
				}
			}
			else
			{
				throw new Exception('Valid options are "cache"');
			}
		}
	}
}
