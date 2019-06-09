<?php

namespace core\controllers\http\psr;

use core\helpers\Collection;

/**
 * Headers
 *
 * This class represents a collection of HTTP headers
 * that is used in both the HTTP request and response objects.
 * It also enables header name case-insensitivity when
 * getting or setting a header value.
 *
 * Each HTTP header can have multiple values. This class
 * stores values into an array for each header name. When
 * you request a header value, you receive an array of values
 * for that header.
 */
class Headers extends Collection {

	/**
	 * If HTTP_AUTHORIZATION does not exist tries to get it from
	 * getallheaders() when available.
	 * @return array
	 */
	public static function getAuthorizationHeader() {
		$return = array ();
		if(is_callable('getallheaders')) {
			$headers = getallheaders();
			$headers = array_change_key_case($headers, CASE_LOWER);
			if(isset($headers['authorization'])) {
				$return['HTTP_AUTHORIZATION'] = $headers['authorization'];
			}
		}
		
		return $return;
	}

	/**
	 * Return array of HTTP header names and values.
	 *
	 * @return array
	 */
	public function all() {
		return parent::all();
	}

	/**
	 * Set HTTP header value
	 *
	 * This method sets a header value. It replaces
	 * any values that may already exist for the header name.
	 *
	 * @param string $key
	 *        	The case-insensitive header name
	 * @param string $value
	 *        	The header value
	 */
	public function set($key, $value) {
		if (!is_array($value)) {
			$value = array($value);
		}
		parent::set($this->normalizeKey($key), $value);
	}

	/**
	 * Get HTTP header value
	 *
	 * @param string $key
	 *        	The case-insensitive header name
	 * @param mixed $default
	 *        	The default value if key does not exist
	 *
	 * @return string[]
	 */
	public function get($key, $default = null) {
		if($this->has($key)) {
			return parent::get($this->normalizeKey($key));
		}
		return $default;
	}

	/**
	 * Add HTTP header value
	 *
	 * This method appends a header value. Unlike the set() method,
	 * this method _appends_ this new value to any values
	 * that already exist for this header name.
	 *
	 * @param string $key
	 *        	The case-insensitive header name
	 * @param array|string $value
	 *        	The new header value(s)
	 */
	public function add($key, $value) {
		$oldValues = $this->get($this->normalizeKey($key), array ());
		$newValues = is_array($value) ? $value : array ($value);
		$this->set($this->normalizeKey($key), array_merge($oldValues, $newValues));
	}

	/**
	 * Does this collection have a given header?
	 *
	 * @param string $key
	 *        	The case-insensitive header name
	 *
	 * @return bool
	 */
	public function has($key) {
		return parent::has($this->normalizeKey($key));
	}

	/**
	 * Remove header from collection
	 *
	 * @param string $key
	 *        	The case-insensitive header name
	 */
	public function remove($key) {
		parent::remove($this->normalizeKey($key));
	}

	/**
	 * Normalize header name
	 *
	 * @param string $key
	 *        	The case-insensitive header name
	 *
	 * @return string Normalized header name
	 */
	public function normalizeKey($key) {
		$key = strtolower($key);
		return $key;
	}
}
