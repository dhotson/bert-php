<?php

class Bert_Rpc_Service
{
	private $_host;
	private $_port;
	private $_timeout;

	public function __construct($host, $port, $timeout = null)
	{
		$this->_host = $host;
		$this->_port = $port;
		$this->_timeout = $timeout;
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
