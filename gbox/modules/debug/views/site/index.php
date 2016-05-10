<?php
use \Gbox\helpers\Url;
use \Gbox\base\Session;
?>
<div class="row">
	<h3>File: <?=Session::get('files-debug-current')?></h3>
	<div class="col-md-3">
		<div class="list-group">
			<a href="<?=Url::to('@web-module', ['id' => Session::get('files-debug-current')])?>" class="list-group-item <?php if (empty(\Gbox::getRequest()->get('type'))) echo 'active'; ?>">Summary</a>
			<a href="<?=Url::to('@web-module', ['id' => Session::get('files-debug-current'), 'type' => 'router'])?>" class="list-group-item <?php if (\Gbox::getRequest()->get('type') == 'router') echo 'active'; ?>">Router</a>
			<a href="<?=Url::to('@web-module', ['id' => Session::get('files-debug-current'), 'type' => 'orm'])?>" class="list-group-item <?php if (\Gbox::getRequest()->get('type') == 'orm') echo 'active'; ?>">Orm</a>
			<a href="<?=Url::to('@web-module', ['id' => Session::get('files-debug-current'), 'type' => 'model'])?>" class="list-group-item <?php if (\Gbox::getRequest()->get('type') == 'model') echo 'active'; ?>">Model</a>
			<a href="<?=Url::to('@web-module', ['id' => Session::get('files-debug-current'), 'type' => 'sendmail'])?>" class="list-group-item <?php if (\Gbox::getRequest()->get('type') == 'sendmail') echo 'active'; ?>">Sendmail</a>
		</div>
	</div>
	<div class="col-md-9">
		<div class="panel-group" id="accordion-debug" role="tablist" aria-multiselectable="true">
			<?php $i = 0; ?>
			<?php foreach (\Gbox::$components->debug->getData(Session::get('files-debug-current')) as $record): ?>
				<?php $i++; ?>
				<?php if (!empty(\Gbox::getRequest()->get('type')) && $record['type'] != \Gbox::getRequest()->get('type')) continue; ?>
				<div class="panel panel-default">
					<div class="panel-heading" role="tab" id="heading-<?=$i?>">
						<h4 class="panel-title">
							<a role="button" data-toggle="collapse" data-parent="#accordion-debug" href="#collapse-<?=$i?>" aria-expanded="true" aria-controls="collapse-<?=$i?>">
								<?=ucfirst($record['type'])?>: <?=$record['message']?> <?php if (!empty($record['subtype'])) echo '(' . $record['subtype'] . ')' ?>
							</a>
						</h4>
					</div>
					<div id="collapse-<?=$i?>" class="panel-collapse collapse" role="tabpanel" aria-expanded="true" aria-labelledby="heading-<?=$i?>">
						<ul class="list-group">
							<?php foreach ($record as $item => $value): ?>
								<li class="list-group-item"><strong><?=ucfirst($item)?>:</strong> <?=$value?></li>
							<?php endforeach; ?>
						</ul>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
		<div class="panel-group" id="accordion-debug-summary-parse" role="tabpanel" aria-multiselectable="true">
			<div class="panel panel-default">
				<div class="panel-heading" role="tab" id="heading-summary-parse-<?=$i?>">
					<h4 class="panel-title">
						<a role="button" data-toggle="collapse" data-parent="#accordion-debug-summary-parse" href="#collapse-summary-parse-<?=$i?>" aria-expanded="true" aria-controls="collapse-summary-parse-<?=$i?>">
							Parse summary
						</a>
					</h4>
				</div>
				<div id="collapse-summary-parse-<?=$i?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-summary-parse-<?=$i?>">
					<div class="panel-body">
						<pre><?php print_r(\Gbox::$components->debug->getData(Session::get('files-debug-current'))) ?></pre>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>