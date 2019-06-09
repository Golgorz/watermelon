<?php

namespace core\controllers\cache;

use Predis\Client;

/**
 * Class RedisClient.
 *
 * php-redis implementation of redis client.
 */
final class RedisClient implements ClientInterface
{
	/**
	 * The redis instance.
	 *
	 * @var Client
	 */
	protected $redis;

	/**
	 * Constructor.
	 *
	 * @param array $config
	 */
	public function __construct() {
		$config = array (
					'scheme' => DB_REDIS_SCHEME,
					'host' => DB_REDIS_HOST,
					'port' => DB_REDIS_PORT,
					'database' => DB_REDIS_CACHE_DATABASE
			);
		
		if (DB_REDIS_CACHE_DATABASE_PSWD != "") {
			
			$config["password "] = DB_REDIS_CACHE_DATABASE_PSWD;
		}
		$this->redis = new Client($config);
	}

	/**
	 * Destructor.
	 */
	public function __destruct()
	{
		$this->quit();
	}

	/**
	 * {@inheritdoc}
	 */
	public function quit()
	{
		$this->redis->close();
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param string $key
	 *
	 * @return bool
	 */
	public function exists($key)
	{
		return $this->redis->exists($key);
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param string $key
	 * @param string $value
	 */
	public function set($key, $value)
	{
		$this->redis->set($key, $value);
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param string $key
	 * @param int    $seconds
	 * @param string $value
	 */
	public function setex($key, $seconds, $value)
	{
		$this->redis->setex($key, $seconds, $value);
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param string $key
	 *
	 * @return string|false
	 */
	public function get($key)
	{
		return $this->redis->get($key);
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param array|mixed $keys
	 *
	 * @return int
	 */
	public function del($keys)
	{
		return $this->redis->del($keys);
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param string $key
	 *
	 * @return int
	 */
	public function incr($key)
	{
		$value = $this->redis->incr($key);
		if ($value === false) {
			throw new \RuntimeException('Increased key is not an integer');
		}

		return $value;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param string $key
	 * @param int    $increment
	 *
	 * @return int
	 */
	public function incrby($key, $increment)
	{
		$value = $this->redis->incrBy($key, $increment);
		if ($value === false) {
			throw new \RuntimeException('Increased key is not an integer');
		}

		return $value;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param string $key
	 *
	 * @return int
	 */
	public function decr($key)
	{
		$value = $this->redis->decr($key);
		if ($value === false) {
			throw new \RuntimeException('Decreased key is not an integer');
		}

		return $value;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param string $key
	 * @param int    $decrement
	 *
	 * @return int
	 */
	public function decrby($key, $decrement)
	{
		$value = $this->redis->decrBy($key, $decrement);
		if ($value === false) {
			throw new \RuntimeException('Decreased key is not an integer');
		}

		return $value;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param string $pattern
	 *
	 * @return array
	 */
	public function keys($pattern)
	{
		return $this->redis->keys($pattern);
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param int    $cursor
	 * @param string $pattern
	 * @param int    $count
	 *
	 * @return array
	 */
	public function scan($cursor, $pattern = '', $count = 0)
	{
		if ($cursor == 0) {
			$cursor = null;
		}
		$this->redis->setOption(Client::OPT_SCAN, Client::SCAN_RETRY);
		$result = $this->redis->scan($cursor, $pattern, $count);
		if ($result == false) {
			$result = array();
		}

		return array($cursor, $result);
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param string      $key
	 * @param array|mixed $members
	 *
	 * @return int
	 */
	public function sadd($key, $members)
	{
		if (!is_array($members)) {
			return $this->redis->sAdd($key, $members);
		}
		switch (count($members)) {
			case 1:
				return $this->redis->sAdd($key, $members[0]);
			case 2:
				return $this->redis->sAdd($key, $members[0], $members[1]);
			case 3:
				return $this->redis->sAdd($key, $members[0], $members[1], $members[2]);
			default:
				array_unshift($members, $key);

				return call_user_func_array(array($this->redis, 'sAdd'), $members);
		}
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param string $key
	 *
	 * @return int
	 */
	public function scard($key)
	{
		return $this->redis->sCard($key);
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param string $key
	 * @param string $member
	 *
	 * @return bool
	 */
	public function sismember($key, $member)
	{
		return $this->redis->sIsMember($key, $member);
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param string $key
	 *
	 * @return array
	 */
	public function smembers($key)
	{
		return $this->redis->sMembers($key);
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param string      $key
	 * @param array|mixed $member
	 *
	 * @return int
	 */
	public function srem($key, $member)
	{
		if (!is_array($member)) {
			return $this->redis->sRem($key, $member);
		}
		switch (count($member)) {
			case 1:
				return $this->redis->sRem($key, $member[0]);
			case 2:
				return $this->redis->sRem($key, $member[0], $member[1]);
			case 3:
				return $this->redis->sRem($key, $member[0], $member[1], $member[2]);
			default:
				array_unshift($member, $key);

				return call_user_func_array(array($this->redis, 'sRem'), $member);
		}
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param string $key
	 * @param int    $cursor
	 * @param string $pattern
	 * @param int    $count
	 *
	 * @return array
	 */
	public function sscan($key, $cursor, $pattern = '', $count = 0)
	{
		$result = $this->redis->sScan($key, $cursor, $pattern, $count);
		if ($result == false) {
			$result = array(
					0,
					array(),
			);
		} else {
			$result = array(
					$cursor,
					array($result),
			);
		}

		return $result;
	}
	
	public function clearAll() {
		try {
			$this->redis->flushdb();
			return true;
		} catch (\RuntimeException $exception) {
			return false;
		}
		
	}
}