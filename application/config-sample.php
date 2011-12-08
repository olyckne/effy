<?php


/**
 * 	Set environment state of application.
 *	
 *	development = debugging and error reporting is on
 *	production = debbugging and error reporting is off
 */

define('ENVIRONMENT', 'development');
//define(ENVIRONMENT, 'production');

/**
 *  Define path to a WRITABLE directory, for storage sqlite, uploaded files etc. 
 */

define('DATA_PATH', BASE_PATH . "/data");


/**
 * 	Define the sites url (you want to do that if it's other than default), set it to null to let $ef figure it out
 */

$ef->config['general']['base_url'] = null;
// $ef->config['general']['base_url'] = 'http://example.com/effy';


/**
 *  Database stuff, you can use either SQLite or mySQL
 */

// SQLite
$ef->config['db']['dsn'] = 'sqlite:' . DATA_PATH . '/.htdb.sqlite';

// mySQL
//$ef->config['db']['dsn'] = 'mysql:host=localhost;port=3306;dbname=effy';

$ef->config['db']['username'] = null;
$ef->config['db']['password'] = null;
$ef->config['db']['driver_options'] = null;
$ef->config['db']['db_prefix'] = 'effy_';

/**
 * 	The controller used as index
 */

 $ef->config['general']['standard_controller'] = 'welcome';
 
/**
 * 	session name
 */
$ef->config['session']['name'] = 'effy_' . $_SERVER['SERVER_NAME'];

/**
 *  Set internal character encoding to UTF-8
 */
$ef->config['general']['character_encoding'] = 'UTF-8';