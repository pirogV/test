<?php 

use common\Html;

?>
<div id="box-card">
<div class="P">
	<table width="100%"  border="1" cellspacing="0" cellpadding="4" bordercolor="#C2C2C2">
		<tr>
			<th>ID</th>
			<th>Серия</th>
			<th>Номер</th>
			<th>Дата выдачи</th>
			<th>Срок действия</th>
			<th>Статус</th>
			<th>Удаление</th>
		</tr>
	<?php foreach ($model->allCard['cards'] as $k => $v):?>
		<tr>
			<td><a href="/discount/view/<?=$v['id']?>">#<?=$v['id']?> Просмотр</a></td>
			<td><?=$v['series']?></td>
			<td><?=$v['number']?></td>
			<td><?=$v['issue_date']?></td>
			<td><?=$model->expiration_date[$v['expiration_date']]?></td>
			<td id="status-<?=$v['id']?>"><?=$model->getStatusLink($v['status'], $v['id'])?></td>
			<td><a href="/discount/delete/0/<?=$v['id']?>/<?=$model->urlPageCount?>" class="ajax-confirm red" box="content">Удалить</a></td>
		</tr>
	<?php endforeach?>
	</table>
	<div class="LN"></div>
	<div class="PV"><?=$model->allCard['pagination']?></div>
</div>
</div>
