<?php
namespace Gbox\base;
abstract class Assets {
	public static $css = [];
	public static $js = [];
	public static $meta = [];
	public static function register ($view)
	{
		foreach (static::$meta as $meta)
		{
			$view->addAsset('meta', $meta);
		}
		foreach (static::$css as $css)
		{
			$view->addAsset('css', $css);
		}
		foreach (static::$js as $js)
		{
			$view->addAsset('js', $js);
		}
	}
}