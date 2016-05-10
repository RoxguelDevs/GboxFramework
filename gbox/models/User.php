<?php
namespace app\models;
use Gbox\base\Identity;
class User extends Identity
{
	public $id_user;
	public $email;
	public $username;
	public $password;
	public $firstname;
	public $lastname;
	public $auth_key;
	public $access_token;
	public $active;
	
	public static function findIdentity ($id_user)
    {
        $user = Users::find()
			->where('active', 1)
			->andWhere('id_user', $id_user)
			->one();
        return new static($user);
    }
	public static function findByUsername ($username)
    {
        $users = Users::find()
			->where('active', 1)
			->andWhere('username', $username)
			->all();
		foreach ($users as $user)
		{
			if (strcasecmp($user->username, $username) === 0)
			{
				return new static($user);
			}
		}
        return null;
    }
	public static function findByEmail ($email)
    {
        $users = Users::find()
			->where('active', 1)
			->andWhere('email', $email)
			->all();
		foreach ($users as $user)
		{
			if (strcasecmp($user->email, $email) === 0)
			{
				return new static($user);
			}
		}
        return null;
    }
    public static function findIdentityByAccessToken ($token, $type = null)
    {
        $users = Users::find()
			->where('active', 1)
			->andWhere('access_token', $token)
			->all();
		foreach ($users as $user)
		{
			if ($user->access_token === $token)
			{
				return new static($user);
			}
		}
		return null;
    }
    public static function keyId ()
	{
		return 'id_user';
	}
    public function getId ()
	{
		return $this->{$this->keyId()};
	}
	public function getAuthKey ()
	{
		return $this->auth_key;
	}
	public function validateAuthKey ($auth_key)
	{
		return $this->auth_key === $auth_key;
	}
	public function validatePassword ($password)
	{
		if (crypt($password, $this->password) == $this->password)
		{
			return true;
		}
		return false;
	}
}