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
	public $real_path;
	public $url_path;
	public $site_url;

	public $grids = 24;
	/**
	 *  HTML Head stuff
	 */
	
	public $siteTitle;
	public $doctype;
	public $charset;
	public $pageLang = 'en';
	public $pageTitle;
	public $meta = array();
	public $favicon;
	public $styles = array();
	public $scripts = array();
	

	/**
	 *  HTML Header stuff
	 */

	 public $mainmenu = array();

	/**
	  * 	HTML Footer stuff
	  */ 


	/**
	 * 	HTML main content
	 */

	 public $views = array();



	protected function __construct()
	{
		global $ef;
		
		$this->html = new html();
		$this->themeToUse = $ef->cfg['config-db']['theme']['name'];
		$this->getPaths();
		
		$this->styles = array(
			'main' => $this->url_path . '/style.css'
			);

		$ef->cfg['theme']['charset'] = 'utf-8';
		
		foreach($ef->cfg['theme'] as $key => $value) {
			$this->$key = $value;
		}

		$this->siteTitle = $ef->cfg['config-db']['general']['sitetitle'];

		$this->meta = array(
				'keywords' => '',
				'descrπiption' => '',
				'author'	=> "{$ef->cfg['config-db']['general']['owner_name']}, {$ef->cfg['config-db']['general']['owner_mail']}",
				'copyright' => "Copyright, 2011, {$ef->cfg['config-db']['general']['owner_name']}",
			);
	}

	public static function GetInstance() {
		if(self::$instance == null) {
			$obj = __CLASS__;
			self::$instance = new $obj();
		}
	
		return self::$instance;
	}


	public function setTheme($name='') {
		global $ef;

		$ef->cfg['config-db']['theme']['name'] = $name;
		$this->themeToUse = $name;

		$this->getPaths();

		$ef->saveConfig();
	}


	/**
	 * getPaths
	 * Finds the real and url real_path to the theme and puts in class variables
	 *
	 * @return void
	 * @author 
	 **/

	public function getPaths() {
		global $ef;

		$load = new Loader();

		$this->real_path = $load->theme($this->themeToUse);
		$this->url_path = $ef->req->baseUrl . 'themes/' . $this->themeToUse;
		$this->site_url = $ef->cfg['config-db']['general']['siteurl'];
		$this->styles['main'] = $this->url_path . '/style.css';
	}


	/**
	 * addStyle
	 * Adds a stylesheet to the theme.
	 * @return void
	 * @author 
	 **/
	
	public function addStyle($name, $type='screen') {
		$filePath = $this->real_path . $name;
		if(is_file($filePath)) {
			$this->styles[$type] = $filePath;
		}
	}

	/**
	 * addExternalStyle
	 *
	 * @return void
	 * @author 
	 **/
	public function addExternalStyle($name, $type='screen') {
		array_unshift($this->styles, $name);
	}

	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	public function addScript($script, $region = 'footer') {
		$scriptTag = "<script type='text/javascript' src='{$script}'></script>";

		$this->addView($scriptTag, $region);
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
		include($this->real_path . 'index.php');
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
		$funcFile = $this->real_path . '/functions.php';

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

		include($this->real_path . 'header.php');
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

		include($this->real_path . 'footer.php');
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
	 
	 public function renderMainMenu($menu = null) {

	 	function modifyMainMenu($items) {
		 	global $ef;
	 		$ref = $ef->req->action;
	 		$items[$ref]['class'] .= ' selected';
	 		
	 		return $items; 
	 	}

	 	$menu 	= isset($menu)			? $menu 		 : $this->mainmenu;
	 	$list	= isset($menu['list']) 	? $menu['list']	 : false;
	 	$id 	= isset($menu['id'])	? $menu['id'] . ' ef-mainmenu' 	 : 'ef-mainmenu';
	 	$class 	= isset($menu['class'])	? $menu['class'] : null;
	 	echo $this->generateMenu($menu, $list, $id, $class);
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


	 /**
	  * generateMenu function
	  * Generates a menu as html and returns it
	  *	
	  * @return void
	  * @author 
	  **/

	 public function generateMenu($menu, $list=false, $id=null, $class=null) {
	 	global $ef;
	 	if(isset($menu['callback'])) {
	 		$items = call_user_func($menu['callback'], $menu['items']);
	 	} else $items = isset($menu['items']) ? $menu['items'] : null;

	 	$list 	= isset($menu['list']) ? $menu['list'] : $list;
	 	$id 	= isset($id)	? "id='{$id}' " 		: null;
	 	$class 	= isset($class) ? "class='{$class}' " 	: null;



	 	$html = "<nav {$id}{$class}>";
	 	$noListClass = (!$list) ? "class='nolist' " : null;
	 	$html .= "<ul {$noListClass}>";

	 	if(isset($items)) {
		 	foreach ($items as $item) {

		 		$classes  = (!$list || (isset($item['class']))) ? "class='" : null;
		 		$classes .= (!$list) ? "nolist " : null;
		 		$classes .= isset($item['class']) ? "{$item['class']}" : null;
		 		$classes .= "' ";
		 		$html .= "<li {$classes}>";
		 		$html .= "<a href='{$item['url']}'>{$item['title']}</a> ";
		 		$html .= "</li>";
		 	}
		}
	 	$html .="</ul>";

	 	$html .= "</nav>";
	 	return $html;
	 }
}