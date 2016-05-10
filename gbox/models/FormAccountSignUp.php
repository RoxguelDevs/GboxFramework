<?php
namespace app\models;
use \Gbox;
use Gbox\base\Model;
class FormAccountSignUp extends Model
{
	public $email;
	public $username;
	public $password;
	public $password_confirm;
	public $firstname;
	public $lastname;
	public function rules ()
	{
		return [
			// [['email', 'username', 'password', 'password_confirm', 'firstname', 'lastname'], 'required', 'message' => 'El campo "{label}" es obligatorio.'],
			[['email', 'password', 'password_confirm'], 'required', 'message' => 'El campo "{label}" es obligatorio.'],
			// [['firstname', 'lastname'], 'match', 'pattern' => '/^.{3,30}$/', 'message' => 'El campo "{label}" debe tener entre 3 y 30 caracteres.'],
			[['email'], 'email', 'message' => 'El correo electrónico no es válido.'],
			[['username'], 'match', 'pattern' => '/^[a-zA-Z0-9\.]{5,30}+$/', 'message' => 'El nombre de usuario debe contener entre 5 y 30 caracteres alfanuméricos, puede contener puntos (.).'],
			[['username'], 'checkUsernameQuickSignUp'],
			[['username'], 'checkUsernameExists'],
			[['email'], 'checkEmailExists'],
			[['password'], 'checkValidPassword'],
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
	public function checkUsernameExists ($attr)
	{
		$user = Users::find()
			->where('username', $this->{$attr})
			->one();
		if ($user)
		{
			$this->addError($attr, 'El nombre de usuario se encuentra en uso. Intente con otro.');
		}
	}
	public function checkUsernameQuickSignUp ($attr)
	{
		if (Gbox::getRequest()->post('quick_sign_up') !== '1' && empty($this->{$attr}))
		{
			$this->addError($attr, 'El nombre de usuario es obligatorio.');
		}
	}
	public function checkEmailExists ($attr)
	{
		$user = Users::find()
			->where('email', $this->{$attr})
			->one();
		if ($user)
		{
			$this->addError($attr, 'El correo ya se encuentra registrado. Intente iniciar sesión o utilice otro.');
		}
	}
	public function checkValidPassword ($attr)
	{
		$password = $this->{$attr};
		if (empty($password))
		{
			$this->addError($attr, 'Debe ingresar una contraseña');
		}
		if (strlen($password) < 8)
		{
			$this->addError($attr, 'La contraseña debe tener por lo menos 8 caracteres.');
		}
		if (strlen($password) > 30)
		{
			$this->addError($attr, 'La contraseña debe tener menos de 30 caracteres.');
		}
		if (!preg_match('/[a-z]+/', $password))
		{
			$this->addError($attr, 'La contraseña debe al menos una letra minúscula.');
		}
		if (!preg_match('/[A-Z]+/', $password))
		{
			$this->addError($attr, 'La contraseña debe al menos una letra mayúscula.');
		}
		if (!preg_match('/[0-9]+/', $password))
		{
			$this->addError($attr, 'La contraseña debe al menos un número.');
		}
	}
}