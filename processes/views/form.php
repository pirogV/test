<?php 

use common\Form;

?>
<div id="box-card">
<div class="P layout">
	<?=Form::begin($model->rulesSearch(), ['name' => 'card', 'head' => 'Фильтр'])?>
		<div class="col1">
			<?=Form::text50('series', 'Серия')?>
			<?=Form::text50('id', 'Номер')?>
			<?=Form::datepicker('issue_date', 'Дата выдачи')?>
		</div>
		<div class="col2">
			<?=Form::checkboxArray('expiration_date', $model->expiration_date, 'Срок действия')?>
			<?=Form::checkboxArray('status', $model->status, 'Статус')?>
		</div>
		<div class="mar">
			<?=Form::submitAjax('/', 'box-card', 'Поиск', '<a href="/discount/create" class="pagination">Создать дисконтную карточку</a>')?>
		</div>
		<div class="form-wrap-input"><div class="LN"></div></div>
	<?=Form::end()?>
</div>
