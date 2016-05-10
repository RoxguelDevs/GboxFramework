<?php
namespace Gbox\base;
use \Gbox;
use Gbox\base\Cookie;
use Gbox\helpers\Url;
use Gbox\collections\Cookies;
use Gbox\collections\Headers;
class Response
{
	const FORMAT_HTML = 'html';
	const FORMAT_JSON = 'json';
	const DEFAULT_CHARSET = 'utf-8';
	public $format = self::FORMAT_HTML;
	public $charset;
	public $version;
	public $isSent = false;
	public $formatters = [];
	public $data;
	public $content;
	public $statusText = 'OK';
	public static $httpStatuses = [
		100 => 'Continue',
		101 => 'Switching Protocols',
		102 => 'Processing',
		118 => 'Connection timed out',
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',
		207 => 'Multi-Status',
		208 => 'Already Reported',
		210 => 'Content Different',
		226 => 'IM Used',
		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Found',
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'Use Proxy',
		306 => 'Reserved',
		307 => 'Temporary Redirect',
		308 => 'Permanent Redirect',
		310 => 'Too many Redirect',
		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Time-out',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Request Entity Too Large',
		414 => 'Request-URI Too Long',
		415 => 'Unsupported Media Type',
		416 => 'Requested range unsatisfiable',
		417 => 'Expectation failed',
		418 => 'I\'m a teapot',
		422 => 'Unprocessable entity',
		423 => 'Locked',
		424 => 'Method failure',
		425 => 'Unordered Collection',
		426 => 'Upgrade Required',
		428 => 'Precondition Required',
		429 => 'Too Many Requests',
		431 => 'Request Header Fields Too Large',
		449 => 'Retry With',
		450 => 'Blocked by Windows Parental Controls',
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway or Proxy Error',
		503 => 'Service Unavailable',
		504 => 'Gateway Time-out',
		505 => 'HTTP Version not supported',
		507 => 'Insufficient storage',
		508 => 'Loop Detected',
		509 => 'Bandwidth Limit Exceeded',
		510 => 'Not Extended',
		511 => 'Network Authentication Required',
	];
	private $headers;
	private $cookies;
	private $statusCode = 200;
	public function init ()
	{
		if ($this->version === null)
		{
			if (isset($_SERVER['SERVER_PROTOCOL']) && $_SERVER['SERVER_PROTOCOL'] === 'HTTP/1.0')
			{
				$this->version = '1.0';
			}
			else
			{
				$this->version = '1.1';
			}
		}
		if ($this->charset === null)
		{
			$this->charset = self::DEFAULT_CHARSET;
		}
		$this->formatters = array_merge($this->defaultFormatters(), $this->formatters);
	}
	public function getHeaders ()
	{
		if ($this->headers === null)
		{
			$this->headers = new Headers;
		}
		return $this->headers;
	}
	public function getCookies ()
	{
		if ($this->cookies === null)
		{
			$this->cookies = new Cookies;
		}
		return $this->cookies;
	}
	public function getStatusCode ()
	{
		return $this->statusCode;
	}
	public function getIsInvalid ()
	{
		return $this->getStatusCode() < 100 || $this->getStatusCode() >= 600;
	}
	public function setStatusCode ($value, $text = null)
	{
		if ($value === null)
		{
			$value = 200;
		}
		$this->statusCode = (int) $value;
		if ($this->getIsInvalid())
		{
			throw new Exception("The HTTP status code is invalid: $value");
		}
		if ($text === null)
		{
			$this->statusText = isset(static::$httpStatuses[$this->statusCode]) ? static::$httpStatuses[$this->statusCode] : '';
		}
		else
		{
			$this->statusText = $text;
		}
	}
	public function send ()
	{
		if ($this->isSent)
		{
			return;
		}
		$this->prepare();
		$this->sendHeaders();
		$this->sendContent();
		$this->isSent = true;
	}
	protected function sendContent ()
	{
		echo $this->content;
	}
	protected function sendHeaders ()
	{
		if (headers_sent($a, $b))
		{
			return;
		}
		$statusCode = $this->getStatusCode();
		header("HTTP/{$this->version} $statusCode {$this->statusText}");
		if ($this->headers)
		{
			$headers = $this->getHeaders()->toArray();
			foreach ($headers as $name => $values)
			{
				$name = str_replace(' ', '-', ucwords(str_replace('-', ' ', $name)));
				$replace = true;
				foreach ($values as $value)
				{
					header("$name: $value", $replace);
					$replace = false;
				}
			}
		}
		$this->sendCookies();
	}
	protected function sendCookies ()
	{
		if ($this->cookies === null)
		{
			return;
		}
		if (property_exists(Gbox::getConfig(), 'cookieSalt'))
		{
			if ($salt = Gbox::getConfig()->cookieSalt)
			{
				foreach ($this->getCookies()->toArray() as $cookie)
				{
					$value = Gbox::simple_encrypt($cookie->value, $salt);
					setcookie($cookie->name, $value, $cookie->expire, $cookie->path, $cookie->domain);
				}
			}
		}
		else
		{
			foreach ($this->getCookies()->toArray() as $cookie)
			{
				setcookie($cookie->name, $cookie->value, $cookie->expire, $cookie->path, $cookie->domain);
			}
		}
	}
	protected function defaultFormatters ()
	{
		return [
			self::FORMAT_HTML => 'Gbox\formatters\HtmlFormatter',
			self::FORMAT_JSON => 'Gbox\formatters\JsonFormatter',
		];
	}
	protected function prepare ()
	{
		if (isset($this->formatters[$this->format]))
		{
			$formatter = new $this->formatters[$this->format];
			$formatter->format($this);
		}
		else
		{
			throw new Exception("Unsupported response format: {$this->format}");
		}
	}
	public function redirect ($url, $code = 302)
	{
		Gbox::getResponse()->setStatusCode($code);
		Gbox::getResponse()->getHeaders()->set('location', Url::to($url));
	}
}