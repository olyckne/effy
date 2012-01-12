<?php


/**
*  html helper class.
*/
class Html
{
	
	function __construct()
	{
		# code...
	}

	/**
	 * getDoctype
	 * 
	 * Returns the html for the choicen doctype
	 *
	 * @return void
	 * @author 
	 **/
	public function getDoctype($doctype = 'html5', $pageLang = 'en', $pageCharset = 'UTF-8') {
		$html = "";


		switch ($doctype) {
			default:
			case 'html5':
				$html = <<<EOD
<!DOCTYPE html>
	<html lang="{$pageLang}">
EOD;
				break;
				
			case 'xhtml-strict':
				$html = <<<EOD
<?xml version='1.0' encoding='{$pageCharset}' ?>
<!DOCTYPE html
	PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{$pageLang}" lang="{$pageLang}">
EOD;
				break;

		}

		return $html;
	}


	/**
	 * getFeedback
	 *
	 * Returns all feedback (messages for errors, success, notice etc) as html
	 * 
	 * @return void
	 * @author 
	 **/
	public function getFeedback(){
		global $ef;
		$html = "";


		if(isset($_SESSION[Feedback::sessionName])) {
			foreach ($_SESSION[Feedback::sessionName] as $feedback) {
				$html .= "<p><output class='{$feedback['class']}'>{$feedback['message']}</output></p>";
			}

			unset($_SESSION[Feedback::sessionName]);
		}

		return $html;
	}
}