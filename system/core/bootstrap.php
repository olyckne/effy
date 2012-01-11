<?php

/**
 * 	Defines
 */

 define('POWERED_BY', "Powered by &copy; <a href='https://github.com/olyckne/effy'>Effy</a>, a free and open source PHP based MVC inspired CMS framework.");

/**
 *  Enables autoload of class declarations
 */

function autoload($className) {
	$className = ucfirst($className);

	$files = array(
			SYS_PATH . "core/{$className}.php",
			SYS_PATH . "controllers/Ctrl{$className}.php",
			SYS_PATH . "controllers/{$className}.php",
			SYS_PATH . "models/{$className}.php",
			SYS_PATH . "helpers/{$className}.php",
			SYS_PATH . "core/interfaces/{$className}.php",

			
			APP_PATH . "controllers/Ctrl{$className}.php",
			APP_PATH . "controllers/{$className}.php",
			APP_PATH . "models/{$className}.php",
		);

	foreach($files as $file) {
		if(is_file($file)) {
			require_once($file);
			break;
		}
	}
}

spl_autoload_register('autoload');
