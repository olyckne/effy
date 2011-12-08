<?php

if(!is_file(APP_PATH . 'config.php')) {
	header('Location: setup.php');
	exit;
}

require_once(APP_PATH . 'config.php');

switch (ENVIRONMENT) {
	case 'development':
		ini_set('display_errors', 1);
		ini_set('log_errors', 1);
		error_reporting(E_ALL | E_NOTICE);
		break;

	case 'production':
		ini_set('display_errors', 0);
		ini_set('log_errors', 0);
		error_reporting(0);
		break;

	default:
			die('You have to set ENVIRONMENT variable');
			break;
}

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
//	$file1 = SYS_PATH . "core/{$className}.php";
//	$file2 = APP_PATH . "controllers/{$className}.php";
//	$file3 = SYS_PATH . "controllers/{$className}.php";
	
	foreach($files as $file) {
		if(is_file($file)) {
			require_once($file);
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