<?php

/**
* 	Class for loading views, files, libraries etc
*/

//namespace system/core;

class Loader
{
	
	protected $modelPaths	= array();
	protected $viewPaths	= array();
	protected $helperPaths 	= array();
	protected $themePaths	= array();

	function __construct()
	{
		$this->modelPaths = array(APP_PATH . 'models/',
								  SYS_PATH . 'models/');
		$this->viewPaths = array(APP_PATH . 'views/',
								 SYS_PATH . 'views/');
		
		$this->helperPaths = array(APP_PATH . 'helpers/', 
									SYS_PATH . 'helpers/');

		$this->themePaths = array(BASE_PATH . '/themes/'
								  );
	}


	/**
	 * 	Load model, it just calls the loadClass-method.
	 */
	public function model($name) {
		$this->loadClass($this->modelPaths, $name);
	}

	/**
	 * 	Load view
	 */
	public function view($name, $vars = array(), $return = FALSE) {
		$path = BASE_PATH . 'themes/' . $name . '/index.php';

		if(is_file($path)) {
			include($path);
		}

	}

	/**
	 * 	Load helper, it just calls the loadClass-method
	 */
	public function helper($name) {
		$this->loadClass($this->helperPaths, $name);
	}

	/**
	 *  Find theme
	 */

	 public function theme($name) {
	 	foreach ($this->themePaths as $path) {
	 		$themePath = $path . $name .'/';
	 		$indexFile = $themePath . 'index.php';
	 		if(is_file($indexFile)) {
	 			return $themePath;

	 			break;
	 		}
	 	}
	 }

	/**
	 * 		Searches for the class, and if it finds it, create an object of the class and returns it.
	 */
	public function loadClass($paths, $name) {
		foreach($paths as $path) {
			$file = $path . $name . '.php';
			if(is_file($file)) {
				$class = new $name();

				return $class;
				break;
			}
		}
	}

	/**
	 * 		Loads a controller, well, if it exists, inherits from Controller superclass and has the method the user asks for.
	 */
	public function controller($controller, $action, $args) {
		global $ef;

		$ctrlExist		= isset($ef->cfg['controllers'][$controller]);
		$ctrlEnabled	= false;
		$className		= null;
		$classExists	= false;

		if($ctrlExist) {
			$ctrlEnabled 	= $ef->cfg['controllers'][$controller]['enabled'];
			$className 		= $ef->cfg['controllers'][$controller]['class'];
			$classExists 	= class_exists($className);
		}

		if($ctrlEnabled && $classExists) {

			$rc = new ReflectionClass($className);

			if($rc->isSubclassOf('Controller')) {
				if($rc->hasMethod($action)) {
					$ctrlObj = $rc->newInstance();
					$method = $rc->getMethod($action);
					$method->invokeArgs($ctrlObj, $args);
				}
				else {
					throw new Exception("Controller {$controller} does not have method {$action}");
				}
			}
			else {
				throw new Exception("Controller {$controller} does not inherit from the Controller superclass");
			}
		}
		else {
			$feedback = array('class' => 'error', 'message' => "Couldn't find a matching page. Sorry!");
			$ef->addFeedback($feedback);
			$ef->frontController('error', 'code404');
		}
	}
}