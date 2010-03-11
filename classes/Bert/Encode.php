<?php

class Bert_Encode
{
	private $_out;

	public function __construct($out)
	{
		$this->_out = $out;
	}

	public static function encode($obj)
	{
		$fp = fopen('php://memory', 'rw');
		$encode = new self($fp);
		$encode->writeAny($obj);
		rewind($fp);
		return stream_get_contents($fp);
	}

	public function writeAny($obj)
	{
		$this->write1(Bert_Types::MAGIC);
		$this->writeAnyRaw($obj);
	}

	public function writeAnyRaw($obj)
	{
		if ($obj instanceof Bert_Atom)
			return $this->writeSymbol($obj);
		elseif (is_integer($obj))
			return $this->writeInteger($obj);
		elseif (is_string($obj) && preg_match('/^-?[1-9][0-9]+$/', $obj))
			return $this->writeBignum($obj);
		elseif (is_float($obj))
			return $this->writeFloat($obj);
		elseif ($obj instanceof Bert_Tuple)
			return $this->writeTuple($obj);
		elseif (is_array($obj))
			return $this->writeList($obj);
		elseif (is_string($obj))
			return $this->writeBinary($obj);
		else
			$this->_fail($obj);
	}

	public function write1($byte)
	{
		fwrite($this->_out, pack('C', $byte));
	}

	public function write2($short)
	{
		fwrite($this->_out, pack('n', $short));
	}

	public function write4($long)
	{
		fwrite($this->_out, pack('N', $long));
	}

	public function writeString($string)
	{
		fwrite($this->_out, $string);
	}

	public function writeBoolean($bool)
	{
		$val = ($bool === true)
			? Bert_Atom::true()
			: Bert_Atom::false();

		$this->writeSymbol($val);
	}

	public function writeSymbol($str)
	{
		$data = "$str";
		$this->write1(Bert_Types::ATOM);
		$this->write2(strlen($data));
		$this->writeString($data);
	}

	public function writeInteger($num)
	{
		if ($num >= 0 && $num < 256)
		{
			$this->write1(Bert_Types::SMALL_INT);
			$this->write1($num);
		}
		elseif ($num <= Bert_Types::MAX_INT && $num >= Bert_Types::MIN_INT)
		{
			$this->write1(Bert_Types::INT);
			$this->write4($num);
		}
		else
		{
			$this->writeBignum($num);
		}
	}

	public function writeFloat($float)
	{
		$this->write1(Bert_Types::FLOAT);
		$this->writeString(sprintf('%15.15e', $float));
	}

	public function writeBignum($num)
	{
		$negative = bccomp($num, '0') < 0;

		// Absolute
		if ($negative)
			$num = bcmul('-1', $num);

		// Convert $num to base-256
		$values = array();
		while (bccomp($num, 0) > 0)
		{
			$values []= intval(bcmod($num, '256'));
			$num = bcdiv($num, '256');
		}

		if (count($values) < 256)
		{
			$this->write1(Bert_Types::SMALL_BIGNUM);
			$this->write1(count($values));
			$this->write1($negative ? 1 : 0);
			foreach ($values as $v)
				$this->write1($v);
		}
		else
		{
			$this->write1(Bert_Types::LARGE_BIGNUM);
			$this->write4(count($values));
			$this->write1($negative ? 1 : 0);
			foreach ($values as $v)
				$this->write1($v);
		}
	}

	public function writeTuple($data)
	{
		if (!is_array($data) && !($data instanceof Bert_Tuple))
			$this->_fail($data);

		if (count($data) < 256)
		{
			$this->write1(Bert_Types::SMALL_TUPLE);
			$this->write1(count($data));
		}
		else
		{
			$this->write1(Bert_Types::LARGE_TUPLE);
			$this->write4(count($data));
		}

		foreach ($data as $val)
		{
			$this->writeAnyRaw($val);
		}
	}

	public function writeList($data)
	{
		if (!is_array($data))
			$this->_fail($data);

		if (empty($data))
			return $this->write1(Bert_Types::NIL);

		$this->write1(Bert_Types::LISTTYPE);
		$this->write4(count($data));

		foreach ($data as $val)
		{
			$this->writeAnyRaw($val);
		}

		$this->write1(Bert_Types::NIL);
	}

	public function writeBinary($data)
	{
		$this->write1(Bert_Types::BIN);
		$this->write4(strlen($data));
		$this->writeString($data);
	}

	private function _fail($str)
	{
		throw new Exception("Cannot encode to erlang external format: $str");
	}
}
