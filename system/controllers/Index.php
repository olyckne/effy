<?php

/**
* Basic index controller
*/
class Index extends Controller implements active
{


	public function index() {

		$mainMenu = array('callback' => 'modifyMainMenu', 'list' => true, 'id'=>null,'class'=>null,
				'items' => array(
					'index' => array(
								'title' => 'home', 
								'url' 	=> 'home',
								'class' => 'ef-menu-item',
								),
					'about' => array(
								'title' => 'about',
								'url' 	=> 'about',
								'class' => 'ef-menu-item',
								),
					'help'	=> array(
								'title' => 'help',
								'url'	=> 'http://mattiaslyckne.se',
								'class' => 'ef-menu-item',
								),

					)
				);

		$this->theme->pageTitle = "EFFY!";
	//	$this->theme->mainmenu = $mainMenu;

	}
}