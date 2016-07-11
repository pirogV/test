<?php 

use common\Html;

?>

<div id="box-card">
<div class="P">
	<table class="tbl" width="100%"  border="1">
		<tr>
			<th>Серия - Номер</th>
			<th>Дата выдачи</th>
			<th>Срок действия</th>
			<th>Статус</th>
			<th>Удаление</th>
			<th></th>
		</tr>
	<?php foreach ($model->allCard['cards'] as $k => $v):?>
		<tr>
			<td><?=$v['series']?> - <?=$v['id']?></td>
			<td><?=$v['issue_date']?></td>
			<td><?=$model->expiration_date[$v['expiration_date']]?></td>
			<td id="status-<?=$v['id']?>"><?=$model->getStatusLink($v['status'], $v['id'])?></td>
			<td><a href="/discount/delete/0/<?=$v['id']?>/<?=$model->urlPageCount?>" form="card" class="ajax-confirm red" box="box-card">Удалить</a></td>
			<td align="center"><a href="/discount/view/<?=$v['id']?>">Просмотр</a></td>
		</tr>
	<?php endforeach?>
	</table>
	<div class="LN"></div>
	<div class="PV"><?=$model->allCard['pagination']?></div>
</div>
</div>
