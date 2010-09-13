<?php

class Bert_Decode
{
	private $_input;
	private $_peeked = '';

	/**
	 * @param $input A file/stream like resource
	 */
	public function __construct($input)
	{
		$this->_input = $input;
	}

	public static function decode($string)
	{
		$decode = new self(fopen('data:application/octet-stream;base64,'.base64_encode($string), 'r'));
		return $decode->readAny();
	}

	public function readAny()
	{
		if ($this->read1() !== Bert_Types::MAGIC)
			$this->_fail('Bad Magic');

		return $this->readAnyRaw();
	}

	public function readAnyRaw()
	{
		$val = $this->peek1();
		switch ($val)
		{
			case Bert_Types::ATOM: return $this->readAtom();
			case Bert_Types::SMALL_INT: return $this->readSmallInt();
			case Bert_Types::INT: return $this->readInt();
			case Bert_Types::SMALL_BIGNUM: return $this->readSmallBignum();
			case Bert_Types::LARGE_BIGNUM: return $this->readLargeBignum();
			case Bert_Types::FLOAT: return $this->readFloat();
			case Bert_Types::SMALL_TUPLE: return $this->readSmallTuple();
			case Bert_Types::LARGE_TUPLE: return $this->readLargeTuple();
			case Bert_Types::NIL: return $this->readNil();
			case Bert_Types::STRING: return $this->readErlString();
			case Bert_Types::LISTTYPE: return $this->readList();
			case Bert_Types::BIN: return $this->readBin();
			default:
				$this->_fail('Unknown term tag: "'.$this->peek1().'"');
		}
	}

	public function read($length)
	{
		if ($length < strlen($this->_peeked))
		{
			$result = substr($this->_peeked, 0, $length);
			$this->_peeked = substr($this->_peeked, $length);
			$length = 0;
		}
		else
		{
			$result = $this->_peeked;
			$this->_peeked = '';
			$length = $length - strlen($result);
		}


		if ($length > 0)
		{
			$tmp = fread($this->_input, $length - strlen($this->_peeked));
			$result .= $tmp;
		}

		return $result;
	}

	public function peek($length)
	{
		if ($length <= strlen($this->_peeked))
		{
			return substr($this->_peeked, 0, $length);
		}
		else
		{
			$readBytes = fread($this->_input, $length - strlen($this->_peeked));

			if ($readBytes)
				$this->_peeked .= $readBytes;

			return $this->_peeked;
		}
	}

	public function peek1()
	{
		return array_shift(unpack('C', $this->peek(1)));
	}

	public function peek2()
	{
		return array_shift(unpack('n', $this->peek(2)));
	}

	public function read1()
	{
		return array_shift(unpack('C', $this->read(1)));
	}

	public function read2()
	{
		return array_shift(unpack('n', $this->read(2)));
	}

	public function read4()
	{
		return array_shift(unpack('N', $this->read(4)));
	}

	public function readString($length)
	{
		return $this->read($length);
	}

	public function readAtom()
	{
		if ($this->read1() !== Bert_Types::ATOM)
			$this->_fail('Invalid Type, not an atom');

		$length = $this->read2();
		$str = $this->readString($length);

		return new Bert_Atom($str);
	}

	public function readSmallInt()
	{
		if ($this->read1() !== Bert_Types::SMALL_INT)
			$this->_fail('Invalid Type, not a small int');

		return $this->read1();
	}

	public function readInt()
	{
		if ($this->read1() !== Bert_Types::INT)
			$this->_fail('Invalid Type, not a small int');

		$value = $this->read4();
		$negative = ($value >> 31) & 1 === 1;

		if ($negative)
			$value = ($value - (1 << 32));

		return $value;
	}

	public function readSmallBignum()
	{
		if ($this->read1() !== Bert_Types::SMALL_BIGNUM)
			$this->_fail('Invalid Type, not a small bignum');

		$count = $this->read1();
		$negative = $this->read1();

		$result = '0';
		for ($i=0; $i<$count; $i++)
		{
			$val = $this->read1();
			$result = bcadd("$result", bcmul($val, bcpow('256',$i))); // $result += $val * (256 ^ $i)
		}

		if ($negative)
			$result = bcmul('-1', $result);

		return $result;
	}

	public function readLargeBignum()
	{
		if ($this->read1() !== Bert_Types::LARGE_BIGNUM)
			$this->_fail('Invalid Type, not a large bignum');

		$count = $this->read4();
		$negative = $this->read1();

		$result = '0';
		for ($i=0; $i<$count; $i++)
		{
			$val = $this->read1();
			$result = bcadd("$result", bcmul($val, bcpow('256',$i))); // $result += $val * (256 ^ $i)
		}

		if ($negative)
			$result = bcmul('-1', $result);

		return $result;
	}

	public function readFloat()
	{
		if ($this->read1() !== Bert_Types::FLOAT)
			$this->_fail('Invalid Type, not a float');

		$str = $this->readString(31);
		return floatval($str);
	}

	public function readSmallTuple()
	{
		if ($this->read1() !== Bert_Types::SMALL_TUPLE)
			$this->_fail('Invalid Type, not a small tuple');

		return $this->readTuple($this->read1());
	}

	public function readLargeTuple()
	{
		if ($this->read1() !== Bert_Types::LARGE_TUPLE)
			$this->_fail('Invalid Type, not a large tuple');

		return $this->readTuple($this->read4());
	}

	public function readTuple($arity)
	{
		if ($arity > 0)
		{
			$tag = $this->readAnyRaw();
			if (is_object($tag) && $tag == Bert::a('bert'))
			{
				return $this->readComplexType($arity);
			}
			else
			{
				$tuple = array();
				$tuple []= $tag;

				for ($i=0; $i<$arity - 1; $i++)
					$tuple []= $this->readAnyRaw();

				return new Bert_Tuple($tuple);
			}
		}
		else
		{
			return new Bert_Tuple();
		}
	}

	public function readComplexType()
	{
		$val = $this->readAnyRaw();

		if ($val == Bert::a('nil'))
			return null;
		elseif ($val == Bert::a('true'))
			return true;
		elseif ($val == Bert::a('false'))
			return false;
		elseif ($val == Bert::a('time'))
			return new Bert_Time($this->readAnyRaw(), $this->readAnyRaw(), $this->readAnyRaw());
		elseif ($val == Bert::a('regex'))
		{
			$source = $this->readAnyRaw();
			$opts = $this->readAnyRaw();

			$options = array();
			foreach ($opts as $name)
				$options []= "$name";

			return new Bert_Regex($source, $options);
		}
		elseif ($val == Bert::a('dict'))
			return $this->readDict();
		else
			return null;
	}

	public function readDict()
	{
		$type = $this->read1();
		if (!in_array($type, array(Bert_Types::LISTTYPE, Bert_Types::NIL)))
			$this->_fail('Invalid dict spec, not an erlang list');

		if ($type === Bert_Types::LISTTYPE)
			$length = $this->read4();
		else
			$length = 0;

		$arr = array();
		for ($i=0; $i<$length; $i++)
		{
			$pair = $this->readAnyRaw();
			$arr[$pair[0]] = $pair[1];
		}

		if ($type === Bert_Types::LISTTYPE)
			$this->read1();

		return $arr;
	}

	public function readNil()
	{
		if ($this->read1() !== Bert_Types::NIL)
			$this->_fail('Invalid Type, not a nil list');

		return array();
	}

	public function readErlString()
	{
		if ($this->read1() !== Bert_Types::STRING)
			$this->_fail('Invalid Type, not an erlang string');

		$length = $this->read2();
		return unpack('C'.$length, $this->readString($length));
	}

	public function readList()
	{
		if ($this->read1() !== Bert_Types::LISTTYPE)
			$this->_fail('Invalid Type, not an erlang list');

		$length = $this->read4();
		$list = array();
		for ($i=0; $i<$length; $i++)
			$list []= $this->readAnyRaw();

		$this->read1();

		return $list;
	}

	public function readBin()
	{
		if ($this->read1() !== Bert_Types::BIN)
			$this->_fail('Invalid Type, not an erlang binary');

		$length = $this->read4();
		return $this->readString($length);
	}

	private function _fail($str)
	{
		throw new Exception($str);
	}
}
