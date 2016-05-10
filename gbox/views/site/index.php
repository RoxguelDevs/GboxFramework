<?php
use Gbox\helpers\Url;
use Gbox\helpers\Html;
?>
<div class="jumbotron">
	<?php if (Gbox::$components->user->isGuest): ?>
		<h1>Bienvenido</h1>
	<?php else: ?>
		<h1>Bienvenido, <?=Gbox::$components->user->firstname?></h1>
	<?php endif; ?>
	<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Deleniti saepe dolore voluptas, recusandae quia, quaerat veritatis aperiam? Illum praesentium natus in labore, at harum hic quisquam consequuntur ad, architecto atque.</p>
	<p><a class="btn btn-primary btn-lg" href="<?=Url::to('@web/features')?>" role="button">Características</a></p>
</div>
