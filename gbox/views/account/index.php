<?php
use Gbox\helpers\Url;
use Gbox\helpers\Html;
use app\widgets\Bootstrap;
use Gbox\helpers\Form;
?>
<div class="row">
	<div class="col-md-6">
		<?=isset($msg) ? Bootstrap::Alert($msg, $modelAccount->hasErrors() ? 'danger' : 'success') : ''?>
		<?php $form = new Form([
			'method' => 'post',
		]); ?>
			<div class="panel panel-default">
			    <div class="panel-heading"><h3 class="panel-title"><strong><?=$this->title?></strong></h3></div>
			    <div class="panel-body">
			    	<p><strong>Datos personales</strong></p>
					<?=$form->field($modelAccount, 'firstname')->input('text', ['placeholder' => $modelAccount->getAttrLabel('firstname')])->label(false)?>
					<?=$form->field($modelAccount, 'lastname')->input('text', ['placeholder' => $modelAccount->getAttrLabel('lastname')])->label(false)?>
					<hr />
					<p><strong>Datos de acceso</strong></p>
					<?=$form->field($modelAccount, 'email')->input('email', ['placeholder' => $modelAccount->getAttrLabel('email')])->label(false)?>
					<?=$form->field($modelAccount, 'username')->input('text', ['disabled' => true])->label(false)?>
					<hr />
					<p><strong>Modificar contraseña</strong></p>
					<?=$form->field($modelAccount, 'password')->input('password', ['placeholder' => $modelAccount->getAttrLabel('password')])->label(false)?>
					<?=$form->field($modelAccount, 'password_confirm')->input('password', ['placeholder' => $modelAccount->getAttrLabel('password_confirm')])->label(false)?>
				</div>
				<div class="panel-footer text-right">
					<?=Html::submitButton('Guardar mi información', ['class' => 'btn btn-primary'])?>
				</div>
			</div>
		<?php $form->end() ?>
	</div>
</div>