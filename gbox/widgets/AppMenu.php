<?php
namespace app\widgets;
use \Gbox;
use Gbox\base\Widget;
use Gbox\helpers\Url;
use Gbox\helpers\Html;
use app\widgets\Bootstrap;
class AppMenu extends Widget
{
	public static function Show ()
	{
		return Bootstrap::Navbar('mainmenu', Bootstrap::NavbarNav('mainmenu', [
			[
				'name' => 'Página principal',
				'url' => Url::goHome(),
			],
			[
				'name' => 'Características',
				'url' => Url::to('@web/features'),
			],
			[
				'name' => 'GitHub',
				'url' => 'https://github.com/RoxguelDevs/GboxFramework',
				'target' => '_blank',
			],
			Gbox::$components->user->isGuest ? null : [
				'name' => 'Mi cuenta',
				'url' => Url::to('@web/account'),
			],
			Gbox::$components->user->isGuest ? [
				'name' => 'Acceder',
				'url' => Url::to('@web/account/login'),
			] : [
				'name' => 'Cerrar sesión',
				'url' => Url::to('@web/account/sign-out'),
			],
		]));
	}
}