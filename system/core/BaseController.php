<?php

/**
* 
*/
class BaseController
{
	
	private static $instance = null;
	protected $load;
	public $theme;
	public $url;
	public $action;

	public $requireLogin = false;

	function __construct()
	{
		global $ef;
		$this->theme = Theme::GetInstance();
		$this->load = new Loader();
		$this->url = $ef->req->baseUrl . $ef->req->controller . '/';
		$this->action = $ef->req->action;

		$this->theme->siteTitle;

	}

	/**
	 * Called when page not found, but the controller exists
	 *
	 * @return void
	 * @author 
	 **/
	public function code404() {
		global $ef;
		$ef->frontController('error', 'code404');
		
	}

}