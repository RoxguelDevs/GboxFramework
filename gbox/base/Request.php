<?php
namespace Gbox\base;
use \Gbox;
use Gbox\helpers\Url;
class Request
{
	private $module;
	private $controller;
	private $action;
	private $args;
	private $cookies = [];
	
	public function __construct ()
	{
		if (isset($_GET['route']))
		{
			$route = filter_input(INPUT_GET, 'route', FILTER_SANITIZE_URL);
			$route = explode('/', $route);
			$route = array_filter($route);

			/* Compruebo si se trata de una petición a un módulo configurado */
			if (count($route) > 0)
			{
				if (Gbox::getModules($route[0]))
				{
					$this->module = array_shift($route);
				}
				$this->controller = array_shift($route);
				$this->action = array_shift($route);
				$this->args = $route;
			}
		}

		if (!$this->controller)
		{
			$this->controller = 'site';
		}
		if (!$this->action)
		{
			$this->action = 'index';
		}
		if (!isset($this->args))
		{
			$this->args = [];
		}

		$this->controller = implode('', array_map('ucfirst', explode('-', $this->controller)));
		$this->action = implode('', array_map('ucfirst', explode('-', $this->action)));
		
		if (!$this->module && Gbox::getConfig()->shortUrl)
		{
			$tmp_controller_url = '@controllers/' . ucfirst($this->controller) . 'Controller' . '.php';
			Url::to($tmp_controller_url);
			if (!file_exists(Url::to($tmp_controller_url)) && strtolower($this->action) == 'index')
			{
				$this->action = $this->controller;
				$this->controller = 'site';
			}
		}
	}

	public function getCookies ($key = null, $valueDefault = null)
	{
		foreach ($_COOKIE as $name => $value)
		{
			if (property_exists(Gbox::getConfig(), 'cookieSalt'))
			{
				if ($salt = Gbox::getConfig()->cookieSalt)
				{
					$this->cookies[$name] = Gbox::simple_decrypt($value, Gbox::getConfig()->cookieSalt);
				}
				else
				{
					$this->cookies[$name] = $value;
				}
			}
		}
		if ($key)
		{
			if (array_key_exists($key, $this->cookies))
			{
				return $this->cookies[$key];
			}
			else
			{
				return $valueDefault;
			}
			return $this->cookies;
		}
		else
		{
			return $this->cookies;
		}
	}

	public function getMethod ()
	{
		return isset($_SERVER['REQUEST_METHOD']) ? strtoupper($_SERVER['REQUEST_METHOD']) : 'GET';
	}

	public function isGet ()
	{
		return $this->getMethod() === 'GET';
	}

	public function isPut ()
	{
		return $this->getMethod() === 'PUT';
	}

	public function isPost ()
	{
		return $this->getMethod() === 'POST';
	}

	public function isDelete ()
	{
		return $this->getMethod() === 'DELETE';
	}
	
	public function getModule ()
	{
		return $this->module;
	}
	
	public function getController ()
	{
		return $this->controller;
	}
	
	public function getAction ()
	{
		return $this->action;
	}
	
	public function getArgs ()
	{
		return $this->args;
	}
	
	public function getUrl ()
	{
		$url = 'http';
		if (array_key_exists('HTTPS', $_SERVER))
		{
			if ($_SERVER['HTTPS'] == 'on')
			{
				$url .= 's';
			}
		}
		$url .= '://';
		if ($_SERVER['SERVER_PORT'] != '80')
		{
			$url .= $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI'];
		}
		else
		{
			$url .= $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
		}
		return $url;
	}
	
	public static function request ($key = NULL, $defaultValue = NULL)
	{
		if ($key)
		{
			return array_key_exists($key, $_REQUEST) ? $_REQUEST[$key] : $defaultValue;
		}
		else
		{
			return $_REQUEST;
		}
	}
	
	public static function get ($key = NULL, $defaultValue = NULL)
	{
		if ($key)
		{
			return array_key_exists($key, $_GET) ? $_GET[$key] : $defaultValue;
		}
		else
		{
			return $_GET;
		}
	}
	
	public static function post ($key = NULL, $defaultValue = NULL)
	{
		if ($key)
		{
			return array_key_exists($key, $_POST) ? $_POST[$key] : $defaultValue;
		}
		else
		{
			return $_POST;
		}
	}
}