<?php


/**
* 
*/
class CtrlUser extends Controller
{
	
	
	/**
	 * Every controllers must have an index-function
	 *
	 * @return void
	 * @author 
	 **/
	public function index() {
		$this->theme->addView('USER!');
	}
}