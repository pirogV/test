<?php 

use common\Form;

?>
<div id="box-card">
<div class="P layout">
<div><a href="/">Назад</a></div>
	<?=Form::begin($model->rulesCreate(), ['name' => 'card', 'head' => '', 'method' => 'post'])?>
		<div class="col1">
			<?=Form::text50('series', 'Серия')?>
			<?=Form::radio('expiration_date', $model->expiration_date, 'Срок действия')?>
			<?=Form::text50('number', 'Количество')?>
		</div>
		<div class="mar3">
			<?=Form::submit()?>
		</div>
		<div class="form-wrap-input"><div class="LN"></div></div>
	<?=Form::end()?>
</div>
</div>