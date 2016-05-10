<?php
use Gbox\base\Session;
use Gbox\helpers\Url;
use Gbox\helpers\Html;
use app\assets\SiteAssets;
use app\widgets\AppMenu;
use app\widgets\Bootstrap;
if (!$this->title) $this->title = 'Gbox Framework';
if (!$this->description) $this->description = 'Descripción de Gbox Framework';
SiteAssets::register($this);
?><!DOCTYPE html>
<html>
<head>
	<?=$this->renderHead()?>
</head>
<body>
	<?=AppMenu::Show()?>
	<div class="container">
		<?php
			if ($response = Session::get('response'))
			{
				echo Bootstrap::Alert($response['msg'], array_key_exists('type', $response) ? $response['type'] : 'danger', true);
				Session::destroy('response');
			}
		?>
		<?=$content?>
	</div>
</body>
</html>