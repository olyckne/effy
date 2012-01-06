<?php

/**
* THE MASTER class, singleton
*/

// namespace system/core;

class Effy implements Singleton, useSQL {
	
	/**
 	*  Variables
 	*/
	
	private static $instance = null;

	/**
	 * Holds all config stuff
	 */
	public $cfg;

	/**
	 * Holds the database object
	 */

	 public $db;

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

		$this->load = new Loader();
		
		$configFile = APP_PATH . 'config.php';
		if(is_file($configFile)) {
			ob_start();//Hook output buffer 
			include($configFile);
			ob_end_clean();//Clear output buffer
		}
		else {
			session_start();
			$_SESSION['APP_PATH'] = APP_PATH;
			header('Location: setup.php' );
		}

		// Load config from database
		extract($ef->cfg['db']);
		$this->db = new Database($ef->cfg['db']);
		
		$cfg = $this->db->executeAndFetchAll($this->SQL('load ef::config'));

		$this->cfg['config-db'] = unserialize($cfg[0]['ef_value']);

		// Set environment, defined by ENVIRONMENT constant in config.
		$this->setEnvironment();

		date_default_timezone_set($ef->cfg['config-db']['general']['timezone']);	

		// Start a named session
		session_name($this->cfg['session']['name']);
		session_start();


		$ef->cfg['controllers'] =  array(
			'error' 	=> array('enabled' => true, 'class' => 'CtrlError'),
			'admin' 	=> array('enabled' => true, 'class' => 'CtrlAdmin'),
			'content'	=> array('enabled' => true, 'class' => 'CtrlContent'),
			'user'		=> array('enabled' => true, 'class' => 'CtrlUser'),
			);
		

		$indexController = isset($this->cfg['config-db']['index_controller']) ? $this->cfg['config-db']['index_controller'] : 'index';
		// Init the request
		$this->req = new Request();
		$this->req->init($this->cfg['config-db']['general']['siteurl'], $indexController);
		$ef->cfg['general']['base_url'] = $this->req->baseUrl;
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

		$this->theme->render($this->cfg['config-db']['theme']['name']);
	}

	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	public function SQL($id = null) {
		$db_prefix = $this->cfg['db']['db_prefix'];

		$query = array(
				'create ef::config' => "CREATE TABLE IF NOT EXISTS {$db_prefix}Effy(ef_module VARCHAR(255),ef_key VARCHAR(255), ef_value TEXT, PRIMARY KEY(ef_module, ef_key))ENGINE=InnoDB;
					INSERT INTO {$db_prefix}Effy(ef_module, ef_key, ef_value)VALUES('effy', 'config', ?);",
				'save ef::config' => "UPDATE {$db_prefix}Effy SET `ef_value` = ? WHERE `ef_module` = 'effy' AND `ef_key` = 'config';",
				'load ef::config' => "SELECT ef_value FROM {$db_prefix}Effy WHERE ef_module = 'effy' AND ef_key = 'config';",
			);

		if(!isset($query[$id])) {
	  		throw new Exception(t('#class error: Out of range. Query = @id', array('#class'=>get_class(), '@id'=>$id)));

		}
		return $query[$id];


	}

	/**
	 * saves the config-db to the database
	 *
	 * @return void
	 * @author 
	 **/
	public function saveConfig() {
		echo $this->db->executeQuery($this->SQL('save ef::config'), array(serialize($this->cfg['config-db'])));
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
	 	if(!isset($_SESSION[self::feedbackSessionName])) {
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