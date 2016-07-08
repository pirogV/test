<?php
spl_autoload_register (function ($className) {
	include (str_replace('\\', '/', __DIR__ . '/' . $className . '.php'));
});