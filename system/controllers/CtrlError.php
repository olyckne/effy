<?php


/**
* 
*/
class CtrlError extends Controller
{
	

	/**
	 * index
	 *
	 * @return void
	 * @author 
	 **/
	public function index($errorCode = '') {
		echo "hej! {$errorCode}";
	}


	/**
	 * code404 function
	 * Take care of 404
	 *
	 * @return void
	 * @author 
	 **/
	public function code404() {

		$feedback = $this->theme->html->getFeedback();
		$this->theme->addView($feedback);
	}
}