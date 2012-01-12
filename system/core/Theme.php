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
		
		$this->html = new Html();
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
		$this->pageTitle = $this->siteTitle;

		$name = $ef->cfg['config-db']['general']['owner_firstname'] . ' ' . $ef->cfg['config-db']['general']['owner_lastname'];

		$this->meta = array(
				'keywords' => '',
				'description' => '',
				'author'	=> "{$name}, {$ef->cfg['config-db']['general']['owner_mail']}",
				'copyright' => "Copyright, 2011, {$name}",
			);

		$this->mainmenu = isset($ef->cfg['config-db']['theme']['mainmenu']) ? $ef->cfg['config-db']['theme']['mainmenu'] : null;

		$this->createLoginoutMenu();
	}

	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	public function createLoginoutMenu() {
		global $ef;
		$text = "";

		$url = $ef->req->baseUrl . 'user/';
		$currentUrl = str_replace('/', '+',$ef->req->getControllerActionParams());

		$menu = array('callback' => null, 'id' => 'ef-loginmenu',
					  'separator' => '| ');

		$loggedInAs = "";
		if(Auth::isAuthenticated()) {
			$user = User_model::GetInstance();
			$name = $user->userData['username'];
			$menu['items'] = array(
					$name => array(
							'title' => $name,
							'url'	=> $ef->req->baseUrl . 'admin/user',
							'class' => null
						),
					'Dashboard' => array(
							'title' => 'Dashboard',
							'url'	=> $ef->req->baseUrl . 'admin',
						),
					'Logout'	=> array(
							'title'	=> 'Logout',
							'url' 	=> $url . "logoutAndRedirectTo/{$currentUrl}",
						),
					);
		}
		else {
			$menu['items'] = array(
					'Login' => array(
							'title' => 'Login',
							'url'	=> $url . "login",
						),
					);
		}

		if(!isset($this->views['login']))
			$this->views['login'] = array();
		

		$html = $this->generateMenu($menu);
		array_unshift($this->views['login'], $html);

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
		$scriptTag = "<script type='text/javascript' src='{$script}'></script>\n";

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
		global $ef;

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
	 		$ref = $ef->req->controller;
	 		$ref2 = $ef->req->action;
	 		$ref3 = $ef->req->can_url;

	 		if(isset($items[$ref]))
	 			$items[$ref]['class'] .= ' selected';
			elseif(isset($items[$ref2]))
				$items[$ref2]['class'] .= ' selected';
			elseif(isset($items[$ref3]))
				$items[$ref3]['class'] .= ' selected';

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

	 public function generateMenu($menu, $list=null, $id=null, $class=null) {
	 	global $ef;
	 	if(isset($menu['callback'])) {
	 		$items = call_user_func($menu['callback'], $menu['items']);
	 	} else $items = isset($menu['items']) ? $menu['items'] : null;


		if(!isset($list)) 
			$list = isset($menu['list']) ? $menu['list'] : false;
		
		if(!isset($id))
			$id = isset($menu['id']) ? "id='{$menu['id']}'" : null;
		else
			$id = "id='$id'";
		if(!isset($class))
			$class = isset($menu['class']) ? "class='{$menu['class']}'" : null;
		else
			$class = "class='$class'";
			
	 	$html = "<nav {$id}{$class}>";
	 	$noListClass = (!$list) ? "class='nolist' " : null;
	 	$html .= "<ul {$noListClass}>";
	 	$separator = isset($menu['separator']) ? $menu['separator'] : null;

	 	if(isset($items)) {
		 	foreach ($items as $item) {

		 		$classes  = (!$list || (isset($item['class']))) ? "class='" : null;
		 		$classes .= (!$list) ? "nolist " : null;
		 		$classes .= isset($item['class']) ? "{$item['class']}" : null;
		 		$classes .= "' ";
		 		$html .= "<li {$classes}>";
		 		$html .= "<a href='{$item['url']}'>{$item['title']}</a> $separator";
		 		$html .= "</li>";
		 	}
		}
		if(isset($separator)) {
			$pos = strrpos($html, "$separator");
			if($pos) {
				$html = substr_replace($html, '', $pos);
			}
		}

	 	$html .="</ul>";

	 	$html .= "</nav>";
	 	return $html;
	 }



	 /**
	  * Adds a WYSIWYG-editor (Uses the elRTE-editor)
	  *
	  * @return void
	  * @author 
	  **/
	 public function editor($element=".editor", $options=null) {
		global $ef;


		$options = isset($options) ? json_encode($options) : json_encode(array('lang' => 'en',
													  'styleWithCSS' => false,
													  'height' => 400,
													  'toolbar' => 'maxi'));

	 	$sitePath = $ef->cfg['config-db']['general']['siteurl'] . 'themes/core/libraries/';
	 	$pathToEditor = $sitePath . 'elrte/';

	 	$this->addExternalStyle($pathToEditor . 'css/smoothnes/jquery-ui-1.8.13.custom.css');
	 	$this->addExternalStyle($pathToEditor . 'css/elrte.min.css');

	 	$this->addScript($pathToEditor . 'js/jquery-1.6.1.min.js', 'head');
	 	$this->addScript($pathToEditor . 'js/jquery-ui-1.8.13.custom.min.js', 'head');
	 	$this->addScript($pathToEditor . 'js/elrte.min.js', 'head');

	 	$editorScript = <<<EOD
	 	<script type="text/javascript">
	 		$().ready( function() {
	 			$('{$element}').elrte({$options});
	 		});
	 	</script>
EOD;

		$this->addView($editorScript, 'footer');
	 }
}