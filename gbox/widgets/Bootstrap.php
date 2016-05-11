<?php
namespace app\widgets;
use \Gbox;
use Gbox\base\Widget;
use Gbox\helpers\Url;
use Gbox\helpers\Html;
class Bootstrap extends Widget
{
	public static function Alert ($message, $type = 'success', $dismissible = false)
	{
		if (is_array($message))
		{
			$message[0] = '<strong>' . $message[0] . '</strong>';
			$message = implode(' ', $message);
		}
		$html  = '<div class="alert alert-' . $type . ' ' . ($dismissible ? 'alert-dismissible' : '') . '" role="alert">';
			if ($dismissible) $html .= '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
			$html .= $message;
		$html .= '</div>';
		return $html;
	}
	public static function NavbarNav ($id, $items = [])
	{
		$html  = '<div class="collapse navbar-collapse" id="bs-navbar-collapse-' . $id . '">';
			$html .= '<ul class="nav navbar-nav">';
				foreach ($items as $item)
				{
					if (!is_array($item)) continue;
					$html .= '<li class="' . (array_key_exists('class', $item) ? $item['class'] : '') . '"><a href="' . (array_key_exists('url', $item) ? $item['url'] : '#') . '"' . (!empty($item['target']) ? 'target="' . $item['target'] . '"' : '') . '>' . $item['name'] . '</a></li>';
				}
			$html .= '</ul>';
		$html .= '</div>';
		return $html;
	}
	public static function Navbar ($id = null, $navbar_nav = null)
	{
		$html  = '<nav class="navbar navbar-default navbar-static-top">';
			$html .= '<div class="container">';
				$html .= '<div class="navbar-header">';
					if ($navbar_nav)
					{
						$html .= '<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-navbar-collapse-' . $id . '" aria-expanded="false">';
							$html .= '<span class="sr-only">Toggle navigation</span>';
							$html .= '<span class="icon-bar"></span>';
							$html .= '<span class="icon-bar"></span>';
							$html .= '<span class="icon-bar"></span>';
						$html .= '</button>';
					}
					$html .= '<a class="navbar-brand" href="' . Url::goHome() . '">' . Gbox::getConfig()->params['app_name'] . '</a>';
				$html .= '</div>';
				if ($navbar_nav)
				{
					$html .= $navbar_nav;
				}
			$html .= '</div>';
		$html .= '</nav>';
		return $html;
	}
}