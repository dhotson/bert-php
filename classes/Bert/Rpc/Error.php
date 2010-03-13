<?php

class Bert_Rpc_Error extends Exception
{
	private $_originalException;
	private $_bt;


	public function __construct($msg = null, $class = null, $bt = array())
	{
		if (is_array($msg))
			list($code, $message) = $msg;
		else
			list($code, $message) = array(0, $msg);

		if (isset($class))
			$this->_originalException = new Bert_Rpc_Error_RemoteError("$class: $message");
		else
			$this->_originalException = $this;

		parent::__construct($message, $code);
	}

	public function __toString()
	{
		return sprintf("exception '%s' with message '%s' and code '%d'\nStack trace:\n%s\n",
			get_class($this),
			$this->message,
			$this->code,
			implode("\n", $this->_bt)
		);
	}
}
