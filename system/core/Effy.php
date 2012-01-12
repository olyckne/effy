<?php

/**
* THE MASTER class, singleton
*/

// namespace system/core;

class Effy implements Singleton, SQL {
	
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
	 *   Holds session stuff
	 */

	public $sessionName;
	public $sessionId;

	

	protected function __construct()
	{
		// Reference this to ef just to use the same variable everywhere
		$ef = &$this;

		$configFile = APP_PATH . 'config.php';
		if(is_file($configFile)) {
//			ob_start();//Hook output buffer 
			include($configFile);
//			ob_end_clean();//Clear output buffer
		}
		else {
//			session_start();
//			$_SESSION['APP_PATH'] = APP_PATH;
			header('Location: setup.php' );
			exit;
		}

		// Start a named session
		session_name($ef->cfg['session']['name']);
		session_start();
		$this->sessionName = session_name();
		$this->sessionId = session_id();


		// set default exception handler
		set_exception_handler(array($this, 'DefaultExceptionHandler'));
		
		$this->load = new Loader();

		// Load config from database
		extract($ef->cfg['db']);
		$this->db = new Database($ef->cfg['db']);
		
		$cfg = $this->db->executeAndFetchAll($this->SQL('load ef::config', $ef->cfg['db']['db_prefix']));

		$this->cfg['config-db'] = unserialize($cfg[0]['ef_value']);

		// Set environment, defined by ENVIRONMENT constant in config.
		$this->setEnvironment();

		date_default_timezone_set($ef->cfg['config-db']['general']['timezone']);	



/*		$ef->cfg['controllers'] =  array(
			'index' => array('enabled' => true, 'class' => 'CtrlIndex'),
			'error' => array('enabled' => true, 'class' => 'CtrlError'),
			'admin' => array('enabled' => true, 'class' => 'CtrlAdmin'),
			'page'  => array('enabled' => true, 'class' => 'CtrlContent'),
			'user'  => array('enabled' => true, 'class' => 'CtrlUser'),
			'canurl'=> array('enabled' => true, 'class' => 'CtrlCanonical'),
			);*/

		$indexController = isset($this->cfg['config-db']['general']['standard_controller']) ? $this->cfg['config-db']['general']['standard_controller'] : 'index';
		// Init the request
		$this->req = new Request($this->cfg['config-db']['general']['siteurl'], $indexController);
		$this->req->init();
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
	public static function SQL($id = null, $prefix = null) {
		global $ef;

		$db_prefix = isset($ef->cfg['db']['db_prefix']) ? $ef->cfg['db']['db_prefix'] : $prefix;

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
				error_reporting(E_ALL);
				ini_set('display_errors', 1);
				ini_set('log_errors', 1);
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
	  * Destroys and restarts the session
	  *
	  * @return void
	  * @author 
	  **/
	 public function destroyAndRestartsession() {
	 	$_SESSION = array();
	 	if(ini_get("session.use_cookies")) {
	 		$params = session_get_cookie_params();
	 		setcookie(session_name(), '', time() -42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
	 	}

	 	session_destroy();
	 	session_name($this->cfg['session']['name']);
	 	session_start();
	 	session_regenerate_id();
	 	$this->sessionId = session_id();
	 }

}