<?php

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
	public $cfg;

	/**
	 * Holds the Request-object
	 */
	public $req;

	/**
	 * 	Holds the Loader-object
	 */
	public $load;
	
	/**
	 * 	Holds the theme-object
	 */
	public $theme;

	/**
	 *   Holds the feedback session name
	 */

	const feedbackSessionName = 'ef-feedback';
	
	protected function __construct()
	{

		// Reference this to ef just to use the same variable everywhere
		$ef = &$this;

		// set default exception handler
		set_exception_handler(array($this, 'DefaultExceptionHandler'));

		$configFile = APP_PATH . 'config.php';
		if(is_file($configFile)) {
			ob_start();//Hook output buffer 
			include($configFile);
			ob_end_clean();//Clear output buffer
		}

		// Set environment, defined by ENVIRONMENT constant in config.
		$this->setEnvironment();

		date_default_timezone_set($ef->cfg['general']['timezone']);	

		// Start a named session
		session_name($this->cfg['session']['name']);
		session_start();

		$ef->cfg['controllers']['error'] = array('enabled' => true, 'class' => 'CtrlError');

		// Init the request
		$this->req = new Request();
		$this->req->init($this->cfg['general']['base_url']);

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
  			" at line " . $aException->getLine() . 
  			"<p>Uncaught exception: " . $aException->getMessage() . 
  			"<pre>" . print_r($aException->getTrace(), true) . "</pre>");
 	}

	public function frontController($controller=null, $action=null) {
		$controller = isset($controller) ? $controller : $this->req->controller;
		$action 	= isset($action) ? $action : $this->req->action;
		$args		= $this->req->args;

		$this->load = new Loader();
		$this->load->controller($controller, $action, $args);

	}

	public function templateEngine() {
		if($this->theme == null) {
			$this->theme = Theme::GetInstance();
		}
		$this->theme->render($this->cfg['theme']['name']);
	}

	public function setEnvironment() {
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
	}



	/**
	 *  	Feedback stuff
	 */


	 /**
	  * addFeedback
	  * add a feedback
	  *
	  * @return void
	  * @author 
	  **/
	 public function addFeedback($feedback) {
	 	if(isset($_SESSION[self::feedbackSessionName])) {
	 		$_SESSION[self::feedbackSessionName] = array();
	 	}

	 	$_SESSION[self::feedbackSessionName][] = $feedback;
	 }

	 /**
	  * addFeedbackError function
	  * Adds a feedback of type error
	  *
	  * @return void
	  * @author 
	  **/
	 public function addFeedbackError($error) {
	 	$this->addFeedback( array('class' => 'error', 'message' => $error));
	 }

	 /**
	  * addFeedbackSuccess
	  * Adds a feedback of type success
	  *	
	  * @return void
	  * @author 
	  **/
	 public function addFeedbackSuccess($success) {
	 	$this->addFeedback( array('class' => 'success', 'message' => $success));
	 }


}