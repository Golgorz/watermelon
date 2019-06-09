<?php

include_once 'EventConstants.php';

date_default_timezone_set("Europe/Madrid");

if (!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}

define("APP_ROOT_PATH", ROOT_PATH . '/app' );
define("LOG_FILE_PATH", ROOT_PATH . '/core/logs' );



//SESSION OPTIONS
define("SESSION_LENGTH", 3600); //30 MINS
define("SESSIONS_SYSTEM_ACTIVE", true);
define ("GF_GLOBAL_SESSION", "gf_session");
define ("GF_DEFAULT_SESSION", "gf_default");

//LOCALIZATION
define("LOCALIZATION_ENABLED", true);
define("DEFAULT_LOCALIZATION", "es");


//EVENTS
define("EVENTS_SYSTEM_ACTIVE", true);


//LOGGING
define("LOGGING_ENABLED", true);
define("LOGGING_TO_FILE", true);
define("LOGGING_TO_MYSQL", true);


//HOST CONFIGURATION
define("DOMAIN_HOST","localhost");
define("DOMAIN_PATH","watermelon");


//REDIS CACHE
define("REDIS_CACHE_ENABLED", false);



//SECURITY

define("CSRF_ENABLED", true);
