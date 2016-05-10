<?php
namespace Gbox\helpers;
class Url
{
	public static function to ($route = '@web', $query = [])
	{
		if (is_array($route))
		{
			$route = implode('/', $route);
		}

		$routes = explode('/', $route);
		// if (empty($routes[0]))
		// 	$routes[0] = '@web';

		if (strpos($routes[0], '@') === 0)
		{
			if ($path = \Gbox::getAlias($routes[0]))
			{
				$routes[0] = $path;
			}
		}
		$to = implode('/', $routes);

		if (count($query) != 0) $to = $to . '?' . http_build_query($query);
		return $to;
	}
	public static function goHome ()
	{
		return self::to('@web');
	}
	public static function goBack ()
	{
		return self::referer();
	}
	public static function referer ()
	{
		return $_SERVER["HTTP_REFERER"];
	}
	public static function checkExternalUrl ($url)
	{
		if (is_array($url))
		{
			$url = implode('/', $url);
		}
		return strpos($url, '//') !== false || strpos($url, 'http://') !== false || strpos($url, 'https://') !== false;
	}
}