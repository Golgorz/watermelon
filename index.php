<?php

use core\WMCore;
use app\AppIndex;

setShowError(true);
define("ROOT_PATH", __DIR__);
require_once 'core/WMAutoload.php';



//Watermelon starter logic
$watermelon = WMCore::getInstance();


//Your app initial logic
$AppIndex = AppIndex::getInstance();

//Active downloaded modules you installed
$activeModules = $AppIndex->loadModules();

//We load the bootstrap file of the modules
$watermelon->loadModules($activeModules);

//We attach your custom app routes
$AppIndex->attachRoutes();


//We launch the framework process
$watermelon->start();



function setShowError($showError) {
	if ($showError) {
		error_reporting(E_ALL);
		ini_set('display_errors', 1);
	}
}
