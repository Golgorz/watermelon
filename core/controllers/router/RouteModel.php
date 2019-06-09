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
     * Class or Class::method
     * @var string
     */
    public $target;
    
    /**
     * Accepted HTTP verb, empty array for all.
     * @var array() $verbs
     */
    public $verbs;

    

    private function __construct() {

    }

    /**
     * @param String $url
     * @param array() $config ("name","target","verbs")
     */
    public static function withConfig($url, array $config) {
    	
    	return self::with($url, 
    			isset($config['target']) ? $config['target'] : null, 
    			isset($config['verbs']) ? (array) $config['verbs'] : array(),
    			isset($config['name']) ? $config['name'] : "");
    }
    

    public static function with( $url, $target, $verbs = array(), $name = "") {

    	$instance = new self();

    	$instance->url     	= $url;
    	$instance->name		= $name;
    	$instance->target 	= $target;
    	$instance->verbs	= $verbs;
    	
    	if(is_null($instance->target) ) throw new \Exception('null target');

    	return $instance;
    }


    /**
     * @param array() $config ("name","targetClassMethod","targetClass","verbs","checkCSRF")
     */
    public function updateConfig($config) {
    	$this->name		= isset($config['name']) ? $config['name'] : $this->name;
        $this->target	= isset($config['target']) ? $config['target'] : $this->target;
        $this->verbs	= isset($config['verbs']) ? $config['verbs'] : $this->verbs;
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


    public function getName() {
        return $this->name;
    }
    public function setName($name)  {
        $this->name = (string)$name;
    }
    
    /**
     * 
     * @return \core\controllers\router\array()
     */
    public function getVerbs() {
    	return $this->verbs;
    }
    public function setVerbs(array $methods) {
    	$this->verbs = $methods;
    }


    public function getRegex()  {
        return preg_replace('/(:\w+)/', '([\w]+)', $this->url);
    }
	public function getTarget() {
		return $this->target;
	}
	public function setTarget($target) {
		$this->target = $target;
		return $this;
	}
	





}
