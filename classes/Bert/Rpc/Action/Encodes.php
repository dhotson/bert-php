<?php

class Bert_Rpc_Action_Encodes
{
	public static function encodeRequest($request)
	{
		return Bert::encode($request);
	}

	public static function decodeResponse($bert)
	{
		$response = Bert::decode($bert);

		if ($response[0] == 'reply')
			return $response[1];
		elseif ($response[0] == 'noreply')
			return null;
		elseif ($response[0] == 'error')
			self::_error($response[1]);
		else
			throw new Exception('Unknown response type');
	}

	// ---

	private static function _error($err)
	{
		list($level, $code, $class, $message, $backtrace) = $err;

		switch ($level)
		{
			case 'protocol':
				throw new Bert_Rpc_Action_ProtocolError(
					$code,
					$message,
					$class,
					$backtrace
				);
			case 'server':
				throw new Bert_Rpc_Action_ServerError(
					$code,
					$message,
					$class,
					$backtrace
				);
			case 'user':
				throw new Bert_Rpc_Action_UserError(
					$code,
					$message,
					$class,
					$backtrace
				);
			case 'proxy':
				throw new Bert_Rpc_Action_ProxyError(
					$code,
					$message,
					$class,
					$backtrace
				);
			default:
				throw new Exception();
		}
	}
}
