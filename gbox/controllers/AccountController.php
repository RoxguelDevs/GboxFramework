<?php
namespace app\controllers;
use \Gbox;
use Gbox\base\Session;
use Gbox\base\Response;
use Gbox\base\Controller;
use app\models\Users;
use app\models\FormAccount;
use app\models\FormAccountSignUp;
use app\models\FormAccountSignIn;
class AccountController extends Controller
{
	public function behaviors ()
	{
		return [
            'access' => [
                [
                    'actions' => ['sign-up', 'sign-in'],
                    'allow' => true,
                    'roles' => ['user' => 'guest'],
                ],
                [
                    'actions' => ['index', 'logout'],
                    'allow' => false,
                    'roles' => ['user' => 'guest'],
                ],
            ],
        ];
	}
	public function actionIndex ()
	{
		$this->View->title = 'Mi cuenta';
		$modelAccount = new FormAccount;
		$contact = Users::find()->one(Gbox::$components->user->id);
		$modelAccount->firstname = $contact->firstname;
		$modelAccount->lastname = $contact->lastname;
		$modelAccount->email = $contact->email;
		$modelAccount->username = $contact->username;
		if (Gbox::getRequest()->isPost() && $modelAccount->load(Gbox::getRequest()->post()))
		{
			if (!empty($modelAccount->password) && $modelAccount->password != $modelAccount->password_confirm)
			{
				$modelAccount->addError('password_confirm', 'La contraseña debe coincidir con la confirmación.');
			}
			if ($modelAccount->validate())
			{
				$table = new Users;
				$table->firstname = $modelAccount->firstname;
				$table->lastname = $modelAccount->lastname;
				$table->email = $modelAccount->email;

				if (!empty($modelAccount->password) && $modelAccount->password == $modelAccount->password_confirm)
				{
					$table->password = crypt($modelAccount->password, '$2y$10$' . Gbox::getConfig()->params['salt']);
				}

				if ($table->update(Gbox::$components->user->id))
                {
					$msg = 'Se ha editado su cuenta con éxito.';
					Session::set('response', ['msg' => $msg, 'type' => 'success']);
                }
                else
                {
                    Session::set('response', ['msg' => 'Ha ocurrido un error al editar su cuenta.', 'type' => 'danger']);
                }
			}
			else
			{
				Session::set('response', ['msg' => 'Ocurrió un error, revise los campos y vuelva a intentarlo.', 'type' => 'warning']);
			}
		}
		return $this->render('index', ['modelAccount' => $modelAccount]);
	}
	public function actionLogin ()
	{
		$modelSignIn = new FormAccountSignIn;
		$modelSignUp = new FormAccountSignUp;
		return $this->render('login', ['modelSignIn' => $modelSignIn, 'modelSignUp' => $modelSignUp]);
	}
	public function actionSignIn ()
	{
		if (!Gbox::$components->user->isGuest)
		{
			return $this->redirect('@web');
		}
		$model = new FormAccountSignIn;
		$msg = NULL;
		$this->View->title = 'Iniciar sesión';
		if (Gbox::getRequest()->isPost() && $model->load(Gbox::getRequest()->post()) && $model->login())
		{
			if ($model->validate())
			{
				return $this->redirect(['@web'], ['action' => 'logged']);
			}
			else
			{
				$msg = 'Error al iniciar sesión.';
			}
		}
		return $this->render('sign-in', [
			'model' => $model,
			'msg' => $msg,
        ]);
	}
	public function actionSignOut ()
	{
		if (Gbox::$components->user->isGuest)
		{
			return $this->redirect('@web');
		}
		$model = new FormAccountSignIn;
		$model->logout();
		Session::destroy('_business_id');
		return $this->redirect('@web', ['action' => 'signout']);
	}
	public function actionSignUp ()
	{
		$model = new FormAccountSignUp;
		$msg = NULL;
		$this->View->title = 'Registro de usuairo';
		if (Gbox::getRequest()->isPost() && $model->load(Gbox::getRequest()->post()))
		{
			if ($model->password != $model->password_confirm)
			{
				$model->addError('password_confirm', 'Las contraseñas no coinciden.');
			}
			if ($model->validate())
			{
				$table = new Users;
				$table->username = $model->username;
				$table->email = $model->email;
				$table->password = crypt($model->password, '$2y$10$' . Gbox::getConfig()->params['salt']);
				$table->firstname = $model->firstname;
				$table->lastname = $model->lastname;
				$table->auth_key = $this->randKey('abcdefghijklmnopqrstuvxyz0123456789', 128);
				$table->access_token = $this->randKey('abcdefghijklmnopqrstuvxyz0123456789', 128);
				$table->active = 1;

				if ($id_user = $table->insert())
                {
                    $msg = 'Se ha creado el usuario ' . $model->firstname . ' ' . $model->lastname . ' (' . $id_user . ').';
					$model->username = null;
					$model->email = null;
					$model->password = null;
					$model->firstname = null;
					$model->lastname = null;
                }
                else
                {
                    $msg = 'Ha ocurrido un error al guardar al nuevo usuario.';
                }
			}
			else
			{
				$msg = 'Ocurrió un error, revise los campos y vuelva a intentarlo.';
			}
		}
		return $this->render('sign-up', [
			'model' => $model,
			'msg' => $msg,
        ]);
	}
    private function randKey ($str = '', $long = 32)
    {
        $key = '';
        $str = str_split($str);
        $start = 0;
        $limit = count($str)-1;
        for($i = 0; $i < $long; $i++)
        {
            $key .= $str[rand($start, $limit)];
        }
        return $key;
    }
}