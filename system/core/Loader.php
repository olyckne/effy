<?php

/**
* 	Class for loading views, files, libraries etc
*/

//namespace system/core;

class Loader
{
	
	protected $modelPaths	= array();
	protected $viewPaths	= array();
	protected $helperPaths 	= array();

	function __construct()
	{
		$this->modelPaths = array(
								APP_PATH . 'models/',
								SYS_PATH . 'models/'
							);
		$this->viewPaths = array(
								APP_PATH . 'views/',
								SYS_PATH . 'views/'
							);
		
		$this->helperPaths = array(
								APP_PATH . 'helpers/',
								SYS_PATH . 'helpers/'	
								);
	}


	public function model($name) {
		foreach($this->modelPaths as $path) {
			$file = $path . $name . '.php';
			if(is_file($file)) {
				$model = new $name();
				return $model;
				break;
			}
		}

	}

	public function view($name, $vars = array(), $return = FALSE) {
		

	}

	public function helper($name) {
		foreach($this->helperPaths as $path) {
			$file = $path . $name . '.php';
			if(is_file($file)) {
				$helper = new $name();
				return $helper;
				break;
			}
		}
	}
}