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
			<?=Gbox::getRequest()->post('quick_sign_up') === '1' ? '<input type="hidden" name="quick_sign_up" value="1" />' : ''?>
			<div class="panel panel-default">
			    <div class="panel-heading"><h3 class="panel-title"><strong><?=$this->title?></strong></h3></div>
			    <div class="panel-body">
			    	<p><strong>Datos personales</strong></p>
					<?=$form->field($model, 'firstname')->input('text', ['placeholder' => $model->getAttrLabel('firstname')])->label(false)?>
					<?=$form->field($model, 'lastname')->input('text', ['placeholder' => $model->getAttrLabel('lastname')])->label(false)?>
					<hr />
					<p><strong>Datos de acceso</strong></p>
					<?=$form->field($model, 'email')->input('email', ['placeholder' => $model->getAttrLabel('email')])->label(false)?>
					<?=$form->field($model, 'username')->input('text', ['placeholder' => $model->getAttrLabel('username')])->label(false)?>
					<hr />
					<p><strong>Contraseña</strong></p>
					<?=$form->field($model, 'password')->input('password', ['placeholder' => $model->getAttrLabel('password')])->label(false)?>
					<?=$form->field($model, 'password_confirm')->input('password', ['placeholder' => $model->getAttrLabel('password_confirm')])->label(false)?>
				</div>
				<div class="panel-footer text-right">
					<?=Html::a('Iniciar sesión', Url::to('@web/account/sign-in'), ['class' => 'btn btn-default'])?>
					<?=Html::submitButton('Registrarme', ['class' => 'btn btn-primary'])?>
				</div>
			</div>
		<?php $form->end() ?>
	</div>
</div>