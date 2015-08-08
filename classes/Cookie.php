<?php

class Cookie {

	public static function exists($name)
	{
		return isset($_COOKIE["$name"]);
	}

	public static function put($name, $value = '', $expiry = 0, $path = '/')
	{
		$expiry = ($expiry !== 0)? time() + $expiry : 0;
		return setcookie($name, $value, $expiry, $path);
	}

	public static function get($name)
	{
		return $_COOKIE["$name"];
	}

	public static function delete($name, $expiry, $path = '/')
	{
		setcookie($name, '', time() - $expiry, $path);
	}
}