<?php
require_once __DIR__ .'/config/Constants.php';


function namespaceAutoloads($class) {

	if(file_exists($class . ".php")) {
		require_once $class.".php";
	} else {
		$filename = ROOT_PATH . DS . $class . '.php';
		$filename = str_replace('\\', DS, $filename);
		if (file_exists($filename)) {
			require_once $filename;
		} else {
			return;
		}
	}
}
spl_autoload_register('namespaceAutoloads');

if(file_exists( __DIR__ .'/vendors/autoload.php'))
	require_once __DIR__ .'/vendors/autoload.php';

if(REDIS_CACHE_ENABLED)
	Predis\Autoloader::register();