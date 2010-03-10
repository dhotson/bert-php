<?php

require_once(BASEDIR.'/classes/Bert.php');

class EncodeTest extends UnitTestCase
{
	public function testEncodeSmallInteger()
	{
		$this->assertEqual(
			Bert::encode(42),
			pack('c*', 131, 97, 42)
		);
	}

	public function testEncodeInteger()
	{
		$this->assertEqual(
			Bert::encode(1000),
			pack('c*', 131, 98, 0, 0, 3, 232)
		);
	}

	public function testEncodeFloat()
	{
		$this->assertEqual(
			Bert::encode(1.125),
			pack('c*', 131, 99)."1.125000000000000e+0"
		);
	}

	public function testEncodeAtom()
	{
		$this->assertEqual(
			Bert::encode(new Bert_Atom('test')),
			pack('c*', 131, 100, 0, 4)."test"
		);
	}

	public function testEncodeSmallTuple()
	{
		$this->assertEqual(
			Bert::encode(new Bert_Tuple(array(10,20,30))),
			pack('c*', 131, 104, 3, 97, 10, 97, 20, 97, 30)
		);
	}

	public function testEncodeLargeTuple()
	{
		$a = array_fill(0, 301, 42);

		$this->assertEqual(
			Bert::encode(new Bert_Tuple($a)),
			pack('c*', 131, 105, 0, 0, 1, 45) . str_repeat(pack('c*', 97, 42), 301)
		);
	}

	public function testEncodeList()
	{
		$this->assertEqual(
			Bert::encode(array(41,42,43)),
			pack('c*', 131, 108, 0, 0, 0, 3, 97, 41, 97, 42, 97, 43, 106)
		);
	}

	public function testEncodeEmptyList()
	{
		$this->assertEqual(
			Bert::encode(array()),
			pack('c*', 131, 106)
		);
	}

	public function testEncodeNestedList()
	{
		$this->assertEqual(
			Bert::encode(array(1, array(2))),
			pack('c*', 131, 108, 0, 0, 0, 2, 97, 1, 108, 0, 0, 0, 1, 97, 2, 106, 106)
		);
	}

	public function testEncodeBinary()
	{
		$this->assertEqual(
			Bert::encode("hello world\x00\xFF"),
			pack('c*', 131, 109, 0, 0, 0, 13)."hello world\x00\xFF"
		);
	}

	public function testEncodeAssocArray()
	{
		$this->assertEqual(
			Bert::decode(Bert::encode(array('a' => 'b'))),
			array('a' => 'b')
		);
	}

}
