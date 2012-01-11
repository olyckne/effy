<?php


/**
* Class for handling feedbacks (messages for alerts, notice, success etc)
*/
class Feedback
{
	
	const sessionName = 'ef-feedback';

	function __construct() {
		
	}


		/**
	 *  	Feedback stuff
	 */


	 /**
	  * addFeedback
	  * add a feedback
	  *
	  * @return void
	  * @author 
	  **/
	 public static function addFeedback($feedback) {
	 	if(!isset($_SESSION[self::sessionName])) {
	 		$_SESSION[self::sessionName] = array();
	 	}

	 	$_SESSION[self::sessionName][] = $feedback;
	 }

	 /**
	  * addFeedbackError function
	  * Adds a feedback of type error
	  *
	  * @return void
	  * @author 
	  **/
	 public static function addError($message) {
	 	self::addFeedback( array('class' => 'alert-message error', 'message' => $message));
	 }

	/**
	 * addFeedbackSuccess
	 * Adds a feedback of type success
	 *	
	 * @return void
	 * @author 
	**/
	public static function addSuccess($message) {
	 	self::addFeedback( array('class' => 'alert-message success', 'message' => $message));
	}

	/**
	 * addFeedbackNotice
	 * Adds a feedback of type notice
	 *
	 * @return void
	 * @author 
	 **/
	public static function addNotice($message) {
		self::addFeedback( array('class' => 'alert-message notice', 'message' => $message));
	}


	/**
	 * Returns all the feedbacks
	 *
	 * @return void
	 * @author 
	 **/
	public static function getFeedbacks($unset=true) {
		global $ef;

		$feedbacks = $_SESSION[self::sessionName];

		if($unset) {
			unset($_SESSION[self::sessionName]);
		}

		return $feedbacks;	
	}

	/**
	 * Returns all the feedbacks in html-format. It just returns the function form html helper class.
	 *
	 * @return void
	 * @author 
	 **/
	public static function getFeedbacksAsHTML() {
		$html = new html();
		return $html->getFeedback();
	}

}