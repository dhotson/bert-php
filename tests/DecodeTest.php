<?php

require_once(BASEDIR.'/classes/Bert.php');

class DecodeTest extends UnitTestCase
{
	public function testDecodeSmallInteger()
	{
		$this->assertEqual(
			Bert_Decode::decode(pack('c*', 131, 97, 42)),
			42
		);
	}

	public function testDecodeInteger()
	{
		$this->assertEqual(
			Bert_Decode::decode(pack('c*', 131, 98, 0, 0, 3, 232)),
			1000
		);
	}

	public function testDecodeFloat()
	{
		$this->assertEqual(
			Bert_Decode::decode(pack('c*', 131, 99)."1.125000000000000e+0"),
			1.125
		);
	}

	public function testDecodeAtom()
	{
		$this->assertEqual(
			Bert_Decode::decode(pack('c*', 131, 100, 0, 4)."test"),
			new Bert_Atom('test')
		);
	}

	public function testDecodeSmallTuple()
	{
		$this->assertEqual(
			Bert_Decode::decode(pack('c*', 131, 104, 3, 97, 10, 97, 20, 97, 30)),
			new Bert_Tuple(array(10,20,30))
		);
	}

	public function testDecodeLargeTuple()
	{
		$a = array_fill(0, 301, 42);
		$this->assertEqual(
			Bert_Decode::decode(pack('c*', 131, 105, 0, 0, 1, 45) . str_repeat(pack('c*', 97, 42), 301)),
			new Bert_Tuple($a)
		);
	}

	public function testDecodeList()
	{
		$this->assertEqual(
			Bert_Decode::decode(pack('c*', 131, 108, 0, 0, 0, 3, 97, 41, 97, 42, 97, 43, 106)),
			array(41,42,43)
		);
	}

	public function testDecodeEmptyList()
	{
		$this->assertEqual(
			Bert_Decode::decode(pack('c*', 131, 106)),
			array()
		);
	}

	public function testDecodeNestedList()
	{
		$this->assertEqual(
			Bert_Decode::decode(pack('c*', 131, 108, 0, 0, 0, 2, 97, 1, 108, 0, 0, 0, 1, 97, 2, 106, 106)),
			array(1, array(2))
		);
	}

	public function testDecodeBinary()
	{
		$this->assertEqual(
			Bert_Decode::decode(pack('c*', 131, 109, 0, 0, 0, 13)."hello world\x00\xFF"),
			"hello world\x00\xFF"
		);
	}
}
