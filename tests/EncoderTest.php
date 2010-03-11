<?php

require_once(BASEDIR.'/classes/Bert.php');

class EncoderTest extends UnitTestCase
{
	public function testConvertBool()
	{
		$this->assertEqual(
			Bert_Encoder::convert(true),
			Bert::t(Bert::a('bert'), Bert::a('true'))
		);

		$this->assertEqual(
			Bert_Encoder::convert(false),
			Bert::t(Bert::a('bert'), Bert::a('false'))
		);

		$this->assertEqual(
			Bert_Encoder::convert(null),
			Bert::t(Bert::a('bert'), Bert::a('nil'))
		);
	}

	public function testConvertDict()
	{
		$this->assertEqual(
			Bert_Encoder::convert(array('a' => 'b')),
			Bert::t(Bert::a('bert'), Bert::a('dict'), array(array('a', 'b')))
		);
	}

	public function testConvertTime()
	{
		$this->assertEqual(
			Bert_Encoder::convert(new Bert_Time(100, 200, 300)),
			Bert::t(Bert::a('bert'), Bert::a('time'), 100, 200, 300)
		);
	}

	public function testConvertRegex()
	{
		$this->assertEqual(
			Bert_Encoder::convert(new Bert_Regex('.*?', array('caseless'))),
			Bert::t(Bert::a('bert'), Bert::a('regex'), '.*?', array(Bert::a('caseless')))
		);
	}

	public function testConvertRecursive()
	{
		$this->assertEqual(
			Bert_Encoder::convert(Bert::t(Bert::t(true), false)),
			Bert::t(
				Bert::t(
					Bert::t(Bert::a('bert'), Bert::a('true'))
				),
				Bert::t(Bert::a('bert'), Bert::a('false'))
			)
		);
	}


}
