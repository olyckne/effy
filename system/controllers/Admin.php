<?php


/**
*  Admin Control Panel
*/
class Admin extends Controller implements Active
{

	public $adminMenu;

	function __construct($args=null) {
		parent::__construct();


		$this->requireLogin = true;

		$this->adminMenu = array('callback' => 'modifyMainMenu', 'list' => true, 'id' => null, 'class' => null,
				'items' => array(
						'index'		=> array(
								'title' => 'Dashboard',
								'url'	=> $this->url . 'index',
								'class' => 'ef-menu-item',
							),
						'settings'	=> array(
								'title' => 'Settings',
								'url' =>  $this->url . 'settings',
								'class' => 'ef-menu-item',
								),
						'theme'	=> array(
								'title' => 'Theme',
								'url'	=> $this->url . 'theme',
								'class' => 'ef-menu-item',
								),
						'page'		=> array(
								'title' => 'Pages',
								'url'	=> $this->url . 'page/listAll',
								'class' => 'ef-menu-item',
							),
						'canurls' 	=> array(
								'title' => 'Canurls',
								'url' 	=> $this->url . 'canurls',
								'class' => 'ef-menu-item',
								),
						'user'		=> array(
								'title' => 'User',
								'url' 	=> $this->url . 'user',
								'class' => 'ef-menu-item',
							),
						'controller'		=> array(
								'title' => 'Controller',
								'url' 	=> $this->url . 'controller',
								'class' => 'ef-menu-item',
							),
						'developer' => array(
								'title' => 'Developer',
								'url'	=> $this->url . 'developer',
								'class' => 'ef-menu-item',
							),
					),
			);
		

		$this->theme->mainmenu = $this->adminMenu;



		Auth::loginAndRedirect('admin/' . $this->action);

	}
	/**
	 * All controllers must have an index function
	 *
	 * @return void
	 * @author 
	 **/
	public function index() {
		$this->theme->addView("Admin control panel!");
	}


	/**
	 * settings admin page
	 *
	 * @return void
	 * @author 
	 **/
	public function settings($save = null) {
		if(isset($save)) $this->saveSettings();

		global $ef;


		$this->theme->addView("<h2>Settings!</h2>");

		$timezones = timezone_identifiers_list();
		$timezoneOption = "<select id='timezone' name='timezone'>";
		foreach ($timezones as $timezone) {
			$selected = ($timezone == $ef->cfg['config-db']['general']['timezone']) ? " selected='selected'" : null;
			$timezoneOption .= "<option{$selected}>{$timezone}</option>";
		}


		$timezoneOption .= "</select>";
		$now = date('r');

		extract($ef->cfg['config-db']['general']);

		$form = <<<EOD
			<form action='settings/save' method="POST">
			<table class='form-table'>
			<tbody>
			<tr>
				<td>
				<label for='sitetitle'>Site title </label>
				</td>
				<td>
				<input type='text' name='sitetitle' id='sitetitle' value='{$sitetitle}'>
				</td>
			</tr>
			<tr>
				<td>
				<label for='siteurl'>Site url </label>
				</td>
				<td>
				<input type='url' name='siteurl' id='siteurl' value='{$siteurl}'>
				</td>
			</tr>
			<tr>
				<td>
				<label for='owner_firstname'>Owner firstname  </label>
				</td>
				<td>
				<input type='text' name='owner_firstname' id='owner_firstname' value='{$owner_firstname}'>
				</td>
			</tr>
			<tr>
				<td>
				<label for='owner_lastname'>Owner lastname</label>
				</td>
				<td>
				<input type='text' name='owner_lastname' id='owner_lastname' value='{$owner_lastname}'>
				</td>
			</tr>
				<td>
				<label for='mail'>Owner mail </label>
				</td>
				<td>
				<input type='email' name='mail' id='mail' value='{$owner_mail}'>
				</td>
			</tr>
			<tr>
				<td>
				<label for='timezone'>Timezone </label>
				</td>
				<td>
				{$timezoneOption} 
				<span class="description">{$now} </span>
				</td>
			</tr>
			<tr>
				<td scope="row">
				<input type='submit' value='Save'>
				</td>
				<td></td>
			</tr>
			</tbody>
			</table>
			</form>
EOD;

		$this->theme->addView($form);


	}

	/**
	 * saveSettings
	 *
	 * @return void
	 * @author 
	 **/
	public function savesettings() {
		global $ef;
		foreach ($_POST as $key => $value) {
			$ef->cfg['config-db']['general'][$key] = $value;
		}

		$db = new Database();

		$ef->saveConfig();

		Feedback::addSuccess('Settings saved!');

		$ef->req->redirectTo('admin', 'settings');
	}



	/**
	 * developer, shows stuff
	 *
	 * @return void
	 * @author 
	 **/
	public function developer($arg = null) {
		global $ef;

		function modifyNavbar($items) {
			global $ef;
			$ref = isset($ef->req->args[0]) && isset($items[$ef->req->args[0]]) ? $ef->req->args[0] : null;
			if($ref)
				$items[$ref]['class'] .= ' selected';
			
			return $items;

		}

		$menu = array(
			'callback' => 'modifyNavbar', 'list' => false,
			'items' => array(
					'config' => array(
							'title' => 'Configuration',
							'url' => $this->url . 'developer/config',
							'class' => null,
						),
					'request'	=> array(
							'title' => 'Request',
							'url' => $this->url . 'developer/request',
							'class' => null,
						),
					'server' => array(
							'title' => 'Server',
							'url'	=> $this->url . 'developer/server',
							'class' => null,
						),
					'session' => array(
							'title' => 'Session',
							'url'	=> $this->url . 'developer/session',
							'class'	=> null,
						),
			));

		$html = $this->theme->generateMenu($menu);

		switch ($arg) {
			case 'config':
				$html .= "<pre>" . print_r($ef->cfg, true) . "</pre>";
				break;

			case 'request':
				$html .= "<pre>" . print_r($ef->req, true) . "</pre>";
				break;

			case 'server':
				$html .= "<pre>" . print_r($_SERVER, true) . "</pre>";
				break;

			case 'session':
				$html .= "<pre>" . print_r($_SESSION, true) . "</pre>";
				break;
			
			default:
				break;
		}


		$this->theme->addView($html);

	}


	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	public function page($action = null, $args=null) {
		global $ef;

		$action = isset($action) ? $action : 'index';
		$ef->req->args = array($args);
		$ef->frontController('page', $action);
	}




	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	public function user($action = null,$args = null)	{
		global $ef;

		$action = isset($action) ? $action : 'index';
		$ef->req->args = array($args);

		$ef->frontController('user', $action);
	}


	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	public function controller($save = false) {
		global $ef;

		if($save) {
			$ef->cfg['config-db']['general']['standard_controller'] = $_POST['standard_controller'];		

			$ef->saveConfig();
		}

		$standard = $ef->cfg['config-db']['general']['standard_controller'];

		$html = "<h2>Available controllers</h2>";

		$html .= "<form action='controller/save' method='post'>";
		$html .= "<label for='standard_controller'>Front controller</label ><input type='text' name='standard_controller' id='standard_controller' value='{$standard}'>";

		$html .= "<br><br><input type='submit' value='Save'>";
		$html .= "</form>";
//		$html .= $table;
		$this->theme->addView($html);
	}

	/**
	 * show the theme settings page
	 *
	 * @return void
	 * @author 
	 **/
	public function theme($themeChoice = null) {
		global $ef;

		if(isset($themeChoice)) {
			$this->theme->setTheme($themeChoice);
			$ef->req->redirectTo('admin', 'theme');
		}


		$themeDir = BASE_PATH . '/themes/';

		$themeDir = scandir($themeDir);

		$themes = array('callback' => null,
					'items' => array(


						));


		foreach ($themeDir as $theme) {
			$indexFile = BASE_PATH . '/themes/' . $theme . '/index.php';
			if(is_file($indexFile) && $theme != '..') {
				$themes['items'][$theme] = array(
							'title' => $theme . ' ',
							'url'	=> $this->url . "theme/{$theme}",
							'class' => null,
					); 
			}
		}

		$html = "<h2>Available themes</h2>";
		$html .= $this->theme->generateMenu($themes);


		$mainmenu = isset($ef->cfg['config-db']['theme']['mainmenu']['items']) ? $ef->cfg['config-db']['theme']['mainmenu']['items'] : array();

		$html .= "<h3>Main menu:</h3>";
		$html .= "<table><thead><tr><th>Title</th><th>Url</th></tr></thead>";
		foreach ($mainmenu as $item) {
			$html .= "<tr><td>{$item['title']}</td> <td><a href='{$item['url']}'>{$item['url']}</a></td></tr>";
		}
		$html .= "<tr><td><a href='{$this->url}editMenu' class='btn'>Edit menu</a></td><td></td></tr>";

		$html .= "</table>";



		$this->theme->addView($html);
	}



	/**
	 * edit the main menu
	 *
	 * @return void
	 * @author 
	 **/
	public function editMenu($add=null) {
		global $ef;

		$html = "<h2>Edit main menu</h2>";

		$mainmenu = isset($ef->cfg['config-db']['theme']['mainmenu']['items']) ? $ef->cfg['config-db']['theme']['mainmenu']['items'] : array();

		$html .= "<form action='{$this->url}saveMenu' method='POST'>";
		$html .= "<table><thead><tr><th>Title</th><th>Url</th></tr></thead>";

		$nr = 0;
		foreach ($mainmenu as $item) {
			$html .= "<tr><td>
					<input type='text' name='menuitemTitle{$nr}' value='{$item['title']}'>
					</td> 
					<td>
					<input type='text' name='menuitemUrl{$nr}' value='{$item['url']}'>
					<span class='description'><a href='{$this->url}removeMenu/{$item['title']}'' class='btn danger'>Remove</a><span>
					</td>
					</tr>";
			$nr++;
		}
		for ($i=0; $i < $add; $i++) { 
			$html .= "<tr><td>
				<input type='text' name='menuitemTitle{$nr}'>
				</td>
				<td>
				<input type='text' name='menuitemUrl{$nr}'>
				</td>";
			$nr++;
		}

		$add++;
		$html .= "<tr><td><a href='{$this->url}editMenu/{$add}' class='btn'>Add item</a></td></td><td><input type='submit' value='Save' class='btn'></td></tr>";
		$html .= "</table>";
		$html .= "</form>";


		$this->theme->addView($html);
	}


	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	public function saveMenu() {
		global $ef;
		$mainMenu = array('callback' => 'modifyMainMenu', 'list' => true, 'id' => null, 'class' => null,
				'items' => array(
					
					)
			);
		
		$len = count($_POST);
		for ($i=0; $i < $len; $i++) {
			$title = isset($_POST['menuitemTitle'.$i]) && !empty($_POST['menuitemTitle'.$i]) ? $_POST['menuitemTitle'.$i] : null;
			$url = isset($_POST['menuitemUrl'.$i]) && !empty($_POST['menuitemUrl'.$i]) ? $_POST['menuitemUrl'.$i] : null;
			
			if(isset($title) && isset($url)) {
				$item = array('title' => $_POST['menuitemTitle'.$i],
								'url' => $_POST['menuitemUrl'.$i]);

				$mainMenu['items'][$item['title']] = array(
						'title' => $item['title'],
						'url' 	=> $item['url'],
						'class' => 'ef-menu-item',
					);
			}
		}
		$ef->cfg['config-db']['theme']['mainmenu'] = $mainMenu;


		$ef->saveConfig();

		$ef->req->redirectTo('admin', 'editMenu');

	}


	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	public function removeMenu($title) {
		global $ef;

		if(isset($ef->cfg['config-db']['theme']['mainmenu']['items'][$title])) {
			unset($ef->cfg['config-db']['theme']['mainmenu']['items'][$title]);
		}

		$ef->saveConfig();

		$ef->req->redirectTo('admin', 'editMenu');
	}


	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	public function canurls($action=null,$args=null) {
		global $ef;

		$action = isset($action) ? $action : 'index';
		$ef->req->args = array($args);

		$ef->frontController('canonical', $action);	}
}