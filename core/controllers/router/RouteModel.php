<?php
namespace core\controllers\router;

class RouteModel
{
    /**
     * URL of this Route
     * @var string
     */
    public $url;

    /**
     * The name of this route, used to get url from route name
     * @var string
     */
    public $name;

    /**
     * The method of the class that should be called
     * @var string
     */
    public $targetClassMethod;

    /**
     * The class that should be instanciated
     * @var string
     */
    public $targetClass;

    /**
     * If CSRF check is needed (Only with POST method i guess)
     * @var boolean
     */
    public $isCSRFProtected;

    /**
     * Accepted HTTP verb, empty array for all.
     * @var array() $verbs
     */
    public $verbs;

    /**
     * function to run when route match. if set, ignores all other variables
     * @var function
     */
    public $function;
    

    private function __construct() {

    }

    /**
     * @param String $url
     * @param array() $config ("name","targetClassMethod","targetClass","verbs","checkCSRF")
     */
    public static function withConfig($url, array $config) {

    	$instance = new self();

    	$instance->url     			 	= $url;
    	$instance->name					= isset($config['name']) ? $config['name'] : null;
    	$instance->targetClassMethod	= isset($config['targetClassMethod']) ? $config['targetClassMethod'] : null;
    	$instance->targetClass 			= isset($config['targetClass']) ? $config['targetClass'] : null;
    	$instance->verbs 				= isset($config['verbs']) ? (array) $config['verbs'] : array();
    	$instance->isCSRFProtected		= isset($config['checkCSRF']) ? $config['checkCSRF'] : true;

        return $instance;
    }

    public static function with($url, $class, $checkcsrf = false, $method = null, $name = "" ) {

    	$instance = new self();

    	$instance->url     			 	= $url;
    	$instance->name					= $name;
    	$instance->targetClass 			= $class;
    	$instance->targetClassMethod	= $method;
    	$instance->isCSRFProtected			= $checkcsrf;

    	return $instance;
    }

    public static function withFunction($url, $function) {
    	$instance = new self();

    	$instance->url     			 	= $url;
    	$instance->name					= "";
    	$instance->function				= $function;
    	$instance->targetClass 			= null;
    	$instance->targetClassMethod	= null;
    	$instance->isCSRFProtected			= null;
    	return $instance;
    }

    /**
     * @param array() $config ("name","targetClassMethod","targetClass","verbs","checkCSRF")
     */
    public function updateConfig($config) {
    	$this->name					= isset($config['name']) ? $config['name'] : null;
        $this->targetClassMethod	= isset($config['targetClassMethod']) ? $config['targetClassMethod'] : null;
        $this->targetClass 			= isset($config['targetClass']) ? $config['targetClass'] : null;
        $this->verbs 				= isset($config['verbs']) ? (array) $config['verbs'] : array();
        $this->isCSRFProtected		= isset($config['checkCSRF']) ? $config['checkCSRF'] : true;
    }


    public function getUrl() {
        return $this->url;
    }

    //ADDS LAST SLASH TO URL IF NOT SET
    public function setUrl($url) {
        $url = (string)$url;
        if (substr($url, -1) !== '/') {
            $url .= '/';
        }
        $this->url = $url;
    }


    public function getVerbs() {
        return $this->verbs;
    }
    public function setVerbs(array $methods) {
        $this->verbs = $methods;
    }

    public function getFunction() {
    	return $this->function;
    }
    public function setFunction($callback) {
    	$this->function = $callback;
    }



    public function getName() {
        return $this->name;
    }
    public function setName($name)  {
        $this->name = (string)$name;
    }


    public function getTargetClassMethod() {
    	return $this->targetClassMethod;
    }
    public function setTargetClassMethod($targetClassMethod) {
    	$this->targetClassMethod = $targetClassMethod;
    }


    public function getTargetClass() {
    	return $this->targetClass;
    }
    public function setTargetClass($targetClass) {
    	$this->targetClass = $targetClass;
    }


    public function getRegex()  {
        return preg_replace('/(:\w+)/', '([\w]+)', $this->url);
    }


	public function getCheckCSRF() {
		return $this->isCSRFProtected;
	}
	public function setCheckCSRF($checkCSRF) {
		$this->isCSRFProtected = $checkCSRF;
	}



}
