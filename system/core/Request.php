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
	
	public $current;
	public $controller;
	public $action;
	public $args;
	public $params;

	 
	public function __construct($cleanUrls = false)
	{
		$this->current = null;
		$this->supportCleanUrls = $cleanUrls;
	}


	public function init($modifiedBaseUrl = null) {
		$url = $this->getUrlToCurrentPage();

		$parts = parse_url($url);
		$script = $_SERVER['SCRIPT_NAME'];
		$dir = dirname($script);
		$query = isset($modifiedBaseUrl) && isset($_SERVER['REDIRECT_URL']) ? substr($_SERVER['REDIRECT_URL'], strlen($dir)) : substr($parts['path'], strlen($dir));

		$splits = explode('/', trim($query, '/'));

		// If split is empty or equal to index.php we use $_GET['p']
		if(empty($splits[0]) || strcasecmp($splits[0], 'index.php') == 0) {
			if(isset($_GET['p'])) {
				$splits = explode('/', trim($_GET['p'], '/'));
			}
			else {
				$splits[0] = 'welcome';
			}
		}


		/**
		 * 	Step 2.
		 *	Set controller, action and parameters
		 */

		$this->controller 	= !empty($splits[0]) ? $splits[0] : 'index';
		$this->action 		= !empty($splits[1]) ? $splits[1] : 'index';
		$this->args = $this->params = array();

		if(!empty($splits[2])) {
			for($i = 2; $length = count($splits), $i < $length; $i+=2) {
				$this->params[$splits[$i]] = !empty($splits[$i+1]) ? $splits[$i+1] : null;
				$this->args[] = $splits[$i];
				if(!empty($splits[$i+1])) $this->args[] = $splits[$i+1];

			} 

		}

	}


	public function getUrlToCurrentPage() {
		if(!isset($this->current)) {
			$this->current = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

			return $this->current;
		}
	}
}