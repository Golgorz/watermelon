<?php

use core\WMCore;
use app\App;

setShowError(true);
define("ROOT_PATH", __DIR__);
require_once 'core/WMAutoload.php';



//Watermelon starter logic
$watermelon = WMCore::getInstance();


//Your app initial logic
$app = App::getInstance();

//Active downloaded modules you installed
$activeModules = $app->loadModules();

//We load the bootstrap file of the modules
$watermelon->loadModules($activeModules);

//We attach your custom app routes
$app->attachRoutes();


//We launch the framework process
$watermelon->start();



function setShowError($showError) {
	if ($showError) {
		error_reporting(E_ALL);
		ini_set('display_errors', 1);
	}
}
