<?php
namespace app\controllers;
use \Gbox;
use Gbox\base\Controller;
use Gbox\base\Cookie;
use Gbox\base\Session;
use app\models\Tasks;
use app\models\Notes;
use app\models\FormContact;
class SiteController extends Controller
{
	public function behaviors ()
	{
		return [
            'access' => [
                [
                    'actions' => ['index'],
                    'allow' => true,
                    // 'matchCallback' => 'checkBusinessSelected',
                ],
            ],
            'methods' => [
            	'contact' => ['post', 'get'],
            ],
        ];
	}
	public function actionIndex ()
	{
		return $this->render('index');
	}
	public function actionFeatures ()
	{
		return $this->render('features');
	}
	public function actionContact ()
	{
		$this->View->title = Gbox::$components->lang->s('page-title-contact');
		$model = new FormContact;
		$msg = NULL;
		if (Gbox::getRequest()->isPost() && $model->load(Gbox::getRequest()->post()))
		{
			if ($model->validate())
			{
				$msg = 'Su mensaje ha sido enviado correctamente.';
			}
			else
			{
				$msg = 'Hay errores en el formulario. Por favor, revise los campos y vuelva a intentarlo.';
			}
		}
		return $this->render('contact', [
			'model' => $model,
			'msg' => $msg,
        ]);
	}
}