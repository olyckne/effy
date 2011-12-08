<?php

/**
* 
*/
class Controller implements Singleton
{
	
	private static $instance = null;
	protected $load;
	function __construct()
	{
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