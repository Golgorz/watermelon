<?php

namespace core\controllers\http\decorators;



use core\helpers\Utils;
use core\controllers\http\psr\Request;

/**
 * @author Diego
 *
 */
class RequestJSONDecorator {

    private $request;

	function __construct(Request &$request) {
		$this->request = $request;
	}

	public function setJSONResponse() {
		$this->request->getResponse()->putHeaderValue("Content-Type", "application/json");
		if($this->isJson($this->request->getResponse()->getResponseBody()) === false) {
			$keysParsed = Utils::convertArrayKeysToUtf8($this->request->getResponse()->getResponseBody());
			$this->request->getResponse()->setResponseBody(json_encode($keysParsed));
		}
	}
	
	function isJson($str) {
		$json = json_decode($str);
		return $json && $str != $json;
	}

}