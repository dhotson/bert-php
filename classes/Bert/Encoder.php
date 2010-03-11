<?php

class Bert_Encoder
{
	/**
	 * Encode a PHP object into a BERT.
   * @param $obj is the object
   * @return string The serialized object
   */
	public static function encode($obj)
	{
		$complexObj = self::convert($obj);
		return Bert_Encode::encode($complexObj);
	}

	/**
	 * Convert complex PHP type to a simple BERT compatible PHP type.
	 * @param $obj is the object to convert
	 *
	 * @return object
	 */
	public static function convert($obj)
	{
		if (is_array($obj) && self::_isDict($obj))
		{
			$pairs = array();
			foreach ($obj as $k => $v)
			{
				$pairs []= array(
					self::convert($k),
					self::convert($v),
				);
			}

			return new Bert_Tuple(array(
				Bert_Atom::bert(),
				new Bert_Atom('dict'),
				$pairs,
			));
		}
		elseif ($obj instanceof Bert_Tuple)
		{
			return new Bert_Tuple(
				array_map(
					array('self', 'convert'),
					iterator_to_array($obj)));
		}
		elseif ($obj instanceof Bert_Time)
		{
			return new Bert_Tuple(array(
				Bert_Atom::bert(),
				new Bert_Atom('time'),
				$obj->megaseconds,
				$obj->seconds,
				$obj->microseconds,
			));
		}
		elseif ($obj instanceof Bert_Regex)
		{
			return new Bert_Tuple(array(
				Bert_Atom::bert(),
				new Bert_Atom('regex'),
				$obj->source,
				array_map(array('Bert','a'), $obj->options) // atom-ise options
			));
		}
		elseif (is_array($obj))
		{
			return array_map(array('self', 'convert'), $obj);
		}
		elseif ($obj === null)
		{
			return new Bert_Tuple(array(
				Bert_Atom::bert(),
				Bert_Atom::nil(),
			));
		}
		elseif ($obj === true)
		{
			return new Bert_Tuple(array(
				Bert_Atom::bert(),
				Bert_Atom::true(),
			));
		}
		elseif ($obj === false)
		{
			return new Bert_Tuple(array(
				Bert_Atom::bert(),
				Bert_Atom::false(),
			));
		}
		else
		{
			return $obj;
		}
	}

	// Check if array is associative or not.. stolen from a comment on php.net
	private static function _isDict($array)
	{
		return (is_array($array) && 0 !== count(array_diff_key($array, array_keys(array_keys($array)))));
	}
}
