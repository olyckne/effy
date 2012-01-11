<?php


/**
* 
*/
class User extends Controller implements Active
{
	/**
	 * undocumented class variable
	 *
	 * @var string
	 **/
	private $user_model;

	function __construct() {
		global $ef;
		parent::__construct();

		$this->user_model = User_model::GetInstance();
	}

	/**
	 * Every controllers must have an index-function
	 *
	 * @return void
	 * @author 
	 **/
	public function index() {
		global $ef;
		$this->settings();
		if(!$this->user_model->isAuthenticated()) {
			$ef->req->redirectTo('user', 'login');
		}

	}



	/**
	 * Shows the user settings
	 *
	 * @return void
	 * @author 
	 **/
	public function settings() {
		$html = "<h3>User settings</h3>";

		extract($this->user_model->userData);


		$sites = "<select id='socialsites' name='socialsites'>";
		if(isset($socialsites)) {
			foreach ($socialSites as $key => $value) {
				$sites .= "<option value='{$key}'>{$key}</option>";
			}
		}

		$sites .= "</select>";

		$html .= <<<EOD
		<form action="user/save" method="POST">
			<table class='form-table'>
				<tr>
					<td>
					<label for="username">Username</label>
					</td>
					<td>
					<input type="text" name="username" id="username" value="{$username}" disabled>
					</td>
				</tr>
				<tr>
					<td>
					<label for="firstname">First name</label>
					</td>
					<td>
					<input type="text" name="firstname" id="firstname" value="{$firstname}">
					</td>
				</tr>
				<tr>
					<td>
					<label for="lastname">Last name</label>
					</td>
					<td>
					<input type="text" name="lastname" id="lastname" value="{$lastname}">
					</td>
				</tr>
				<tr>
					<td>
					<label for="mail">Mail</label>
					</td>
					<td>
					<input type="text" name="mail" id="mail" value="{$mail}">
					</td>
				</tr>
				<tr>
					<td>
					<label for="role">Role</label>
					</td>
					<td>
					<input type="text" name="role" id="role" value="{$role}" disabled>
					</td>
				</tr>
				<tr>
					<td>
					<label for="socialsites">Social sites</label>
					</td>
					<td>
					{$sites}
					<span class="description">
					<a href='#' class='btn' disabled>Add site</a> Sorry, not implemented yet!
					</span>
					</td>
				</tr>
				<tr>
					<td>
					<input type="submit" value="Save" disabled>
					<span class="description">Saving not implemented yet. Sorry!</span>
					</td>
					<td></td>
				</tr>
				
				
			
		
			</table>
		</form>	

EOD;


		$this->theme->addView($html);
	}


	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	public function save() {
		global $ef;

		Feedback::addFeedback( array('class' => 'alert-message notice', 'message' => 'Saving not implemented yet. Sorry!'));

		$ef->req->redirectTo('admin', 'user');
	}




	/**
	 * login page
	 *
	 * @return void
	 * @author 
	 **/
	public function login($back=null) {

		$redirect = "";
		if(isset($back)) {
			$redirect = "<input type='hidden' name='redirect' value='{$_SESSION['redirectTo']}'>";
		}
		$html = "<div id='ef-login'>
					<h1>Login</h1>";

		


		$html .= <<<EOD
		<form action="{$this->url}doLogin" method="POST">
			{$redirect}
			<input type="text" name="username" id="username" placeholder="username">
			<br><br>
			<input type="password" name="password" id="password" placeholder="password">
			<br><br>
			<input type="submit" value="Login" class='large btn'>
		</form>

EOD;
		
		$html .= "</div>";
		$this->theme->addView($html);
	}


	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	public function loginAndRedirectTo($url) {
		$_SESSION['redirectTo'] = str_replace('+', '/', $url);
		$this->login('andDirectBack');
	}

	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	public function logoutAndRedirectTo($url) {
		$_SESSION['redirectTo'] = str_replace('+', '/', $url);
		$this->logout('andDirectBack');
	}

	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	public function doLogin() {
		global $ef;

		$redirect = (isset($_POST['redirect']) && !empty($_POST['redirect'])) ? $_POST['redirect'] : 'admin';
		$username = (isset($_POST['username']) && !empty($_POST['username'])) ? $_POST['username'] : '';
		$password = (isset($_POST['password']) && !empty($_POST['password'])) ? $_POST['password'] : '';

		if($this->user_model->login($username, $password)) {
			Feedback::addSuccess('Logged in!');
		}
		else {
			Feedback::addError('Wrong username and/or password!');
			$redirect = 'user/login';
		}

		$ef->req->redirectTo($redirect);
	}


	/**
	 * logout use
	 *
	 * @return void
	 * @author 
	 **/
	public function logout($back=null) {
		global $ef;
		$redirect = isset($back) ? $_SESSION['redirectTo'] : $ef->req->baseUrl;

		$this->user_model->logout();

		$ef->req->redirectTo($redirect);
	}

}