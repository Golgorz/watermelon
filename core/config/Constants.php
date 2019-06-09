<?php

include_once 'EventConstants.php';

date_default_timezone_set("Europe/Madrid");

if (!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}

define("APP_ROOT_PATH", ROOT_PATH . '/app' );
define("APP_LOCALIZATION_PATH", ROOT_PATH . '/app/localization' );
define("LOG_FILE_PATH", ROOT_PATH . '/core/logs' );


//LOCALIZATION
define("LOCALIZATION_ENABLED", true);
define("DEFAULT_LOCALIZATION", "es");


//EVENTS
define("EVENTS_SYSTEM_ENABLED", true);


//LOGGING
define("LOGGING_ENABLED", true);
define("LOGGING_TO_FILE", true);
define("LOGGING_TO_MYSQL", true);


//HOST CONFIGURATION
define("DOMAIN_HOST","localhost");
define("DOMAIN_PATH","watermelon");


//REDIS CACHE
define("REDIS_CACHE_ENABLED", false);


//JWT
define("JWT_EXPIRES", false);
define("JWT_EXPIRES_TIME", 3600); //maximum of 3600
define("JWT_CHECK_AUD", true);