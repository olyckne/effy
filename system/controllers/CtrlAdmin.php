<?php


/**
*  Admin Control Panel
*/
class CtrlAdmin extends Controller
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
						'themes'	=> array(
								'title' => 'Themes',
								'url'	=> $this->url . 'themes',
								'class' => 'ef-menu-item',
								),
						'posts'		=> array(
								'title' => 'Posts',
								'url'	=> $this->url . 'posts',
								'class' => 'ef-menu-item',
							),
						'pages'		=> array(
								'title' => 'Pages',
								'url'	=> $this->url . 'pages',
								'class' => 'ef-menu-item',
							),
						'user'		=> array(
								'title' => 'User',
								'url' 	=> $this->url . 'user',
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
				<label for='owner'>Owner name  </label>
				</td>
				<td>
				<input type='text' name='owner' id='owner' value='{$owner_name}'>
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
			<tr><th></th></tr>
			<tr>
				<th></th>
				<th scope="row">
				<input type='submit' value='Save'>
				</th>
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
	public function pages($action = null, $args=null) {
		global $ef;

		$action = isset($action) ? $action : 'index';
		$ef->req->args = array($args);
		$ef->frontController('content', $action);
	}




	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	public function user($args = null)	{
		global $ef;

		$action = isset($action) ? $action : 'index';
		$ef->req->args = array($args);
		$ef->frontController('user', $action);
	}

	/**
	 * show the theme settings page
	 *
	 * @return void
	 * @author 
	 **/
	public function themes($themeChoice = null) {
		global $ef;

		if(isset($themeChoice)) {
			$this->theme->setTheme($themeChoice);
			$ef->req->redirectTo('admin', 'themes');
		}


		$themeDir = BASE_PATH . '/themes/';

		$themeDir = scandir($themeDir);

		$themes = array('callback' => null,
					'item' => array(
							

						));


		foreach ($themeDir as $theme) {
			$indexFile = BASE_PATH . '/themes/' . $theme . '/index.php';
			if(is_file($indexFile) && $theme != '..') {
				$themes['items'][$theme] = array(
							'title' => $theme . ',',
							'url'	=> $this->url . "themes/{$theme}",
							'class' => null,
					); 
			}
		}

		$html = "<h2>Available themes</h2>";
		$html .= $this->theme->generateMenu($themes);

		$this->theme->addView($html);
	}
}