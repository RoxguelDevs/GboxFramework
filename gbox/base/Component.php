<?php
namespace Gbox\base;
abstract class Component {
	protected $params = [];
	public function __construct ()
	{
		
	}
	abstract public function init ();
	public function loadParams ($params)
	{
		$this->params = array_merge($this->params, $params);
	}
}