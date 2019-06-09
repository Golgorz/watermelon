<?php

namespace core\controllers\router;

class RouteCollection extends \SplObjectStorage
{
	private static $instancia;

	public static function getInstance()
	{
		if (  !self::$instancia instanceof self)
		{
			self::$instancia = new self;
		}
		return self::$instancia;
	}

	private function __construct()
	{

	}

	public function attachRoute(RouteModel $routeObject) {
		parent::attach($routeObject);
	}

	public function dettachRoute(RouteModel $routeObject){
		if($this->contains($routeObject))
			parent::detach($routeObject);
	}
	
	/**
	 * 
	 * @return RouteModel[]
	 */

	public function getAllRoutes(){
		$routes = array();
		foreach ($this as $route) {
			$routes[] = $route;
		}
		return $routes;
	}

	public function updateRouteConfig($urlOfRoute, $newConfig) {
		foreach ($this as $route) {
			if($route->getUrl() == $urlOfRoute) {
				$this->detach($route);
				$route->updateConfig($newConfig);
				$this->attach($route);
				return true;
			}
		}
		return false;
	}

	public function addOrUpdateRoute(RouteModel $routeObject) {
		foreach ($this as $route) {
			if($route->getUrl() == $routeObject->getUrl()) {
				$this->detach($route);
				$this->attach($routeObject);
				return true;
			}
		}
		parent::attach($routeObject);
		return true;
	}
}