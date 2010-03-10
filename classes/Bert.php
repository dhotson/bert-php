<?php

Bert::registerAutoload();

class Bert
{
	public static function encode($obj)
	{
		return Bert_Encoder::encode($obj);
	}

	public static function decode($bert)
	{
		return Bert_Decoder::decode($bert);
	}

	public static function ebin($str)
	{
		$bytes = unpack('C*', $str);
		return '<<' . implode(',', $bytes) . '>>';
	}

	public static function a($str)
	{
		return new Bert_Atom($str);
	}

	public static function t()
	{
		return new Bert_Tuple(func_get_args());
	}


	// --

	public static function autoload($class)
	{
		if (0 !== strpos($class, 'Bert'))
		{
			return false;
		}

		$path = dirname(__FILE__).'/'.str_replace('_', '/', $class).'.php';

		if (!file_exists($path))
		{
			return false;
		}

		require_once $path;
	}

	public static function registerAutoload()
	{
		spl_autoload_register(array('Bert', 'autoload'));
	}
}

