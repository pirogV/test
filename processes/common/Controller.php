<?php
namespace common;

use config\Registry;
use common\TraitStart;

class Controller
{
	
	use TraitStart;
	
	protected $toVar = [];
	protected $model = null;
	
	protected function render($view, $model = null)
	{
		if ($this->toVar !== []) {
			foreach($this->toVar as $k => $v) {
				$$k = $v;
			}
		}
		include Registry::get('filesRoot') . '/views/' . $view . '.php';
	}
}
