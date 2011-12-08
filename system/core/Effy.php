<?

/**
* THE MASTER class, singleton
*/

// namespace system/core;

class Effy implements Singleton {
	
	/**
 	*  Variables
 	*/
	
	private static $instance = null;

	/**
	 * Holds all config stuff
	 */
	public $config;

	/**
	 * Holds the Request-object
	 */
	public $req;


	protected function __construct()
	{
		// Reference this to ef just to use the same variable everywhere
		$ef = &$this;

		$configFile = APP_PATH . 'config.php';
		if(is_file($configFile)) {
			include($configFile);
		} else {
			die('The file ' . APP_PATH . 'config.php is missig!');
		}


		// Start a named session
		session_name($this->config['session']['name']);
		session_start();

		// set default exception handler
		set_exception_handler(array($this, 'DefaultExceptionHandler'));

		// Init the request
		$this->req = new Request();
		$this->req->init($this->config['general']['base_url']);
	}

	public static function GetInstance() {
		if(self::$instance == null) {
			$obj = __CLASS__;
			self::$instance = new $obj();
		}

		return self::$instance;
	}

	/**
	 * Create a common exception handler 
	 */
	public static function DefaultExceptionHandler($aException) {
		// CWatchdog to store logs
  		die("<h3>Exceptionhandler</h3><p>File " . $aException->getFile() . 
  			" at line" . $aException->getLine() . 
  			"<p>Uncaught exception: " . $aException->getMessage() . 
  			"<pre>" . print_r($aException->getTrace(), true) . "</pre>");
 	}

	public function frontController($controller=null, $action=null) {
		$controller = isset($controller) ? $controller : $this->req->controller;
		$action 	= isset($action) ? $action : $this->req->action;
		$args		= $this->req->args;

		$ctrlExists = class_exists($controller);

		if($ctrlExists) {
			$rc = new ReflectionClass($controller);
			if($rc->isSubclassOf('Controller')) {
				if($rc->hasMethod($action)) {
					$ctrlObj = $rc->newInstance();
					$method = $rc->getMethod($action);
					$method->invokeArgs($ctrlObj, $args);
				}
				else
					throw new Exception("Controller {$controller} does not have method {$action}");
			}
			else
				throw new Exception("Controller {$controller} does not implement the interface Controller");			
		}

		else {
			echo "NOT FOUND!";
			//$this->frontController('error', '404');
		}
	}

}