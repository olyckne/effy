<?php
/**************************************************************************************************************
**
** 
**
***************************************************************************************************************/



// ------------------------------ Setup -------------------------------------------

define('VERSION', '0.5');

define('BASE_PATH', dirname(__FILE__));

/**
 *   Change these if necessary
 */
define('APP_PATH', BASE_PATH . "/application/");
define('SYS_PATH', BASE_PATH . "/system/");

/**
 * include bootstrap
 */

include(SYS_PATH . 'core/bootstrap.php');
/**
 * Run
 */

$ef = Effy::GetInstance();

$ef->frontController();


$ef->templateEngine();