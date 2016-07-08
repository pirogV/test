<?php 

use common\Form;

?>

<div class="P">
	<?=Form::begin($model->rules(), ['name' => 'card', 'head' => 'Фильтр'])?>
	<?=Form::text25('series', 'Серия')?>
	<?=Form::text25('number', 'Номер')?>
	<?=Form::datepicker('issue_date', 'Дата выдачи')?>
	<?=Form::checkboxArray('expiration_date', $model->expiration_date, 'Срок действия')?>
	<?=Form::checkboxArray('status', $model->status, 'Статус')?>
	<?=Form::submitAjax('/', 'box-card', 'Показать')?>
	<?=Form::end()?>
</div>
<div class="LN"></div>
