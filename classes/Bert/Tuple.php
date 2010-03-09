<?php

class Bert_Tuple
	implements ArrayAccess, IteratorAggregate, Countable
{
	private $_data = array();

	public function __construct($arr)
	{
		$this->_data = $arr;
	}

	public function count()
	{
		return count($this->_data);
	}

	public function offsetExists($offset)
	{
		return isset($this->_data[$offset]);
	}

	public function offsetGet($offset)
	{
		return isset($this->_data[$offset]) ? $this->_data[$offset] : null;
	}

	public function offsetSet($offset, $value)
	{
		if (isset($offset))
			$this->_data[$offset] = $value;
		else
			$this->_data []= $value;
	}

	public function offsetUnset($offset)
	{
		unset($this->_data[$offset]);
	}

	public function getIterator()
	{
		return new ArrayIterator($this->_data);
	}
}

