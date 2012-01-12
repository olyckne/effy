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
		return $this->loadClass($this->modelPaths, $name);
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
	public function helper($name, $args = array()) {
		return $this->loadClass($this->helperPaths, $name, $args);
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
	public function loadClass($paths, $name, $args=null) {
		foreach($paths as $path) {
			$file = $path . $name . '.php';
			if(is_file($file)) {
				$class = new $name($args);

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

		$controller = ucfirst($controller);
		$ctrlActive = false;
		$classExists = class_exists($controller);
		$canUrl = new CanonicalUrl();

		// Step 1 - Controller class must exist
		if($classExists) {
			$rc = new ReflectionClass($controller);

			// Step 2 - The controller must inherit from superclass Controller
			if($rc->isSubclassOf('Controller')) {

				// Step 3 - The controller must implement the interface Active (otherwise we render as if the controller doesn't exist at all)
				if(!$rc->implementsInterface('active')) {
					Feedback::addError("Sorry! Couldn't find the page.");
					$ef->frontController('error', 'code404');
				} else {

					// Step 4 - The controller must have the method.
					if($rc->hasMethod($action)) {
						$ctrlObj = $rc->newInstance();
						$method = $rc->getMethod($action);

						// If method starts with '_', we render it if it doesn't exists at all
						if($method->name{0} == '_') {
							Feedback::addError("Couldn't find matching page!");
							$ef->frontController($controller, 'code404');
						} else {

							// Step 5 - Run the method, and we're done.
							try {
								$method->invokeArgs($ctrlObj, $args);
							} catch(ReflectionException $e) {
								Feedback::addError("Ooops! Something went wrong, try again!");
								$ef->frontController('error');
							}
						}
					}
					// Found controller, but the method searched for didn't exist, forward to the controllers 404 
					else {
						Feedback::addError("Couldn't find matching page. Action {$action} is missing.");
						$ef->frontController($controller, 'code404');
					}
				}
			} 
			// Controller didn't inherit from superclass Controller
			else {
				throw new Exception("Controller {$controller} does not inherit from the Controller superclass!");
				
			}
		} 

		// The controller class didn't exist, maybe it's a canonical url?
		elseif($url = $canUrl->checkUrl(trim($ef->req->query, '/'))) {
			$ef->req->can_url = trim($ef->req->query, '/');
			$canfile = BASE_PATH . "/$url";
			if(is_file($canfile)) {
				include($canfile);
			} else {
				$ef->req->forwardTo($url);
				$ef->frontController();
			}
		} 
		// The page just doesn't seem to exist, send to error controllers 404-page.
		else {
			Feedback::addError("Sorry! Couldn't find the page.");
			$ef->frontController('error', 'code404');
		}


/*				OBSOLETE?! */
/* 
		$ctrlExist		= isset($ef->cfg['controllers'][$controller]);
		$ctrlEnabled	= false;
		$className		= null;
		$classExists	= false;
		$canUrl			= new CanonicalUrl();

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
					//throw new Exception("Controller {$controller} does not have method {$action}");
					$feedback = "Couldn't find matching page. Action {$action} is missing.";
					Feedback::addError($feedback);
					$ef->frontController($controller, 'code404');
				}
			}
			else {
				throw new Exception("Controller {$controller} does not inherit from the Controller superclass");
			}
		}
		elseif($url = $canUrl->checkUrl(trim($ef->req->query, '/'))) {
			$canfile = BASE_PATH . "/$url";
			if(is_file($canfile)) {
				include($canfile);
			}
			else {
				$ef->req->forwardTo($url);
				$ef->frontController();
			}
		}

		else {
			Feedback::addError("Sorry! Couldn't find the page.");
			$ef->frontController('error', 'code404');
		}
		*/
	}
}