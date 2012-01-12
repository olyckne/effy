<?php

/**
* Handles HTTP requests.
*/

// namespace system/core;

class Request
{
	
	/**
	 * 	Variables
	 */

	public $supportCleanUrls;
	public $baseUrl;
	public $standardController;

	public $current;
	public $forwardedFrom;
	public $query;
	public $splits;

	public $controller;
	public $action;
	public $args;
	public $can_url = null;

	public $get;
	public $post;
	public $session;

	public function __construct($baseUrl = '', $standardController = 'index')
	{
		$this->baseUrl = $baseUrl;
		$this->current = null;
		$this->standardController = $standardController;
		$this->splitControllerActionParams($this->standardController);


	}


	public function init($modifiedBaseUrl = null) {

		$url = $this->getUrlToCurrentPage();

		$parts = parse_url($url);
		$script = $_SERVER['SCRIPT_NAME'];
		$dir = dirname($script);
		$this->query = isset($modifiedBaseUrl) && isset($_SERVER['REDIRECT_URL']) ? substr($_SERVER['REDIRECT_URL'], strlen($dir)) : substr($parts['path'], strlen($dir));

		if($this->query != '/')
			$this->getControllerAction();

		$this->get     = &$_GET;
		$this->post    = &$_POST;
		$this->session = &$_SESSION;

	}

	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	public function splitControllerActionParams($input) {
		$this->query = $input;
		$this->getControllerAction();
	}

	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	public function getControllerAction() {
		$this->splits = explode('/', trim($this->query, '/'));

		// If split is empty or equal to index.php we use $_GET['p']
		if(empty($this->splits[0]) || strcasecmp($this->splits[0], 'index.php') == 0) {
			if(isset($_GET['p'])) {
				$this->splits = explode('/', trim($_GET['p'], '/'));
			}
			else {
				$this->splits[0] = $this->standardController;
			}
		}


		/**
		 * 	Step 2.
		 *	Set controller, action and parameters
		 */

		$this->controller 	= !empty($this->splits[0]) ? $this->splits[0] : 'index';
		$this->action 		= !empty($this->splits[1]) ? $this->splits[1] : 'index';
		$this->args = $this->splits;
		unset($this->args[0]);
		unset($this->args[1]);
		// after unset first value will have index 2. This resets it to 0.
		$this->args = array_values($this->args);
	}

	public function getUrlToCurrentPage() {
		if(!isset($this->current)) {
			$this->current = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

			return $this->current;
		}
	}


	/**
	 * forward to url
	 *
	 * @return void
	 * @author 
	 **/
	public function forwardTo($url) {
		if(strpos($url, '?')) {
			$url = substr($url, 0, strpos($url, '?'));
			if($url[strlen($url)] != '/') {
				$url = dirname($url);
			}
		}

		$url = trim($url, '/');

		$this->forwardedFrom = $this->current;
		$this->current = $this->baseUrl . $url;
		$this->query = $url;
		$this->getControllerAction();
	}

	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	public function redirectTo($controller=null,$action=null,$params=null) {
		global $ef;

		$action = isset($action) ? '/'.$action : null; 
		$params = isset($params) ? '/'.$params : null;

		$url = $ef->cfg['config-db']['general']['siteurl'] . "{$controller}{$action}{$params}";

		header('Location: ' . $url);
		exit();
	}

	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	public function redirectBackTo($to) {
		$_SESSION['redirectTo'] = $to;	
	}


	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	public function createUrlToControllerAction($controller=null, $action=null) {
		$controller = isset($controller) ? $controller 	: $this->controller;
		$action 	= isset($action)	 ? $action 		: $this->action;

		$args = null;
		$numOfArgs = func_num_args();
		if($numOfArgs > 2) {
			for($i = 2; $i < $numOfArgs; $i++) {
				$args .= '/' . func_get_args($i);
			}

		}


		return $this->baseUrl . $controller . $action . $args;
	}


	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	public function getControllerActionParams() {
		$url = $this->controller . '/' . $this->action;

		foreach ($this->args as $arg) {
			$url .= '/'.$arg;
		}

		return $url;
	}
}