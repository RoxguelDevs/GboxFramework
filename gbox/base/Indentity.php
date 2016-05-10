<?php
namespace Gbox\base;
use Gbox;
abstract class Identity extends Component implements IndentityInterface
{
	public $isGuest = true;
	public $id = null;
	private $_identity;
	protected $params = [
		'autoLogin' => true,
	];
	public function __construct ($user = null)
	{
		if ($user)
		{
			$data = get_object_vars($user);
			foreach ($data as $key => $value)
			{
				if (property_exists($this, $key))
				{
					$this->{$key} = $value;
				}
			}
		}
	}
	public function init ()
	{
		$tmp_id = null;
		$tmp_auth_key = null;
		$session_id = Session::get('__' . $this->alias());
		$session_auth_key = Session::get('__' . $this->alias() . '_auth_key');
		if ($session_id !== null && $session_auth_key !== null)
		{
			$tmp_id = $session_id;
			$tmp_auth_key = $session_auth_key;
		}
		else if (array_key_exists('autoLogin', $this->params))
		{
			if ($this->params['autoLogin'] === true)
			{
				$cookie_id = Gbox::getRequest()->getCookies('__' . $this->alias());
				$cookie_auth_key = Gbox::getRequest()->getCookies('__' . $this->alias() . '_auth_key');
				if ($cookie_id !== null && $cookie_auth_key !== null)
				{
					$tmp_id = $cookie_id;
					$tmp_auth_key = $cookie_auth_key;
				}
			}
		}
		if ($tmp_id !== null && $tmp_auth_key !== null)
		{
			$tmp_identity = static::findIdentity($tmp_id);
			if ($tmp_identity->validateAuthKey($tmp_auth_key))
			{
				$this->login($tmp_id);
			}
		}
	}
	public function alias ()
	{
		return 'user';
	}
	public function login ($user = null, $remember = 0)
	{
		if ($user === null)
		{
			$this->_identity = null;
			return false;
		}
		else
		{
			if (is_numeric($user))
			{
				$this->id = $user;
				$data = get_object_vars($this->findIdentity($this->id));
			}
			else
			{
				$data = get_object_vars($user);
				$this->id = $user->{$this->keyId()};
			}

			foreach ($data as $key => $value)
			{
				if ($key === 'id')
				{
					continue;
				}
				if (property_exists($this, $key))
				{
					$this->{$key} = $value;
				}
			}

			$data = Gbox::arrayToObject($data);
			$this->isGuest = false;

			if ($remember > 1)
			{
				$cookie_id = new Cookie;
				$cookie_id->name = '__' . $this->alias();
				$cookie_id->value = $this->id;
				$cookie_id->expire = time() + $remember;

				$cookie_auth_key = new Cookie;
				$cookie_auth_key->name = '__' . $this->alias() . '_auth_key';
				$cookie_auth_key->value = $this->auth_key;
				$cookie_auth_key->expire = time() + $remember;

				Gbox::getResponse()->getCookies()->set($cookie_id);
				Gbox::getResponse()->getCookies()->set($cookie_auth_key);
			}
			Session::set('__' . $this->alias(), $this->id);
			Session::set('__' . $this->alias() . '_auth_key', $this->auth_key);
			return true;
		}
	}
	public function logout ()
	{
		$this->_identity = null;
		$this->isGuest = false;
		$this->id = null;
		Session::destroy('__' . $this->alias());
		Session::destroy('__' . $this->alias() . '_auth_key');
		Gbox::getResponse()->getCookies()->remove('__' . $this->alias());
		Gbox::getResponse()->getCookies()->remove('__' . $this->alias() . '_auth_key');
		return true;
	}
}