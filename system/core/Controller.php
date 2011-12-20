<?php

/**
* 
*/
class Controller implements Singleton
{
	
	private static $instance = null;
	protected $load;
	public $theme;

	
	function __construct()
	{
		$this->theme = Theme::GetInstance();
		$this->load = new Loader();
	}


	public static function GetInstance() {
		if(self::$instance == null) {
			$obj = __CLASS__;
			self::$instance = new $obj();
		}

		return self::$instance;
	}
}