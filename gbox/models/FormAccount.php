<?php
namespace app\models;
use \Gbox;
use Gbox\base\Model;
class FormAccount extends Model
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
			[['email', 'firstname', 'lastname'], 'required', 'message' => 'El campo "{label}" es obligatorio.'],
			// [['firstname', 'lastname'], 'match', 'pattern' => '/^.{3,30}$/', 'message' => 'El campo "{label}" debe tener entre 3 y 30 caracteres.'],
			[['email'], 'email', 'message' => 'El correo electrónico no es válido.'],
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
	public function checkValidPassword ($attr)
	{
		$password = $this->{$attr};
		if (!empty($password))
		{
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
}