
<?php use common\Html ?>

<div class="P w50">
	<div class="tr LN">
		<div class="left">ID</div>
		<div class="right"><?=$id?></div>
	</div>
	<div class="tr LN">
		<div class="left">Серия</div>
		<div class="right"><?=$series?></div>
	</div>
	<div class="tr LN">
		<div class="left">Номер</div>
		<div class="right"><?=$number?></div>
	</div>
	<div class="tr LN">
		<div class="left">Дата выдачи</div>
		<div class="right"><?=$issue_date?></div>
	</div>
	<div class="tr LN">
		<div class="left">Срок действия</div>
		<div class="right"><?=$model->expiration_date[$expiration_date]?></div>
	</div>
	<div class="tr LN">
		<div class="left">Статус</div>
		<div class="right"  id="status-<?=$id?>"><?=$model->getStatusLink($status, $id)?></div>
	</div>
</div>
