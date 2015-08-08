<?php
session_start();
session_regenerate_id(true);   // For security issues
date_default_timezone_set('Africa/Cairo');
error_reporting(E_ALL & ~ E_NOTICE);
// I have sent $GLOBAL['config'] to class Config file 'classes/Config.php' because it's related to it
// and the configs will be set if the file is required automatically by spl_autoload_register() function
// when the class Config is used Like Config::get() or $variable = new Config   ... 

spl_autoload_register(function ($class){
	require_once 'classes/' . $class . '.php';
});

require_once 'functions/sanitize.php';