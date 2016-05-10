<?php
namespace app\models;
use Gbox\base\Model;
class FormAccountSignIn extends Model
{
	public $username;
	public $password;
	public $rememberMe;
	public $businessSelected;
	public function rules ()
	{
		return [
			[['username', 'password'], 'required', 'message' => 'El campo "{label}" es obligatorio.'],
			[['password'], 'validatePassword'],
		];
	}
	public function attrLabels ()
	{
		return [
			'email' => 'Correo electrónico',
			'username' => 'Usuario',
			'password' => 'Contraseña',
			'password_confirm' => 'Verificar contraseña',
			'firstname' => 'Nombre',
			'lastname' => 'Apellidos',
		];
	}
	public function validatePassword ($attr)
	{
		if (!$this->hasErrors())
		{
			if (!$user = User::findByUsername($this->username))
        	{
        		$user = User::findByEmail($this->username);
        	}
			if (!$user || !$user->validatePassword($this->password))
			{
				$this->addError($attr, 'Usuario o contraseña incorrectos.');
			}
		}
	}
	public function login ()
    {
        if ($this->validate())
        {
        	if (!$user = User::findByUsername($this->username))
        	{
        		$user = User::findByEmail($this->username);
        	}
            if ($login = \Gbox::$components->user->login($user, $this->rememberMe ? 3600*24*30 : 0))
            {
            	
            }
            return $login;
        }
        return false;
    }
	public function logout ()
    {
       	\Gbox::$components->user->logout();
    }
}