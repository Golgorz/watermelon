<?php
namespace core\controllers\monolog;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use core\controllers\monoLog\MySQLHandler;

class LoggerController {

	private static $instance;
	private $logger;

	private function __construct(){
		$this->logger = new Logger('WM_LOGGER');
		$dsn = 'mysql:dbname='.$_ENV['MYSQL_DB_NAME'].';host='. $_ENV['MYSQL_HOST'];
		if(LOGGING_ENABLED) {
			if(LOGGING_TO_FILE)
				$this->logger->pushHandler(new StreamHandler(LOG_FILE_PATH .'/log-' . date("Y-m-d") . '.log', Logger::DEBUG));
			if(LOGGING_TO_MYSQL)
				$this->logger->pushHandler(new MySQLHandler(new \PDO($dsn, $_ENV['MYSQL_DB_USER'], $_ENV['MYSQL_DB_PASS'])));
			
		}
	}

	public static function get() {
		if ( !self::$instance instanceof self) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	public function logDebug($string, array $context = array()) {
		if(LOGGING_ENABLED)
			$this->logger->debug($string, $context);
	}
	public function logInfo($string, array $context = array()) {
		if(LOGGING_ENABLED)
			$this->logger->info($string, $context);
	}
	public function logNotice($string) {
		if(LOGGING_ENABLED)
			$this->logger->notice($string);
	}
	public function logWarning($string) {
		if(LOGGING_ENABLED)
			$this->logger->warning($string);
	}
	public function logError($string) {
		if(LOGGING_ENABLED)
			$this->logger->error($string);
	}
	public function logCritical($string) {
		if(LOGGING_ENABLED)
			$this->logger->critical($string);
	}
	public function logAlert($string) {
		if(LOGGING_ENABLED)
			$this->logger->alert($string);
	}
	public function logEmergency($string) {
		if(LOGGING_ENABLED)
			$this->logger->emergency($string);
	}





}