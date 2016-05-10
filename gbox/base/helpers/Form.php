<?php
namespace Gbox\helpers;
use Gbox\helpers\FormField;
class Form
{
	private $enableClientValidation = true;
	private $method = 'get';
	private $action = '';
	private $options = [];
	public $template;
	public function __construct($config = [])
	{
		if (array_key_exists('method', $config))
		{
			$this->method = $config['method'];
		}
		if (array_key_exists('action', $config))
		{
			$this->action = $config['action'];
		}
		else
		{
			$this->action = \Gbox::getRequest()->getUrl();
		}
		if (array_key_exists('template', $config))
		{
			$this->template = $config['template'];
		}
		if (array_key_exists('options', $config))
		{
			$this->options = $config['options'];
		}
		$html = '<form';
			$html .= ' action="' . $this->action . '"';
			$html .= ' method="' . $this->method . '"';
			$html .= array_key_exists('class', $this->options) ? ' class="' . $this->options['class'] . '"' : '';
			$html .= '>';
		echo $html;
	}
	public function end ()
	{
		echo '</form>';
	}
	public function field ($model, $attr, $options = null)
	{
		return new FormField($this, $model, $attr, $options);
	}
}