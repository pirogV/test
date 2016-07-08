<?php
namespace common;

use config\Registry;

trait TraitStart {
	
    protected function notFound() 
	{
        $sapi_name = php_sapi_name();
		
		if ($sapi_name == 'cgi' || $sapi_name == 'cgi-fcgi') {
			header('Status: 404 Not Found');
		} else {
			header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
		}
		
		Registry::set('head', 'Not Found');
		Registry::set('content', '<div class="P red">Страница не найдена.</div>');
		Registry::set('template', '404');
		
		$this->getTemplate();
		
		exit;
    }

    protected function getTemplate()
    {
		if (strpos(Registry::TEMPLATES, Registry::get('template'))) {
			include Registry::get('filesRoot') . '/templates/' . Registry::get('template') . '/index.php';
		} else {
			throw new \Exception('Такой темплейт не создан и(или) не внесен в белый список.');
		}
	}

    protected function module($name)
    {
		if (strpos(Registry::MODULES, $name)) {
			require_once (Registry::get('filesRoot') . '/modules/' . $name . '/index.php');
		} else {
			throw new \Exception('Такой модуль не создан и(или) не внесен в белый список.');
		}
    }
}