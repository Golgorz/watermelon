<?php
namespace app;

use core\WMCore;
use core\controllers\http\psr\Response;
use core\controllers\i18nController;

class AppIndex {
	
	private $WMCore;
	private static $instance;
	
	private function __construct() {
		
		$this->WMCore = WMCore::getInstance();
		self::init();
	
	}
	
	
	/**
	 *
	 * @return AppIndex
	 */
	public static function getInstance() {
		if (!isset(self::$instance)) {
			$myClass = __CLASS__;
			self::$instance = new $myClass;
		}
		return self::$instance;
	}
	
	public function __clone() {
		trigger_error('Cannot clone AppIndex class', E_USER_ERROR);
	}
	
	
	function init() {
		
	}
	
	/**
	 * Load your modules here
	 * @return string[]
	 */
	function loadModules() {
		
		$activeModules = array();
		//$activeModules[] = "Modules\GFStarterKit\Bootstrap";
		//$activeModules[] = "Modules\GFFileManager\Bootstrap";
		
		return $activeModules;
	}
	
	/**
	 * Attach your routes here
	 */
	function attachRoutes() {
// 		$this->GFStarter->withRoute("all", "/generador", PAGAssignGenerator::class);

		$this->WMCore->withRoute("all", "/", function() {
			Response::getResponseInstance()->writeToBody("<b>Home!</b>");
		});
		
		$this->WMCore->withRoute("all", "/xss", function($data) {
			$body = "<p>XSS</p> ";
			Response::getResponseInstance()->writeToBody($body);
		});
			$this->WMCore->withRoute("all", "/xss/:amigo", function($data) {
				$body = "<p>XSS</p> ";
				var_dump($data);die();
				Response::getResponseInstance()->writeToBody($body);
			});
	
		$this->WMCore->withRoute("all", "/test/:id", function($data) {
			Response::getResponseInstance()->writeToBody("<b>It Works! " . $data["id"] . "</b>");
		});
		
		
		$this->WMCore->withRoute("all", "/func", function() {
			Response::getResponseInstance()->writeToBody("<b>It Works! lang: </b>" . i18nController::getDefaultLanguage());
		});
	}
	
	function progress($resource,$download_size, $downloaded, $upload_size, $uploaded)
	{
			echo $uploaded / $upload_size  * 100 . "<br>";
			echo $downloaded / $download_size  * 100 . "<br>";
			ob_flush();
			flush();
	}
}