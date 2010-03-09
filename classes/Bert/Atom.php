<?php

class Bert_Atom
{
	public static function bert() { return new Bert_Atom('bert'); }
	public static function true() { return new Bert_Atom('true'); }
	public static function false() { return new Bert_Atom('false'); }
	public static function nil() { return new Bert_Atom('nil'); }

	private $_name;

	public function __construct($name)
	{
		$this->_name = $name;
	}

	public function __toString()
	{
		return $this->_name;
	}
}
