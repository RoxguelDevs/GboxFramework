<?php
namespace Gbox\helpers;
class Json
{
	public static function encode ($value, $options = 320)
	{
		$json = json_encode($value, $options);
		return $json;
	}
	public static function decode ($json, $asArray = true)
	{
		if (is_array($json))
		{
			throw new Exception('Invalid JSON data.');
		}
		$decode = json_decode((string) $json, $asArray);
		return $decode;
	}
}