<?php
namespace Gbox\collections;
use Gbox\base\Cookie;
class Cookies
{
	private $cookies = [];
	public function get ($name, $default = null)
	{
		return isset($this->cookies[$name]) ? $this->cookies[$name] : null;
	}
	public function getValue ($name, $defaultValue = null)
	{
		return isset($this->cookies[$name]) ? $this->cookies[$name]->value : $defaultValue;
	}
	public function set ($cookie)
	{
		$this->cookies[$cookie->name] = $cookie;
	}
	public function has ($name)
	{
		return isset($this->cookies[$name]) && $this->cookies[$name]->value !== ''
            		&& ($this->cookies[$name]->expire === null || $this->cookies[$name]->expire >= time());
	}
	public function remove ($cookie, $removeFromBrowser = true)
	{
		if ($cookie instanceof Cookie)
		{
			$cookie->expire = 1;
			$cookie->value = '';
		}
		else
		{
			$cookie = new Cookie([
				'name' => $cookie,
				'expire' => 1,
			]);
		}
		if ($removeFromBrowser)
		{
			$this->cookies[$cookie->name] = $cookie;
		}
		else
		{
			unset($this->cookies[$cookie->name]);
		}
	}
	public function removeAll ()
	{
		$this->cookies = [];
	}
	public function toArray ()
	{
		return $this->cookies;
	}
	public function fromArray (array $array)
	{
		$this->cookies = $array;
	}
}