<?php
use Gbox\helpers\Url;
use Gbox\helpers\Html;
use app\widgets\Bootstrap;
use Gbox\helpers\Form;
?>
<div class="row">
	<div class="col-md-5">
		<?php $form = new Form([
			'method' => 'post',
			'action' => URL::to('@web/account/sign-in'),
		]); ?>
			<div class="panel panel-default">
			    <div class="panel-heading"><h3 class="panel-title"><strong>Iniciar sesión en <?=Gbox::getConfig()->params['app_name']?></strong></h3></div>
			    <div class="panel-body">
					<?=$form->field($modelSignIn, 'username')->input('text')->label('Usuario o correo electrónico')?>
					<?=$form->field($modelSignIn, 'password')->input('password')?>
				</div>
				<div class="panel-footer text-right">
					<div class="pull-left">
						<?=$form->field($modelSignIn, 'rememberMe')->input('checkbox', ['class' => ''])->label(false)->setTemplate('<div class="checkbox"><label>{input} No cerrar sesión</label></div>')?>
					</div>
					<?=Html::submitButton("Iniciar sesión", ["class" => "btn btn-primary"])?>
				</div>
			</div>
		<?php $form->end() ?>
	</div>
	<div class="col-md-5 col-md-offset-2">
		<?php $form = new Form([
			'method' => 'post',
			'action' => URL::to('@web/account/sign-up'),
		]); ?>
			<input type="hidden" name="quick_sign_up" value="1" />
			<div class="panel panel-default">
			    <div class="panel-heading"><h3 class="panel-title"><strong>Registrarme en <?=Gbox::getConfig()->params['app_name']?></strong></h3></div>
			    <div class="panel-body">
					<?=$form->field($modelSignUp, 'email')->input('email')?>
					<?=$form->field($modelSignUp, 'password')->input('password')?>
					<?=$form->field($modelSignUp, 'password_confirm')->input('password')?>
				</div>
				<div class="panel-footer text-right">
					<?=Html::submitButton("Registrarme", ["class" => "btn btn-primary"])?>
				</div>
			</div>
		<?php $form->end() ?>
	</div>
</div>