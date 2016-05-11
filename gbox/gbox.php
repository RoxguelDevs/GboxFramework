<?php

/**
 *
 * TODO:
 * Implement Bootstrap.
 *
 */


use Gbox\base\Request;
use Gbox\base\Response;
use Gbox\base\Session;
use Gbox\helpers\Url;
use Gbox\exceptions\Exception;
use Gbox\exceptions\HttpException;
use Gbox\exceptions\NotFoundHttpException;
class Gbox
{
	const GBOX_VERSION = '0.1';
	public static $aliasRoutes = [];
	public static $components;
	public static $app;
	private static $request;
	private static $response;
	private static $config = [];

	public function __construct ($config = [])
	{
		static::$config = $config;
		static::setAlias('web', $config['web']);
		static::setAlias('path', $config['path']);
		static::setAlias('gbox', static::getAlias('path') . '/gbox');

		static::setAlias('controllers', static::getAlias('gbox')  . '/controllers');
		static::setAlias('components', static::getAlias('gbox')  . '/components');
		static::setAlias('plugins', static::getAlias('gbox') . '/plugins');
		static::setAlias('modules', static::getAlias('gbox') . '/modules');
		static::setAlias('models', static::getAlias('gbox') . '/models');
		static::setAlias('views', static::getAlias('gbox') . '/views');
		static::setAlias('tmp', static::getAlias('gbox') . '/tmp');
		
		static::setAlias('layouts', static::getAlias('views') . '/layouts');
		static::setAlias('uploads-path', static::getAlias('path') . '/uploads');

		static::setAlias('uploads', static::getAlias('web') . '/uploads');
		static::setAlias('images', static::getAlias('web') . '/assets' . '/images');
		static::setAlias('fonts', static::getAlias('web') . '/assets' . '/fonts');
		static::setAlias('css', static::getAlias('web') . '/assets' . '/css');
		static::setAlias('js', static::getAlias('web') . '/assets' . '/js');

		session_save_path(realpath(Url::to('@tmp')));
		Session::init();

		if (property_exists(static::getConfig(), 'timezone'))
		{
			date_default_timezone_set(static::getConfig()->timezone);
		}
		else
		{
			date_default_timezone_set('America/Montevideo');
		}
	}

	private function getErrorView ($code = 500)
	{
		$alias = '@view-error-' . $code;
		if (!static::getAlias($alias))
		{
			$alias = '@view-error-500';
			if (!static::getAlias($alias))
			{
				throw new Exception("No se encuentra la vista predefinida para mostrar errores.");
			}
		}
		return $alias;
	}

	public static function simple_encrypt ($data, $salt)
	{  
		return trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $salt, $data, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))));
	}

	public static function simple_decrypt ($data, $salt)
	{  
		return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $salt, base64_decode($data), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)));
	}

	public function run ()
	{
		$this->beforeRun();
		static::$response = new Response;		
		static::$response->init();
		static::$request = new Request();
		try
		{
			$this->initComponents();
			$this->router();
		}
		catch (HttpException $exception)
		{
			if (ob_get_level() > 0) {
                ob_end_clean();
            }
			static::$response->setStatusCode($exception->statusCode);
			ob_start();
		        require Url::to($this->getErrorView($exception->statusCode));
		        $content = ob_get_contents();
			ob_end_clean();
            static::$response->data = $content;
		}
		catch (GboxException $exception)
		{
			if (ob_get_level() > 0) {
                ob_end_clean();
            }
			static::$response->setStatusCode(500);
			ob_start();
		        require Url::to($this->getErrorView(500));
		        $content = ob_get_contents();
			ob_end_clean();
            static::$response->data = $content;
		}
		catch (Exception $exception)
		{
			if (ob_get_level() > 0) {
                ob_end_clean();
            }
			static::$response->setStatusCode(500);
			ob_start();
		        require Url::to($this->getErrorView(500));
		        $content = ob_get_contents();
			ob_end_clean();
            static::$response->data = $content;
		}
		static::$response->send();
		$this->afterRun();
	}

	public static function setAlias ($alias, $route)
	{
		if (strpos($alias, '@') === false)
		{
			$alias = '@' . $alias;
		}
		static::$aliasRoutes[$alias] = $route;
	}

	public static function getAlias ($alias)
	{
		if (strpos($alias, '@') === false)
		{
			$alias = '@' . $alias;
		}
		if (isset(static::$aliasRoutes[$alias]))
		{
			return static::$aliasRoutes[$alias];
		}
		return false;
	}

	public static function removeAlias ($alias)
	{
		if (strpos($alias, '@') === false)
		{
			$alias = '@' . $alias;
		}
		if (isset(static::$aliasRoutes[$alias]))
		{
			unset(static::$aliasRoutes[$alias]);
			return true;
		}
		return false;
	}

	public static function createObject ($type, $params = [])
	{
		if (is_string($type))
		{
			$class = new \ReflectionClass($type);
			return $class->newInstanceArgs($params);
		}
		elseif (is_array($type) && isset($type['class']))
		{
			$class = new \ReflectionClass($type['class']);
			return $class->newInstanceArgs($params);
		}
		elseif (is_callable($type, true))
		{
			return call_user_func($type, $params);
		}
		elseif (is_array($type))
		{
			throw new InvalidConfigException('Object configuration must be an array containing a "class" element.');
		}
		else
		{
			throw new InvalidConfigException("Unsupported configuration type: " . gettype($type));
		}
	}

	public static function arrayToObject ($array = [])
	{
		$object = new \stdClass();
		foreach ($array as $key => $value)
		{
			$object->$key = $value;
		}
		return $object;
	}
	
	public static function getConfig ($key = null)
	{
		$config = self::arrayToObject(static::$config);
		if ($key)
		{
			return $config->{$key};
		}
		return $config;
	}
	
	public static function getModules ($key = NULL)
	{
		if (isset($key))
		{
			if (array_key_exists('modules', static::$config))
			{
				if (array_key_exists($key, static::$config['modules']))
				{
					return static::$config['modules'][$key];
				}
				else
				{
					return NULL;
				}
			}
		}
		else
		{
			return self::arrayToObject(static::$config['modules']);
		}
	}

	private function initComponents ()
	{
		static::$components = new \stdClass();
		foreach (static::$config['components'] as $alias => $data)
		{
			if (array_key_exists('class', $data))
			{
				if (is_string($data['class']))
				{
					static::$components->{$alias} = new $data['class'];
					if (isset($data['params']))
					{
						static::$components->{$alias}->loadParams($data['params']);
					}
					static::$components->{$alias}->init();
				}
			}
			else if (array_key_exists('identityClass', $data))
			{
				if (is_string($data['identityClass']))
				{
					static::$components->{$alias} = new $data['identityClass'];
					if (isset($data['params']))
					{
						static::$components->{$alias}->loadParams($data['params']);
					}
					static::$components->{$alias}->init();
				}
			}
			else if (array_key_exists('errorAction', $data))
			{
				if (is_string($data['errorAction']))
				{
					static::setAlias('view-error-500', static::getAlias('views') . '/' . $data['errorAction'] . '.php');
				}
				else if (is_array($data['errorAction']))
				{
					foreach ($data['errorAction'] as $code => $view)
					{
						static::setAlias('view-error-' . $code, static::getAlias('views') . '/' . $view . '.php');
					}
				}
			}
		}
	}
	
	public static function getResponse ()
	{
		return static::$response;
	}
	
	public static function getRequest ()
	{
		return static::$request;
	}

	private function router ()
	{
		/* Compruebo si se trata de una petición a un módulo configurado */
		if (static::$request->getModule())
		{
			/*=============================
			=            DEBUG            =
			=============================*/
			static::$components->debug->saveReportRouter([
				'message' => 'Module detected.',
				'subtype' => 'module',
				'name' => static::$request->getModule(),
			]);
			/*=====  End of DEBUG  ======*/

			$module_file = static::$request->getModule() . '/Module.php';
			$module_string = static::getModules(static::$request->getModule())['class'];
			$invoke = '\\app\\' . static::$request->getModule() . '\\controllers\\';

			require Url::to(['@modules', $module_file]);


			/*=============================
			=            DEBUG            =
			=============================*/
			static::$components->debug->saveReportRouter([
				'message' => 'File module path.',
				'subtype' => 'module',
				'path' => Url::to(['@modules', $module_file]),
			]);
			/*=====  End of DEBUG  ======*/

			$module = new $module_string;

			$autoload = new Gbox\Autoload;
			$autoload->addFilesByDir(Url::to('@models/'));
		}
		else
		{
			$invoke = '\\app\\controllers\\';
		}

		$controller = ucfirst(static::$request->getController()) . 'Controller';
		$action = 'action' . ucfirst(static::$request->getAction());

		/*=============================
		=            DEBUG            =
		=============================*/
		static::$components->debug->saveReportRouter([
			'message' => 'Set action.',
			'subtype' => 'action',
			'name' => $action,
		]);
		/*=====  End of DEBUG  ======*/
		
		$controller_url = Url::to('@controllers/' . $controller . '.php');

		/*=============================
		=            DEBUG            =
		=============================*/
		static::$components->debug->saveReportRouter([
			'message' => 'Read controller file.',
			'subtype' => 'controller',
			'url' => $controller_url,
		]);
		/*=====  End of DEBUG  ======*/
		
		if (!is_readable($controller_url)) throw new NotFoundHttpException("No se puede leer el archivo del controlador.");

		require $controller_url;
		$invoke .= $controller;

		if (!class_exists($invoke)) throw new NotFoundHttpException("No existe la clase en el controlador.");
		if (!is_callable([$invoke, $action])) throw new NotFoundHttpException("No se puede llamar al método (acción) en la clase del controlador.");

		if (static::$request->getArgs() !== NULL)
		{
			call_user_func_array([new $invoke, $action], static::$request->getArgs());
		}
		else
		{
			call_user_func([new $invoke, $action]);
		}
	}

	private function beforeRun ()
	{
		
	}

	private function afterRun ()
	{

	}
}