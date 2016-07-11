<?php 

use common\Html;

?>

<div id="del-card">
<div class="P layout">
	<div><a href="/">Все карты</a></div>
	<div class="col3">
		<h3>Карточка</h3>
		<div class="tr LN">
			<div class="left">Серия</div>
			<div class="right"><?=$series?></div>
		</div>
		<div class="tr LN">
			<div class="left">Номер</div>
			<div class="right"><?=$id?></div>
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
			<div class="right" id="status-<?=$id?>"><?=$model->getStatusLink($status, $id)?></div>
		</div>
		<div class="tr LN">
			<div class="left">Сумма покупок</div>
			<div class="right"><?=$sum_price?></div>
		</div>
		<div class="tr LN">
			<div class="left">Последняя дата использования</div>
			<div class="right"><?=$last_date?></div>
		</div>
	</div>
	<div class="col4">
		<h3>История покупок</h3>
		<table class="tbl" width="100%"  border="1">
			<tr>
				<th>ID Заказа</th>
				<th>Название продукта</th>
				<th>Дата покупки</th>
				<th>Цена</th>
				<th>Количество</th>
			</tr>
		<?php foreach ($history as $k => $v):?>
			<tr>
				<td><?=$v['id']?></td>
				<td><?=$v['name']?></td>
				<td><?=$v['date']?></td>
				<td><?=$v['price']?></td>
				<td><?=$v['count']?></td>
			</tr>
		<?php endforeach?>
		</table>
		<?php if ($history === []): ?>
		<div class="red">Карточка не использовалась</div>
		<?php endif; ?>
	</div>
	<div class="LN mar2"></div>
	<br><div><a href="/discount/delete/0/<?=$id?>/1" form="card" class="ajax-confirm pagination red" box="del-card">Удалить</a></div>
</div>
</div>