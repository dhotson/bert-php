<?php

require_once(BASEDIR.'/classes/Bert.php');

class RegexTest extends UnitTestCase
{
	public function testBasic()
	{
		$this->assertEqual(
			Bert_Regex::fromString('/hello.*/im'),
			new Bert_Regex('hello.*', array('caseless', 'multiline'))
		);
	}

	public function testEscapeSource()
	{
		$r = Bert_Regex::fromString('/hel\/lo/');

		$this->assertEqual(
			$r->source,
			'hel/lo'
		);

		$this->assertEqual(
			"$r",
			'/hel\/lo/'
		);
	}

	public function testDodgyRegex()
	{
		$this->expectException('Exception');
		Bert_Regex::fromString('missing slashes');
	}

	public function testDodgyOptions()
	{
		$this->expectException('Exception');
		Bert_Regex::fromString('/hello/xyz');
	}
}
