<?php

namespace core\controllers\cache;

use core\controllers\cache\psr\CacheItemInterface;
use core\controllers\cache\psr\InvalidArgumentException;
/**
 * Class Item.
 */
class Item implements CacheItemInterface
{
	/**
	 * A $redisClient instance this Item used.
	 *
	 * @var RedisClient
	 */
	private $redisClient;

	/**
	 * Cache Item key name.
	 *
	 * @var string
	 */
	private $key;

	/**
	 * Cache Item value.
	 *
	 * @var mixed
	 */
	private $value;

	/**
	 * Cache Item ttl (time to live) in minutes.
	 *
	 * @var float
	 */
	private $ttlSeconds;

	/**
	 * Constructor.
	 *
	 * @param RedisClient $redisClient a RedisClient instance
	 * @param string          $key             Cache Item key name
	 * @param int             $minute          Cache Item expire time
	 */
	public function __construct(RedisClient $redisClient, $key, $ttlSeconds = null)
	{
		$this->setRedisClient($redisClient);
		$this->setKey($key);
		$this->setTtlSeconds($ttlSeconds);
	}

	/**
	 * @return RedisRepository
	 */
	public function getRedisClient()
	{
		return $this->redisClient;
	}

	/**
	 * @param RedisRepository $repository
	 */
	protected function setRedisClient(RedisClient $repository)
	{
		$this->redisClient = $repository;
	}

	/**
	 * Returns the key for the current cache item.
	 *
	 * The key is loaded by the Implementing Library, but should be available to
	 * the higher level callers when needed.
	 *
	 * @return string
	 *                The key string for this cache item.
	 */
	public function getKey()
	{
		return $this->key;
	}

	/**
	 * Set the TTL in seconds for the item. Changes will apply after the item is saved.
	 *
	 * @param int|null seconds
	 *
	 * @return static
	 */
	protected function setTtlSeconds($seconds)
	{
		$this->ttlSeconds = $seconds;

		return $this;
	}

	/**
	 * Retrieves the value of the item from the cache associated with this object's key.
	 *
	 * The value returned must be identical to the value originally stored by set().
	 *
	 * If isHit() returns false, this method MUST return null. Note that null
	 * is a legitimate cached value, so the isHit() method SHOULD be used to
	 * differentiate between "null value was found" and "no value was found."
	 *
	 * @return mixed
	 *               The value corresponding to this cache item's key, or null if not found.
	 */
	public function get()
	{
		if (is_null($this->value)) {
			$this->value = $this->redisClient->get($this->key);
		}

		return $this->value;
	}


	/**
	 * Confirms if the cache item lookup resulted in a cache hit.
	 *
	 * Note: This method MUST NOT have a race condition between calling isHit()
	 * and calling get().
	 *
	 * @return bool
	 *              True if the request resulted in a cache hit. False otherwise.
	 */
	public function isHit()
	{
		return $this->redisClient->exists($this->key);
	}

	/**
	 * Sets the value represented by this cache item.
	 *
	 * The $value argument may be any item that can be serialized by PHP,
	 * although the method of serialization is left up to the Implementing
	 * Library.
	 *
	 * @param mixed $value
	 *                     The serializable value to be stored.
	 *
	 * @return static
	 *                The invoked object.
	 */
	public function set($value)
	{
		$this->value = $value;

		return $this;
	}

	/**
	 * Sets the expiration time for this cache item.
	 *
	 * @param \DateTimeInterface $expiration
	 *                                       The point in time after which the item MUST be considered expired.
	 *                                       If null is passed explicitly, a default value MAY be used. If none is set,
	 *                                       the value should be stored permanently or for as long as the
	 *                                       implementation allows.
	 *
	 * @return static
	 *                The called object.
	 */
	public function expiresAt($expiration)
	{
		if (!($expiration instanceof \DateTimeInterface || $expiration instanceof \DateTime)) {
			throw new InvalidArgumentException('expiration argument must inherit DateTimeInterface');
		}

		$now = new \DateTime();

		return $this->setTtlSeconds(($expiration->getTimestamp() - $now->getTimestamp()));
	}

	/**
	 * Sets the expiration time for this cache item.
	 *
	 * @param int|\DateInterval $time
	 *                                The period of time from the present after which the item MUST be considered
	 *                                expired. An integer parameter is understood to be the time in seconds until
	 *                                expiration. If null is passed explicitly, a default value MAY be used.
	 *                                If none is set, the value should be stored permanently or for as long as the
	 *                                implementation allows.
	 *
	 * @return static
	 *                The called object.
	 */
	public function expiresAfter($time)
	{
		if (!is_int($time) && !($time instanceof \DateInterval)) {
			throw new InvalidArgumentException('time argument must be int or DateInterval');
		}

		// Convert DateInterval to minutes
		if ($time instanceof \DateInterval) {
			$now = new \DateTime();
			$end = clone $now;
			$end->add($time);
			$time = $end->getTimestamp() - $now->getTimestamp();
		}

		return $this->setTtlSeconds($time);
	}

	/**
	 * Save the Item to the database;.
	 *
	 * @return static
	 *                The called object.
	 */
	public function save() {
		$serializedValue = is_numeric($this->value) ? $this->value : serialize($this->value);
		if (is_null($this->ttlSeconds)) {
			$this->redisClient->set($this->key, $serializedValue);
		} else {
			$this->redisClient->setex($this->key, floatval($this->ttlSeconds), $serializedValue);
		}

		return $this;
	}
}