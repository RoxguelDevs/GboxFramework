<?php
namespace Gbox\base;
use Gbox;
use Gbox\base\View;
abstract class Module {
	abstract public function init ();
	public function __construct ()
	{
		Gbox::setAlias('module', Gbox::getAlias('gbox') . '/modules' . '/' . Gbox::getRequest()->getModule());
		Gbox::setAlias('web-module', Gbox::getAlias('web') . '/' . Gbox::getRequest()->getModule());
		Gbox::setAlias('controllers', Gbox::getAlias('module') . '/controllers');
		Gbox::setAlias('models', Gbox::getAlias('module') . '/models');
		Gbox::setAlias('views', Gbox::getAlias('module') . '/views');
		Gbox::setAlias('layouts', Gbox::getAlias('views') . '/layouts');
	}
}