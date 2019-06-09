<?php
/**
 *
 * @author Diego Lopez Rivera (forgin50@gmail.com)
 *
 */
namespace core\controllers\wmevents;


class WMEventController implements WMEventControllerInterface, EventManagerInterface {

	private static $listeners = array();
	private static $psrEvents = array();

	
	

	/**
	 * {@inheritDoc}
	 * @see \Controllers\Events\EventControllerInterface::on()
	 */
	public static function on($event, callable $callback) {
		if(EVENTS_SYSTEM_ACTIVE === true)
			self::$listeners[$event][] = $callback;

	}

	/**
	 * {@inheritDoc}
	 * @see \Controllers\Events\EventControllerInterface::once()
	 */
	public static function once($event, callable $callback) {

		if(EVENTS_SYSTEM_ACTIVE === true) {
			$wrapper = null;
			$wrapper = function() use ($event, $callback, &$wrapper) {
				self::removeListener($event, $wrapper);
				return call_user_func_array($callback, func_get_args());
			};
			self::on($event, $wrapper);
		}
		

	}

	/**
	 * {@inheritDoc}
	 * @see \Controllers\Events\EventControllerInterface::dispatch()
	 */
	public static function dispatch($event, array $params = null) {
		if(EVENTS_SYSTEM_ACTIVE === true) {
			if(isset(self::$listeners[$event])) {
				$continue = true;
				foreach (self::$listeners[$event] as $listener ) {
					if($continue){
						$continue = call_user_func_array( $listener, $params);
					}
				}
			}
		}

	}

	/**
	 * {@inheritDoc}
	 * @see \Controllers\Events\EventControllerInterface::getEvents()
	 */
	public static function getEvents() {
		if(EVENTS_SYSTEM_ACTIVE === true)
			return array_keys(self::$listeners);

	}

	/**
	 * {@inheritDoc}
	 * @see \Controllers\Events\EventControllerInterface::getEvents()
	 */
	public static function getPsrEvents() {
		if(EVENTS_SYSTEM_ACTIVE === true)
			return array_keys(self::$psrEvents);

	}

	/**
	 * {@inheritDoc}
	 * @see \Controllers\Events\EventControllerInterface::removeEvent()
	 */
	public static function removeEvent($event) {
		if(EVENTS_SYSTEM_ACTIVE === true) {
			if(($key = array_search($event, self::$listeners)) !== false) {
				unset(self::$listeners[$key]);
			}
		}

	}

	/**
	 * {@inheritDoc}
	 * @see \Controllers\Events\EventControllerInterface::removeListener()
	 */
	public static function removeListener($event, callable $callBack) {
		if(EVENTS_SYSTEM_ACTIVE === true) {
			if (!isset(self::$listeners[$event])) {
				return false;
			}
			$index = array_search($callBack, self::$listeners[$event], true);
			if ($index !== false) {
				unset(self::$listeners[$event][$index]);
			}
			return true;
		}

	}


	/**
	 * {@inheritDoc}
	 * @see \Core\Controllers\GFEvents\EventManagerInterface::attach()
	 */
	public function attach($event, $callback, $priority = 0) {
		if(EVENTS_SYSTEM_ACTIVE === true) {
			if(isset(self::$psrEvents[$event])) {
				self::$psrEvents[$event][$priority][] = $callback;
				return true;
			} else {
				self::$psrEvents[$event][$priority][] = $callback;
				return true;
			}
			return false;
		}


	}

	/**
	 * {@inheritDoc}
	 * @see \Core\Controllers\GFEvents\EventManagerInterface::detach()
	 */
	public function detach($event, $callback) {
		if(EVENTS_SYSTEM_ACTIVE === true) {
			if(isset(self::$psrEvents[$event])) {
	
				foreach (self::$psrEvents[$event] as $priorities) {
					foreach ($priorities as $key=>$listener) {
						if($listener == $callback){
							unset(self::$psrEvents[$event][$priorities][$key]);
						}
					}
				}
	
				return true;
			}
			return false;
		}

	}

	/**
	 * {@inheritDoc}
	 * @see \Core\Controllers\GFEvents\EventManagerInterface::clearListeners()
	 */
	public function clearListeners($event) {
		if(EVENTS_SYSTEM_ACTIVE === true) {
			if(isset(self::$psrEvents[$event])) {
				self::$psrEvents[$event] = array();
			}
		}
	}

	/**
	 * {@inheritDoc}
	 * @see \Core\Controllers\GFEvents\EventManagerInterface::trigger()
	 */
	public function trigger($event, $target = null, $argv = []) {
		if(EVENTS_SYSTEM_ACTIVE === true) {
		    if(isset(self::$psrEvents[$event->getName()])) {
	    		$max = max(array_keys(self::$psrEvents[$event->getName()]));
	    		$lastResult = null;
	    		for ($i = $max; $i >= 0; $i--) {
	    			if(isset(self::$psrEvents[$event->getName()][$i])) {
	    				foreach(self::$psrEvents[$event->getName()][$i] as $key=>$callbacks) {
	    					$lastResult = call_user_func_array($callbacks, array(&$event,  $argv, $lastResult));
	    					if($event->isPropagationStopped()){
	    						break 2;
	    					}
	    				}
	    			}
	    		}
		    }
		}
	}

	public static function triggerWithEventName($name) {
		if(EVENTS_SYSTEM_ACTIVE === true) {
		    $event = new WMEvent();
		    $event->setName($name);
		    $evento = new self();
		    $evento->trigger($event);
		}
	}

}
