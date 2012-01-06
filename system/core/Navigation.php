<?php

/**
* 	NAVIGATION
*	Create navigation menus
*/

// namespace system/core;

class Navigation
{
	
	function __construct()
	{
		# code...
	}

	/**
	 * generateMenu function
	 * Static function thats generates a menu as html and returns it
	 *
	 * @return void
	 * @author 
	 **/

	public static function generateMenu($menu, $id = null, $class = null) {

		$id 	= isset($id)	? "id='{$id}' " 		: null;
		$class 	= isset($class) ? "class='{$class}' " 	: null;
		
		$html = "<nav {$id}{$class}>";
		$html .= "<ul>";
		foreach ($menu['items'] as $item) {
			$class = isset($item['class']) ? "class='{$item['class']}' " : null;

			$html .= "<li><a href='{$item['url']}' {$class}>{$item['title']}</a></li>";
		}

		$html .= "</ul>";


		return $html;
	}
}