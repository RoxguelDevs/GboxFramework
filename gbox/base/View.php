<?php
namespace Gbox\base;
use Gbox\helpers\Url;
use Gbox\helpers\Html;
use Gbox\exceptions\NotFoundHttpException;
class View {
	public $params = [];
	public $defaultExtension = 'php';
	public $theme;
	public $context;
	public $title;
	public $description;
	public $controller;
	public $metaTags = [];
	public $css = [];
	public $js = [];

	public function __construct ($context = null)
	{
		$this->setContextIfNull($context);
		$request = \Gbox::getRequest();
		// $this->controller = strtolower($request->getController());
		$this->controller = strtolower(preg_replace('/([A-Z]+)/', "-$1", lcfirst($request->getController())));
	}
	public function render ($view, $params = [], $context = null)
    {
    	$this->setContextIfNull($context);
        $viewFile = Url::to('@views/' . $this->controller . '/' . $view . '.' . $this->defaultExtension);
        $layoutFile = Url::to('@layouts/' . $this->context->getLayout() . '.php');

        ob_start();
	        $this->renderFile($viewFile, $params, $context);
	        $content = ob_get_contents();
		ob_end_clean();

		ob_start();
			$this->renderFileLayout($layoutFile, ['content' => $content], $context);
	        $out = ob_get_contents();
		ob_end_clean();
		\Gbox::getResponse()->data = $out;
    }
    public function renderFile ($viewFile, $params = [], $context = null)
    {
    	$this->setContextIfNull($context);
    	extract($params);

		/*=============================
		=            DEBUG            =
		=============================*/
		\Gbox::$components->debug->saveReportRouter([
			'message' => 'Read view file.',
			'subtype' => 'view',
			'url' => $viewFile,
		]);
		/*=====  End of DEBUG  ======*/

    	if (!is_readable($viewFile)) throw new NotFoundHttpException("No se puede leer el archivo de la vista.");
    	
    	require $viewFile;
    }
    public function renderFileLayout ($viewFile, $params = [], $context = null)
    {
    	$this->setContextIfNull($context);
    	extract($params);

		/*=============================
		=            DEBUG            =
		=============================*/
		\Gbox::$components->debug->saveReportRouter([
			'message' => 'Read layout file.',
			'subtype' => 'layout',
			'url' => $viewFile,
		]);
		/*=====  End of DEBUG  ======*/

    	if (!is_readable($viewFile)) throw new NotFoundHttpException("No se puede leer el archivo de la capa.");
    	
    	require $viewFile;
    }
    private function setContextIfNull ($context = null)
    {
    	if ($context)
    	{
    		$this->context = $context;
    	}
    }
    public function renderHead ()
    {
    	ob_start();
			$this->head();
			$head = ob_get_contents();
		ob_end_clean();
		return $head;
    }
    private function head ()
    {
    	echo Html::title($this->title);
		echo Html::meta('description', $this->description);
		foreach ($this->metaTags as $data)
		{
			$http = array_key_exists('http', $data) ? $data['http'] : null;
			echo Html::meta($data['name'], $data['content'], $http);
		}
		foreach ($this->css as $data)
		{
			if (is_string($data))
			{
				// var_dump(Url::checkExternalUrl($data));
				if (!Url::checkExternalUrl($data)) $data = ['@css', $data];
				echo Html::link(Url::to($data));
			}
			else if (is_array($data))
			{
				if (!Url::checkExternalUrl($data['url'])) $data['url'] = ['@css', $data['url']];
				echo Html::link(Url::to($data['url']), $data['rel'], $data['type']);
			}
		}
		foreach ($this->js as $data)
		{
			if (is_string($data))
			{
				if (!Url::checkExternalUrl($data)) $data = ['@js', $data];
				echo Html::script(Url::to($data));
			}
			else if (is_array($data))
			{
				if (!Url::checkExternalUrl($data['url'])) $data['url'] = ['@js', $data['url']];
				echo Html::script(Url::to($data['url']), $data['type'], $data['async']);
			}
		}
    }
    public function addAsset ($type, $data)
    {
    	switch ($type)
    	{
    		case 'css':
    			array_push($this->css, $data);
    			break;
    		case 'js':
    			array_push($this->js, $data);
    			break;
    		case 'meta':
    			array_push($this->metaTags, $data);
    			break;
    	}
    }
}
?>