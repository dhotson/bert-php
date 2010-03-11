<?php

require_once(BASEDIR.'/classes/Bert.php');

class DecoderTest extends UnitTestCase
{
	public function testDecodeBool()
	{
		$this->assertEqual(Bert_Decoder::decode(Bert::encode(true)), true);
		$this->assertEqual(Bert_Decoder::decode(Bert::encode(false)), false);
		$this->assertEqual(Bert_Decoder::decode(Bert::encode(null)), null);
	}

	public function testDecodeRegex()
	{
		$bert = Bert::encode(new Bert_Regex('hello', array('caseless')));

		$this->assertEqual(
			Bert_Decoder::decode($bert),
			new Bert_Regex('hello', array('caseless'))
		);
	}

	public function testDecodeTime()
	{
		$bert = Bert::encode(new Bert_Time(100, 200, 300));

		$this->assertEqual(
			Bert_Decoder::decode($bert),
			new Bert_Time(100, 200, 300)
		);
	}

	public function testDecodeDict()
	{
		$bert = Bert::encode(array('a' => 'b'));

		$this->assertEqual(
			Bert_Decoder::decode($bert),
			array('a' => 'b')
		);
	}

}
