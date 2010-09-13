<?php

class Ernie
{
	private static $_mods = array();
	private static $_currentMod = null;
	private static $_autoStart = true;

	public static function mod($name, $callbacks = array())
	{
		$m = new Ernie_Module($name);
		self::$_currentMod = $m;
		self::$_mods[$name] = $m;

		foreach ($callbacks as $n => $c)
			$m->fun($n, $c);
	}

	public static function fun($name, $callback)
	{
		self::$_currentMod->fun($name, $callback);
	}

	/**
	 * Expose public methods of a class or object
	 * @param $name string The module name to use
	 * @param $obj A classname as a string or and object
	 */
	public static function expose($name, $obj)
	{
		if (is_string($obj))
			$class = $obj;
		else
			$class = get_class($obj);

		$callbacks = array();
		$rc = new ReflectionClass($class);
		foreach ($rc->getMethods(ReflectionMethod::IS_PUBLIC) as $method)
		{
			$callbacks[$method->name] = array($obj, $method->name);
		}

		self::mod($name, $callbacks);
	}

	public static function dispatch($mod, $fun, $args)
	{
		if (null === ($mod = self::$_mods["$mod"]))
			throw new Ernie_ServerError("No such module '$mod'");

		if (null === ($callback = $mod->getFun($fun)))
			throw new Ernie_ServerError("No such function '$mod:$fun'");

		$callback = $mod->getFun($fun);

		return call_user_func_array($callback, $args);
	}

	public static function read4($input)
	{
		if (false === ($raw = fread($input, 4)))
			return null;

		return array_shift(unpack('N', $raw));
	}

	public static function readBerp($input)
	{
		if (null === ($packetSize = self::read4($input)))
			return null;

		$bert = fread($input, $packetSize);

		return Bert::decode($bert);
	}

	public static function writeBerp($output, $obj)
	{
		$bert = Bert::encode($obj);
		fwrite($output, pack('N', strlen($bert)));
		fwrite($output, $bert);
	}

	public static function start()
	{
		$input = fdopen(3, 'r');
		$output = fdopen(4, 'w');

		while (true)
		{
			$obj = self::readBerp($input);

			if (!isset($obj))
			{
				echo "Could not read BERP length header. Ernie server may have gone away. Exiting now.\n";
				exit(1);
			}

			if (count($obj) == 4 && $obj[0] == 'call')
			{
				$mod = $obj[1];
				$fun = $obj[2];
				$args = $obj[3];

				try
				{
					$result = self::dispatch($mod, $fun, $args);
					$response = Bert::t(Bert::a('reply'), $result);
					self::writeBerp($output, $response);
				}
				catch (Ernie_ServerError $e)
				{
					$response = Bert::t(
						Bert::a('error'),
						Bert::t(
							Bert::a('server'),
							0,
							get_class($e),
							$e->getMessage(),
							$e->getTrace()
						)
					);
					self::writeBerp($output, $response);
				}
				catch (Exception $e)
				{
					$response = Bert::t(
						Bert::a('error'),
						Bert::t(
							Bert::a('user'),
							0,
							get_class($e),
							$e->getMessage(),
							$e->getTrace()
						)
					);
					self::writeBerp($output, $response);
				}
			}
			elseif (count($obj) == 4 && $obj[0] == 'cast')
			{
				$mod = $obj[1];
				$fun = $obj[2];
				$args = $obj[3];

				try
				{
					$result = self::dispatch($mod, $fun, $args);
				}
				catch (Exception $e)
				{
					// ignore
				}

				self::writeBerp($output, Bert::t(Bert::a('noreply')));
			}
			else
			{
				$outObj = Bert::t(
					Bert::a('error'),
					array(
						Bert::a('server'),
						0,
						"Invalid request: ".print_r($obj, true)
					)
				);

				self::writeBerp($output, $outObj);
			}

		}
	}
}


class Ernie_ServerError extends Exception { }

class Ernie_Module
{
	private $_name;
	private $_funs = array();

	public function __construct($name)
	{
		$this->_name = $name;
	}

	public function fun($name, $callback)
	{
		$this->_funs[$name] = $callback;
	}

	public function getFun($name)
	{
		return isset($this->_funs["$name"])
			? $this->_funs["$name"]
			: null;
	}

	public function __toString()
	{
		return "$this->_name";
	}
}

// TODO dhotson .. implement logging later
// class Logger
// {
// 	const FATAL = 1;
// 	const ERROR = 2;
// 	const WARN = 4;
// 	const INFO = 8;
// 	const DEBUG = 16;
// }
