<?php
namespace app\widgets;
use Gbox;
use Gbox\base\Widget;
use Gbox\helpers\Url;
use Gbox\helpers\Html;
class BootstrapNavbar extends Widget
{
	private $htmlArray = [];
	private $id;
	private $config = [
		'container' => 'container-fluid',
		'theme' => 'navbar-default',
		'brand' => 'Brand',
	];
	public function __construct ($id, $config = [])
	{
		$this->id = $id;
		$this->config = array_merge($this->config, $config);
		$this->addHtml('<nav class="navbar ' . $this->config['theme'] . '">');
		$this->addHtml('<div class="' . $this->config['container'] . '">');
	}
	public function addHtml ($nav)
	{
		array_push($this->htmlArray, $nav);
	}
	public function getHtml ()
	{
		return implode('', $this->htmlArray);
	}

	public static function newHeader ($id)
	{
		$html  = '<div class="collapse navbar-collapse" id="bs-navbar-collapse-' . $id . '">';
			$html .= '<ul class="nav navbar-nav">';
				foreach ($items as $item)
				{
					$html .= '<li><a href="' . $item['url'] . '"' . (!empty($item['target']) ? 'target="' . $item['target'] . '"' : '') . '>' . $item['name'] . '</a></li>';
				}
			$html .= '</ul>';
		$html .= '</div>';
		return $html;
	}
	public static function newNav ($id, $items = [])
	{
		$html  = '<div class="collapse navbar-collapse" id="bs-navbar-collapse-' . $id . '">';
			$html .= '<ul class="nav navbar-nav">';
				foreach ($items as $item)
				{
					$html .= '<li><a href="' . $item['url'] . '"' . (!empty($item['target']) ? 'target="' . $item['target'] . '"' : '') . '>' . $item['name'] . '</a></li>';
				}
			$html .= '</ul>';
		$html .= '</div>';
		return $html;
	}
}