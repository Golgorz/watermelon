<?php
namespace core\controllers;

use Exception;

/**
 * Simple redis cache.
 * @author "Diego Lopez Rivera forgin50@gmail.com"
 *
 */
class SimpleCacheController {

	protected $redisClient;
	private static $instance;
	private static $redisClient;
	
	
	private function __construct(){}

	public static function get() {
		if ( !self::$instance instanceof self) {
			self::$instance = new self;
			self::$instancia->setRedisClient(self::getRedisClient());
		}
		return self::$instance;
	}

	protected function setRedisClient($client) {
		$this->redisClient = $client;
	}

	public  function getFromCache($key) {
		if(static::$redisClient->exists($key)) {
			return json_decode(static::$redisClient->get($key));
		}
		return null;
	}
	
	public  function deleteFromCache($key) {
		if(static::$redisClient->exists($key)) {
			static::$redisClient->del($key);
			return true;
		} else {
			return false;
		}
		
	}

	public function saveToCache($key, $value, $tts = 0) {
		try {
			if($tts != 0) {
				static::$redisClient->setex($key, $tts ,json_encode($value));
			} else {
				static::$redisClient->set($key, json_encode($value));
			}
			
		} catch (Exception $e) {
			return false;
		}

		return true;
	}

	public static function getRedisClient() {
		if(self::$redisClient == null) {
			self::$redisClient = new \Predis\Client(array (
					'scheme' => DB_REDIS_SCHEME,
					'host' => DB_REDIS_HOST,
					'port' => DB_REDIS_PORT
			));
			return self::$redisClient;
		} else {
			return self::$redisClient;
		}
	
	}

}