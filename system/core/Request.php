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
	public $controller;
	public $action;
	public $args;

	 
	public function __construct($cleanUrls = false, $standardController = 'index')
	{
		$this->current = null;
		$this->supportCleanUrls = $cleanUrls;
		$this->standardController = $standardController;
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
				$splits[0] = $this->standardController;
			}
		}


		/**
		 * 	Step 2.
		 *	Set controller, action and parameters
		 */

		$this->controller 	= !empty($splits[0]) ? $splits[0] : 'index';
		$this->action 		= !empty($splits[1]) ? $splits[1] : 'index';
		$this->args = $splits;
		unset($this->args[0]);
		unset($this->args[1]);
		// after unset first value will have index 2. This resets it to 0.
		$this->args = array_values($this->args);

		$this->baseUrl = "{$parts['scheme']}://{$parts['host']}" . (isset($parts['port']) ? ":{$parts['port']}" : "") . "{$dir}" . "/";
	}


	public function getUrlToCurrentPage() {
		if(!isset($this->current)) {
			$this->current = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

			return $this->current;
		}
	}


	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	public function redirectTo($controller=null,$action=null,$params=null) {
		global $ef;

		$url = $ef->cfg['config-db']['general']['siteurl'] . "/{$controller}/{$action}/";

		header('Location: ' . $url);

	}
}