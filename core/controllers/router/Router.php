<?php
namespace core\controllers\router;

use core\controllers\wmevents\WMEventController;
use Core\Controllers\Http\Psr\Request;
use core\helpers\Utils;

class Router {

	private $routeCollection;
	private $namedRoutes = null;
	private $baseUrl = DOMAIN_HOST;
	private $request;
	private $requestUrl;


	public function __construct(RouteCollection $routesCollection, Request $request) {
		$this->routeCollection = $routesCollection;
		$this->request = $request;
	}


	public function matchRequest() {
		$this->requestUrl = $this->request->getUri()->__toString();
		if (($pos = strpos($this->requestUrl, '?')) !== false) {
			$this->requestUrl = substr($this->requestUrl, 0, $pos);
		}
		Utils::addTrailingSlash($this->baseUrl);
		$this->baseUrl .= DOMAIN_PATH;
		$this->findMatch($this->requestUrl);
	}


	public function findMatch($requestUrl) {
		$allRoutes = $this->routeCollection->getAllRoutes();
		$matchFound = false;
		
		foreach ($allRoutes as $route) {
			if(count($route->getVerbs()) == 0 || in_array($this->request->getMethod(), $route->getVerbs())) {
				
				$stringRoute = $route->getRegex();
				
				$pattern = "@^{$this->baseUrl}{$stringRoute}/?$@i";
				
				$matches = array();

				if (!preg_match($pattern, $requestUrl, $matches)) {
					continue;
				}
				array_shift($matches);

				if (preg_match_all("/:([\w]+)/", $route->getUrl(), $argument_keys)) {
					$argument_keys = $argument_keys[1];
					if(count($argument_keys) != count($matches)) {
						continue;
					}
					$this->request->parseRouteParams($argument_keys, $matches);

				}
				$matchFound = true;
				WMEventController::dispatch(ROUTER_MATCH_SUCCESS);
				$this->request->setHasMatch(true);
				$this->request->setMatchedRoute($route);
				WMEventController::dispatch(ROUTER_PARSE_PARAMS_BEFORE);
				$this->request->parseIncomingParams();
				WMEventController::dispatch(ROUTER_PARSE_PARAMS_AFTER);

				
				break;

			}
		}
		if($matchFound == false) {
			$this->request->setHasMatch(false);
			WMEventController::dispatch(ROUTER_MATCH_FAILED);
		}
	}



	public function generateRoute($routeName, array $params = array())  {

		if($this->namedRoutes == null) {
			$this->namedRoutes = array();
			foreach ($this->routeCollection->getAllRoutes() as $route) {
				$name = $route->getName();
				if (null !== $name) {
					$this->namedRoutes[$name] = $route;
				}
			}
		}

		if (!isset($this->namedRoutes[$routeName])) {
			return false;

		} else {
			$route = $this->namedRoutes[$routeName];
			$url = $route->getUrl();
			$param_keys = array();
			if ($params && preg_match_all("/:(\w+)/", $url, $param_keys)) {
				$param_keys = $param_keys[1];
				foreach ($param_keys as $key) {
					if (isset($params[$key])) {
						$url = preg_replace("/:(\w+)/", $params[$key], $url, 1);
					}
				}
			}

			return $url;
		}


	}


}
