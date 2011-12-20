<?php

/**
* 
*/
class Theme implements Singleton
{
	
	/**
	 * 	Variables
	 */	
	private static $instance = null;

	public $html;

	public $name;
	public $path;
	public $urlPath;
	
	public $grids = 24;
	/**
	 *  HTML Head stuff
	 */

	public $doctype;
	public $charset;
	public $pageLang = 'en';
	public $pageTitle;
	public $meta = array();
	public $favicon;
	public $styles = array();
	public $script = array();
	

	/**
	 *  HTML Header stuff
	 */

	 public $menu;

	/**
	  * 	HTML Footer stuff
	  */ 


	/**
	 * 	HTML main content
	 */

	 public $views = array();



	protected function __construct($name = 'default')
	{
		global $ef;
		
		$this->html = new html();
		$this->themeToUse = $name;
		$this->getPaths();
		
		$this->styles = array(
			'main' => $this->urlPath . '/style.css'
			);

		$ef->cfg['theme']['charset'] = 'utf-8';

		$ef->cfg['theme']['meta'] = array(
				'charset'		=> $ef->cfg['theme']['charset'],
				'keyword'		=> 'framework, php',
				'description'	=> 'blabla',
				'author'		=> 'Mattias Lyckne, hello@mattiaslyckne.se',
				'copyright'		=> 'Copyright 2011'
			);
		
		foreach($ef->cfg['theme'] as $key => $value) {
			$this->$key = $value;
		}

		$this->menu = array(
				'Start'		=> '#',
			);

		$this->views['menu'] = $this->menu;
	}

	public static function GetInstance() {
		if(self::$instance == null) {
			$obj = __CLASS__;
			self::$instance = new $obj();
		}
	
		return self::$instance;
	}


	public function setTheme($name='') {
		$this->themeToUse = $name;

		$this->getPaths();
	}


	/**
	 * getPaths
	 * Finds the real and url path to the theme and puts in class variables
	 *
	 * @return void
	 * @author 
	 **/

	public function getPaths() {
		global $ef;

		$load = new Loader();

		$this->path = $load->theme($this->themeToUse);
		$this->urlPath = $ef->req->baseUrl . 'themes/' . $this->themeToUse;
	}


	/**
	 * addStyle
	 * Adds a stylesheet to the theme.
	 * @return void
	 * @author 
	 **/
	
	public function addStyle($name, $type='screen') {
		$filePath = $this->path . $name;
		if(is_file($filePath)) {
			$this->styles[$type] = $filePath;
		}
	}

	/**
	 * render
	 * Renders the page a.k.a includes the index.php file in the theme directory.
	 *
	 * @return void
	 * @author 
	 **/
	
	public function render($theme = null) {

		$this->includeFuncFile();

		if($theme != null) {
			$this->themeToUse = $theme;
		}
		
		extract(get_object_vars($this));
		include($this->path . 'index.php');
	}

	/**
	 * includeFuncFile
	 * If there is a functions.php in the theme directory include it.
	 * This runs before the page renders
	 *
	 * @return void
	 * @author 
	 **/
	
	public function includeFuncFile() {
		$funcFile = $this->path . '/functions.php';

		if(is_file($funcFile)) {
			include($funcFile);
		}
	}

	/**
	 * getHeader
	 * gets the header of the page a.k.a includes the header.php in the theme directory.
	 *
	 * @return void
	 * @author 
	 **/
	
	public function getHeader($header = '') {
		extract(get_object_vars($this));

		include($this->path . 'header.php');
	}

	/**
	 * getFooter
	 * gets the footer of the page a.k.a includes the footer.php in the theme directory
	 *
	 * @return void
	 * @author 
	 **/
	

	public function getFooter($footer = '') {
		extract(get_object_vars($this));

		include($this->path . 'footer.php');
	}

	/**
	 * getDocType
	 * returns the doctype of the page, loads it from a helper html-class.
	 *
	 * @return html code
	 * @author 
	 **/
	public function getDocType($doctype = '') {
		if(!empty($doctype)) $this->doctype = $doctype;
		return $this->html->getDocType($this->doctype, $this->pageLang, $this->charset);
	}

	/**
	 * getMeta
	 * returns the meta
	 *
	 * @return meta options for the html
	 * @author 
	 **/
	
	public function getMeta() {
		global $ef;
		return $ef->cfg['theme']['meta'];
	}

	/**
	 * getStyles
	 * returns the style options
	 *
	 * @return the styles
	 * @author 
	 **/	
	public function getStyles() {
		global $ef;
		return $ef->cfg['theme']['styles'];

	}


	/**
	 *  View related stuff
	 */

	/**
	 * addView
	 * adds a view in a region
	 *
	 * @return void
	 * @author 
	 **/
	public function addView($data, $region = 'content') {
		$this->views[$region][] = $data;
	}

	/**
	 * viewExist
	 * returns true or false for a view
	 *
	 * @return void
	 * @author 
	 **/

	public function viewExist($region) {
		return isset($this->views[$region]);
	}
	

	/**
	 *  Render html stuff
	 */

	 /**
	  * renderMenu function
	  * Renders the menu as html
	  * @return void
	  * @author 
	  **/
	 
	 public function renderMenu() {
	 	$html = "<nav id='ef-menu'><ul>";
		foreach($this->views['menu'] as $text => $link) {
	 		$html .= "<li class='ef-menu-item'><a href='{$link}'>{$text}</a></li>";
	 	}

	 	$html .= "</ul></nav>";

		echo $html;
	 }


	 /**
	  * renderView
	  * Renders a view
	  *
	  * @return void
	  * @author 
	  **/
	
	 public function renderView($region) {
	 	if(isset($this->views[$region]) && is_array($this->views[$region])) {
		 	foreach ($this->views[$region] as $key => $value) {
		 		if(is_array($value)) {
	 				foreach($value as $val) {
	 					echo $val;
	 				}
	 			}
	 			else {
	 				echo $value;
	 			}
	 		}
	 	}
	 }
}