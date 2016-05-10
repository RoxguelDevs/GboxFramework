<?php
use Gbox\helpers\Url;
use Gbox\helpers\Html;
use app\widgets\Bootstrap;
use Gbox\helpers\Form;
?>
<div class="row">
	<div class="col-md-6">
		<?=$msg ? Bootstrap::Alert($msg, $model->hasErrors() ? 'danger' : 'success') : ''?>
		<?php $form = new Form([
			'method' => 'post',
		]); ?>
			<div class="panel panel-default">
			    <div class="panel-heading"><h3 class="panel-title"><strong><?=$this->title?></strong></h3></div>
			    <div class="panel-body">
					<?=$form->field($model, 'username')->input('text', ['placeholder' => 'Usuario o correo electrónico'])->label(false)?>
					<?=$form->field($model, 'password')->input('password', ['placeholder' => $model->getAttrLabel('password')])->label(false)?>
				</div>
				<div class="panel-footer text-right">
					<div class="pull-left">
						<?=$form->field($model, 'rememberMe')->input('checkbox', ['class' => ''])->label(false)->setTemplate('<div class="checkbox"><label>{input} No cerrar sesión</label></div>')?>
					</div>
					<?=Html::a('Registrarme', Url::to('@web/account/sign-up'), ['class' => 'btn btn-default'])?>
					<?=Html::submitButton('Iniciar sesión', ['class' => 'btn btn-primary'])?>
				</div>
			</div>
		<?php $form->end() ?>
	</div>
</div>