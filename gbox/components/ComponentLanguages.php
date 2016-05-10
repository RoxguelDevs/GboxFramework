<?php
namespace app\components;
use \Gbox;
use Gbox\base\Request;
use Gbox\base\Component;
use Gbox\helpers\Url;
use Gbox\helpers\Json;
use Gbox\base\Cookie;
class ComponentLanguages extends Component
{
	const LANG_DEFAULT = 'es';
	private $langs = [];
	private $lang;
	private $strings = [];
	private $strings_default = [];
	public function init ()
	{
		$this->langs = $this->params['langs'];
		$lang = Request::get('lang');
		$lang_cookie = Gbox::getRequest()->getCookies('lang');
		if ($lang && in_array($lang, $this->langs))
		{
			$this->setLang($lang);
		}
		else if (isset($lang_cookie) && in_array($lang_cookie, $this->langs))
		{
			$this->setLang($lang);
		}
		else
		{
			$this->setLang(self::LANG_DEFAULT);
		}

		$name_file = 'strings-' . $this->lang . '.json';
		$url = Url::to(['@components', 'ConfigLanguages', $name_file]);
		$json = file_get_contents($url);
		$this->strings = Json::decode($json, true);

		if (self::LANG_DEFAULT == $this->lang)
		{
			$this->strings_default = Json::decode($json, true);
		}
		else
		{
			$name_file = 'strings-' . self::LANG_DEFAULT . '.json';
			$url = Url::to(['@components', 'ConfigLanguages', $name_file]);
			$json = file_get_contents($url);
			$this->strings_default = Json::decode($json, true);
		}
	}
	public function getLang ()
	{
		return $this->lang;
	}
	public function setLang ($lang)
	{
		$cookie = new Cookie;
		$cookie->name = '__lang';
		$cookie->value = $lang;
		$cookie->domain = $_SERVER['SERVER_NAME'];
		$cookie->expire = time() + (3600 * 24 * 30);
		Gbox::getResponse()->getCookies()->set($cookie);
		$this->lang = $lang;
	}
	public function s ($key)
	{
		if (array_key_exists($key, $this->strings))
		{
			return $this->strings[$key];
		}
		else if (array_key_exists($key, $this->strings_default))
		{
			return $this->strings_default[$key];
		}
		else
		{
			return '';
		}
	}
}