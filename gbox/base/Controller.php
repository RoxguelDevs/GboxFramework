<?php
namespace Gbox\base;
use \Gbox;
use Gbox\base\View;
use Gbox\base\Request;
use Gbox\base\Response;
use Gbox\helpers\Url;
use Gbox\exceptions\ForbiddenHttpException;
use Gbox\exceptions\MethodNotAllowedHttpException;
abstract class Controller {
	protected $layout = 'site';
	protected $View;
	abstract public function actionIndex ();

	public function __construct ()
	{
		$this->View = new View($this);
		$behaviors = $this->behaviors();

		if (array_key_exists('access', $behaviors))
		{
			if (is_array($behaviors['access']))
			{
				foreach ($behaviors['access'] as $access)
				{
					if (!is_bool($access['allow']))
					{
						$access['allow'] = true;
					}
					if (in_array(strtolower(Gbox::getRequest()->getAction()), array_map('strtolower', $access['actions'])))
					{
						if (is_array($access['roles']))
						{
							foreach ($access['roles'] as $alias => $role)
							{
								if ($role === 'guest')
								{
									if ($access['allow'] !== Gbox::$components->{$alias}->isGuest)
									{
										throw new ForbiddenHttpException("No tiene privilegios para acceder a este directorio.");
									}
								}
								else if ($role === 'logged')
								{
									if ($access['allow'] !== Gbox::$components->{$alias}->isGuest)
									{
										throw new ForbiddenHttpException("No tiene privilegios para acceder a este directorio.");
									}

								}
							}
						}
						if (array_key_exists('matchCallback', $access))
						{
							if (method_exists($this, $access['matchCallback']))
							{
								if ($access['allow'] !== call_user_func([$this, $access['matchCallback']], strtolower(Gbox::getRequest()->getAction())))
								{
									throw new ForbiddenHttpException("No tiene acceso a este directorio.");
								}
							}
						}
					}
				}
			}
		}

		if (array_key_exists('methods', $behaviors))
		{
			if (is_array($behaviors['methods']))
			{
				foreach ($behaviors['methods'] as $action => $methods)
				{
					if (strtolower(Gbox::getRequest()->getAction()) == strtolower($action))
					{
						if (is_array($methods))
						{
							$allow = false;
							foreach ($methods as $method)
							{
								if (strtoupper($method) === Gbox::getRequest()->getMethod())
								{
									$allow = true;
								}
							}
							if (!$allow)
							{
								throw new MethodNotAllowedHttpException("No esta permitido el método " . Gbox::getRequest()->getMethod() . " en este directorio.");
							}
						}
					}
				}
			}
		}
	}

	public function behaviors ()
	{
		return [];
	}

	protected function render ($view, $variables = [])
	{
		$this->View->render($view, $variables);
	}

	protected function renderJson ($data)
	{
		$response = Gbox::getResponse();
		$response->format = Response::FORMAT_JSON;
		$response->data = $data;
	}

	protected function redirect ($route, $query = [], $code = 302)
	{
		return Gbox::getResponse()->redirect(Url::to($route, $query), $code);
	}

	protected function goBack ($defaultUrl = null)
	{
		if (isset($_SERVER['HTTP_REFERER']))
		{
			$url = $_SERVER['HTTP_REFERER'];
		}
		else if ($defaultUrl)
		{
			$url = $defaultUrl;
		}
		else
		{
			return $this->goHome();
		}
		return Gbox::getResponse()->redirect($url);
	}

	protected function goHome ()
    {
        return Gbox::getResponse()->redirect(Url::goHome());
    }

    protected function refresh ($anchor = '')
    {
        return Gbox::getResponse()->redirect(Gbox::getRequest()->getUrl() . $anchor);
    }

	public function getLayout ()
	{
		return $this->layout;
	}
}