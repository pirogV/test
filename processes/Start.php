<?php
/**
 * Клас маршрутизации
 * 
 * Разбираем $_SERVER['REQUEST_URI'] извлекаем параметры, подключам контроллер, темплейт.
 *
 * @author Виктор Пирог
 */
use config\Registry;
use common\TraitStart;

final class Start
{
	use TraitStart;

	public function __construct($config)
    {
		Registry::setArr($config);
		
		Registry::set('filesRoot', __DIR__);
		
		if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) and strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
			Registry::set('ajax', true);
		}
    }

	public function run()
    {
		$this->parseQuery();
		$this->route();
		
		if (!Registry::get('ajax')) {
			$this->getTemplate();
		}
    }

	/**
	 * private function parseQuery()
	 * 
	 * Разбираем $_SERVER['REQUEST_URI'] извлекаем параметры, подключам контроллер, темплейт.
	 * 
	 * @return void
	 */
    private function parseQuery()
    {

		$str = preg_replace('/[\.|\?|\#].*/s', '', strtolower($_SERVER['REQUEST_URI']));

		if (preg_match('/[^a-z0-9\/\-]/s', $str)) {
			$this->notFound();
		}

        $arr = explode('/', trim($str, '/'));
		
		if (!empty($arr[0])) {
			if (!strpos(Registry::CONTROLLERS, '|' . $arr[0] . '|')) {
				$this->notFound();
			}

			Registry::set('controller', $arr[0]);
		}

		if (isset($arr[1])) {
			Registry::set('action', $arr[1]);
		}
		
		Registry::set('get', array_slice($arr, 2));
	}

	/**
	 * private function route()
	 * 
	 * Маршрутизация
	 * 
	 * @return void
	 */
    private function route()
    {
		include __DIR__ . '/controllers/' . ucfirst(Registry::get('controller')) . 'Controller.php';

		$name_controller = 'controllers\\' . Registry::get('controller') . 'Controller';
		$controller = new $name_controller;

		if (!method_exists($controller, Registry::get('action') . 'Action')) {
			$this->notFound();
		}
		
		$name_action = Registry::get('action') . 'Action';
		
		if (Registry::get('ajax')) {
			$controller -> $name_action();
		} else {
			ob_start();
				$controller -> $name_action();
				Registry::set('content', ob_get_contents());
			ob_end_clean();
		}
	}
}
