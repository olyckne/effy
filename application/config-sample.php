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
 *  Database stuff, you can use either SQLite or mySQL
 */

// SQLite
$ef->cfg['db']['dsn'] = '{DSN}';
//$ef->cfg['db']['dsn'] = 'sqlite:' . DATA_PATH . '/.htdb.sqlite';

// mySQL
//$host = '127.0.0.1'; $port = 3306; $dbname = 'effy';
//$ef->cfg['db']['dsn'] = "mysql:host=$host;port=$port;dbname=$dbname";

$ef->cfg['db']['username'] = '{USERNAME}';
$ef->cfg['db']['password'] = '{PASSWORD}';
$ef->cfg['db']['driver_options'] = null;
$ef->cfg['db']['db_prefix'] = '{DBPREFIX}';

/**
 * 	session name
 */
$ef->cfg['session']['name'] = 'effy_' . preg_replace('/[:\.\/-_]/', '', $_SERVER["SERVER_NAME"]);