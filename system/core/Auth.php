<?php

/**
* 
*/
class Auth
{
	
	function __construct() {
		
	}


	/**
	 * Checks if user is authenticated
	 *
	 * @return void
	 * @author 
	 **/
	public static function isAuthenticated() {
		global $ef;

		$user = User_model::GetInstance();

		return $user->isAuthenticated();
	}


	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	public static function LoginAndRedirect($to=null) {
		global $ef;

		$to = isset($to) ? $to : $ef->req->getControllerActionParams();

		$user = User_model::GetInstance();


		if(!$user->isAuthenticated()) {
			$ef->req->redirectBackTo($to);
			$ef->req->redirectTo('user', 'login', 'andRedirectBack');
		}
	}
}