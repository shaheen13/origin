<?php

$GLOBALS['config'] = array(
	'mysql' => array(
		'host' => '127.0.0.1',
		'username' => 'root',
		'password' => 'root',
		'db' => 'oop'
		),
	'remember' => array(			// For Remember me functionality
		'cookie_name' => 'hash',
		'cookie_expiry' => 30*24*3600
		),
	'session' =>array(
		'session_name' => 'user',
		'token_name' => 'token'
		)
	);

class Config {
	public static function get($path = NULL)
	{
		if($path)
		{
			$path = explode('/', $path);

			$config = $GLOBALS['config'];
			foreach($path as $bit)
			{
				if(isset($config[$bit])) $config = $config[$bit]; else return false;
			}

			return $config;
		}

		return false;
	}
}