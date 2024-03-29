<?php
namespace core\controllers\cache;


/**
 * Interface ClientInterface.
 *
 * Interface for Redis connection and operation.
 */
interface ClientInterface
{
	/**
	 * Close the Redis connection.
	 */
	public function quit();

	/**
	 * Returns if key exists.
	 *
	 * @param $key
	 *
	 * @return bool
	 */
	public function exists($key);

	/**
	 * Set key to hold the string value. If key already holds a value,
	 * it is overwritten, regardless of its type. Any previous time to
	 * live associated with the key is discarded on successful SET operation.
	 *
	 * @param string $key
	 * @param string $value
	 */
	public function set($key, $value);

	/**
	 * Set key to hold the string value and set key to timeout after a
	 * given number of seconds.
	 *
	 * @param string $key
	 * @param int    $seconds
	 * @param string $value
	 */
	public function setex($key, $seconds, $value);

	/**
	 * Get the value of key. If the key does not exist the special value
	 * false is returned. An error is returned if the value stored at key
	 * is not a string, because GET only handles string values.
	 *
	 * @param string $key
	 *
	 * @return string|false
	 */
	public function get($key);

	/**
	 * Removes the specified keys. A key is ignored if it does not exist.
	 *
	 * @param array|mixed $keys
	 *
	 * @return int
	 */
	public function del($keys);

	/**
	 * Increments the number stored at key by one. If the key does not exist,
	 * it is set to 0 before performing the operation.
	 *
	 * @param string $key
	 *
	 * @return int
	 */
	public function incr($key);

	/**
	 * Increments the number stored at key by increment. If the key does not exist,
	 * it is set to 0 before performing the operation.
	 *
	 * @param string $key
	 * @param int    $increment
	 *
	 * @return int
	 */
	public function incrby($key, $increment);

	/**
	 * Decrements the number stored at key by one. If the key does not exist, it is
	 * set to 0 before performing the operation.
	 *
	 * @param string $key
	 *
	 * @return int
	 */
	public function decr($key);

	/**
	 * Decrements the number stored at key by decrement. If the key does not exist,
	 * it is set to 0 before performing the operation.
	 *
	 * @param string $key
	 * @param int    $decrement
	 *
	 * @return int
	 */
	public function decrby($key, $decrement);

	/**
	 * Returns all keys matching pattern.
	 *
	 * @param string $pattern
	 *
	 * @return array
	 */
	public function keys($pattern);

	/**
	 * Incrementally iterate the keys space.
	 *
	 * @param int    $cursor
	 * @param string $pattern
	 * @param int    $count
	 *
	 * @return array
	 */
	public function scan($cursor, $pattern = '', $count = 0);

	/**
	 * Add the specified members to the set stored at key.
	 * Specified members that are already a member of this
	 * set are ignored. If key does not exist, a new set is
	 * created before adding the specified members.
	 *
	 * @param string      $key
	 * @param array|mixed $members
	 *
	 * @return int The number of elements that were added to the set
	 */
	public function sadd($key, $members);

	/**
	 * Returns the set cardinality (number of elements) of the set stored at key.
	 *
	 * @param string $key
	 *
	 * @return int
	 */
	public function scard($key);

	/**
	 * Returns if member is a member of the set stored at key.
	 *
	 * @param string $key
	 * @param string $member
	 *
	 * @return bool
	 */
	public function sismember($key, $member);

	/**
	 * Returns all the members of the set value stored at key.
	 *
	 * @param string $key
	 *
	 * @return array
	 */
	public function smembers($key);

	/**
	 * Remove the specified members from the set stored at key.
	 * Specified members that are not a member of this set are
	 * ignored. If key does not exist, it is treated as an empty
	 * set and this command returns 0.
	 *
	 * @param string $key
	 * @param array  $member
	 *
	 * @return int The number of members that were removed from the
	 *             set, not including non existing members
	 */
	public function srem($key, $member);

	/**
	 * Incrementally iterate Set elements.
	 *
	 * @param string $key
	 * @param int    $cursor
	 * @param string $pattern
	 * @param int    $count
	 *
	 * @return array
	 */
	public function sscan($key, $cursor, $pattern = '', $count = 0);
	
	/**
	 * Clear whole client data from DB
	 *
	 * @return bool
	 */
	public function clearAll();
}