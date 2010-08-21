<?php

class Bert_Rpc_Request
{
	private $_svc;
	public $kind;
	public $options;

	public function __construct($svc, $kind, $options)
	{
		$this->_svc = $svc;
		$this->kind = $kind;
		$this->options = $options;
	}

	public function __call($cmd, $args)
	{
		return new Bert_Rpc_Module($this->_svc, $this, new Bert_Atom($cmd));
	}
}
