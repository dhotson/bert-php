<?php

class Bert_Regex
{
	public $source;
	public $options = array();

	public function __construct($source, $options = array())
	{
		$this->source = $source;
		$this->options = $options;
	}

	public static function fromString($regex)
	{
		if (preg_match('#^([/\#])(.*)\1([imsxeADSUXJU]*)$#', $regex, $matches)
			&& count($matches) == 4)
		{
			$source = str_replace("\\".$matches[1], $matches[1], $matches[2]);
			$options = array();

			foreach (self::$_optionsmap as $o => $name)
				if (strstr($matches[3], $o))
					$options []= $name;

			return new Bert_Regex($source, $options);
		}

		throw new Exception('Invalid regex format');
	}

	public function __toString()
	{
		$opts = '';
		foreach (self::$_optionsmap as $o => $name)
			if (in_array($name, $this->options))
				$opts .= $o;

		return '/'.str_replace("/", "\\/", $this->source).'/'.$opts;
	}

	private static $_optionsmap = array(
		'i' => 'caseless',
		'm' => 'multiline',
		's' => 'dotall',
		'x' => 'extended',
		'A' => 'anchored',
		'D' => 'dollarendonly',
		'U' => 'ungreedy',
		'X' => 'extra',
		'J' => 'infojchanged',
		'u' => 'utf8',
	);
}
