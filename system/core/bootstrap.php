<?php

/**
 * 	Defines
 */

 define('POWERED_BY', "Powered by &copy; <a href='https://github.com/olyckne/effy'>Effy</a>, a free and open source PHP based MVC inspired CMS framework.");

/**
 *  Enables autoload of class declarations
 */

function __autoload($className) {
	$files = array(
			SYS_PATH . "core/{$className}.php",
			SYS_PATH . "controllers/{$className}.php",
			SYS_PATH . "models/{$className}.php",
			SYS_PATH . "views/{$className}.php",
			SYS_PATH . "helpers/{$className}.php",
			APP_PATH . "controllers/{$className}.php",
			APP_PATH . "models/{$className}.php",
			APP_PATH . "views/{$className}.php"
		);

	foreach($files as $file) {
		if(is_file($file)) {
			require_once($file);
			break;
		}
	}
}

/**
 * 
 */

interface Singleton {
	public static function GetInstance();
}

/**
 * 
 */

/* interface Controller {
 	
 	public function index();
 }*/