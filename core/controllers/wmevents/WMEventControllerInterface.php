<?php

/**
 * Event Controller Interface
 *
 * @author Diego Lopez Rivera (forgin50@gmail.com)
 *
 */
namespace core\controllers\wmevents;

interface WMEventControllerInterface {


	/**
	 * Listen to event.
	 *
	 * @param string $event
	 * @param callable $callback
	 * @return void
	 */
    public static function on($event, callable $callback);

    /**
     * Listen to event only once
     *
     * @param string $event
     * @param callable $callback
     * @return void
     */
    public static function once($event, callable $callback);



    /**
     * Dispatch event to all listeners.
     * The dispatched function must return true, or false to stop propagation.
     *
     * @param String $event
     * @param array $params
     * @return void
     */
    public static function dispatch($event, array $params);


    /**
     * Get current list of available events.
     * @return array
     */
    public static function getEvents();




    /**
     * Remove event
     *
     * @param String $event
     * @return boolean
     */
    public static function removeEvent($event);

    /**
     * Remove listener from event.
     *
     * @param String $event
     * @return boolean
     */
    public static function removeListener($event, callable $callBack);


}
