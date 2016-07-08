<?php
/**
* Файл входа
* 
* Подключам конфиг, старт, автолоад, стартуем.
*
* @author Виктор Пирог
*/
error_reporting(E_ALL);
//ini_set("display_errors", 0);
//ini_set("log_errors", 1);

require(__DIR__ . '/../processes/Autoload.php');
require(__DIR__ . '/../processes/config/Registry.php');
require(__DIR__ . '/../processes/Start.php');

$start = new Start(require(__DIR__ . '/../processes/config/Config.php'));
$start->run();
