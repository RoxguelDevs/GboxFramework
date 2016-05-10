<?php
namespace Gbox\base;
class Cookie
{
	public $name;
	public $value = '';
	public $domain = '';
	public $expire = 0;
	public $path = '/';
	public function __construct ($params = [])
	{
		if (array_key_exists('name', $params))
		{
			$this->name = $params['name'];
		}
		if (array_key_exists('value', $params))
		{
			$this->value = $params['value'];
		}
		if (array_key_exists('domain', $params))
		{
			$this->domain = $params['domain'];
		}
		if (array_key_exists('expire', $params))
		{
			$this->expire = $params['expire'];
		}
		if (array_key_exists('path', $params))
		{
			$this->path = $params['path'];
		}
	}
}