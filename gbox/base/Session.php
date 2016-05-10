<?php
namespace Gbox\base;
class Session
{
	public static function init ()
	{
		session_start();
	}
	public static function destroy ($key = false)
	{
		if (is_string($key))
		{
			unset($_SESSION[$key]);
		}
		else if (is_array($key))
		{
			foreach ($key as $index)
			{
				unset($_SESSION[$index]);
			}
		}
		else
		{
			session_destroy();
		}
	}
	public static function set ($key, $value)
	{
		$_SESSION[$key] = $value;
	}
	public static function get ($key, $default = null)
	{
		if (!isset($_SESSION[$key]))
		{
			self::set($key, $default);
		}
		return $_SESSION[$key];
	}
}