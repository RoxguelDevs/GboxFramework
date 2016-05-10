<?php
namespace Gbox\collections;
class Headers
{
	private $headers = [];
	public function get($name, $default = null)
	{
		$name = strtolower($name);
		if (isset($this->headers[$name]))
		{
			return $this->headers[$name];
		}
		else
		{
			return $default;
		}
	}
	public function set($name, $value = '')
	{
		$name = strtolower($name);
		$this->headers[$name] = (array) $value;
		return $this;
	}
	public function add($name, $value)
	{
		$name = strtolower($name);
		$this->_headers[$name][] = $value;
		return $this;
	}
	public function has($name)
	{
		$name = strtolower($name);
		return isset($this->headers[$name]);
	}
	public function remove($name)
	{
		$name = strtolower($name);
		if (isset($this->headers[$name]))
		{
			$value = $this->headers[$name];
			unset($this->headers[$name]);
			return $value;
		}
		else
		{
			return null;
		}
	}
	public function removeAll()
	{
		$this->headers = [];
	}
	public function toArray()
	{
		return $this->headers;
	}
	public function fromArray(array $array)
	{
		$this->headers = $array;
	}
}