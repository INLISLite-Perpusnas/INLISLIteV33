<?php

namespace App\Libraries;

use Predis\Client;

class Cache
{
	private $host;
	private $port;
	private $lifetime;
	private $uri;
	private $client;

	public function __construct()
	{
		log_message('info', 'Redis Initialized');
		$this->host = getenv('redis.host');
		$this->port = getenv('redis.port');
		$this->lifetime = (int) getenv('redis.lifetime');
		$this->uri = "tcp://$this->host:$this->port";
		$this->client = new \Predis\Client($this->uri);
	}

	public function get($key)
	{
		log_message('info', "[cache] : get  '{$key}' succesfully");
		return $this->client->get($key);
	}

	public function set($key, $value, $ttl = false)
	{
		if (!$ttl) $ttl = $this->lifetime;
		$this->client->set($key, $value);
		$this->client->expire($key, $ttl);

		log_message('info', "[cache] : set expired '{$key}' in '{$ttl}' ");
		return true;
	}

	public function upsert($key, $value)
	{

		if ($this->exist($key)) {
			$this->del($key);
			log_message('info', "[cache] : del  '{$key}' exist");
		}

		$this->set($key, $value);

		log_message('info', "[cache] : upsert  '{$key}' succesfully");
		return true;
	}

	public function expire($key, $ttl)
	{
		$this->client->expire($key, $ttl);

		log_message('info', "[cache] : set expired '{$key}' in '{$ttl}' ");
		return true;
	}

	public function ttl($key)
	{
		$ttl = $this->client->ttl($key);

		log_message('info', "[cache] : ttl key '{$key}' in '{$ttl}'  seconds");
		return $ttl;
	}

	public function exists($key)
	{
		$exists = $this->client->exists($key);
		if ($exists) {
			log_message('info', "[cache] : key  '{$key}' is exist");
		} else {
			log_message('info', "[cache] : key  '{$key}' is not exist");
		}

		return $exists;
	}

	public function del($key)
	{
		$this->client->del($key);

		log_message('info', "[cache] : del  '{$key}' succesfully");
		return true;
	}

	public function keys()
	{
		log_message('info', "[cache] : get  all keys");
		return $this->client->keys('*');
	}

	public function flushAll()
	{
		$this->client->flushAll();

		log_message('info', "[cache] : flushAll keys succesfully");
		return true;
	}
}
