<?php
session_start();

$app_path = isset($_SESSION['APP_PATH']) ? $_SESSION['APP_PATH'] : 'application/';

$configFile = $app_path . 'config-sample.php';

if(is_file($configFile)) $configFile = file_get_contents($configFile);

$step = isset($_GET['step']) ? $_GET['step'] : 0;
$submit = isset($_POST['submit']) ? $_POST['submit'] : false;


$html = <<<EOD
<!DOCTYPE html>
<html lang="sv">
<head>
	<meta charset="utf-8">
	<meta name="keywords" content="">
	<meta name="description" content="">
	<meta name="author" content="Mattias Lyckne, hello@mattiaslyckne.se">
	<meta name="copyright" content="Copyright 2011">
	
	<title>Effy setup!</title>
	<link rel="stylesheet" href="themes/default/style.css" title="Style">
	<style>
		.container {
			margin: 0 auto;
			width: 800px;
		}

		form {
			width: 250px;
			text-align: right;

		}

	</style>
<!--[if lt IE 9]>
<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
</head>
<body>
<div class='container'>
	<header id="ef-header">	
		<div id="ef-pageLogo">
		SETUP EFFY!
		</div>

		<nav id="ef-mainmenu">
			<ul>
			</ul>
		</nav>
	</header>
EOD;

$nextStep = $step+1;
$done = false;

$html .= <<<EOD
	<form id="setupForm" action="setup.php?step={$nextStep}" method="post">
EOD;
switch ($step) {
	case 0: //Check writable stuff, data directory, config-file.
		$data_path = 'data';
		$ok = true;
		if(!is_writable($app_path)) {
			$html .= "Can't create config.php file, application directory not writable?<br> You need to chmod 777 it under the setup phase (you can and should set it back later!)";
			$ok = false;
		}
		if(is_dir($data_path)) {
			if(!is_writable($data_path)) {
				$html .= "The data directory doesn't seem to be writable!<br>";
				$ok = false;
			}
			
		}
		elseif(!mkdir($data_path)) {
			$html .= "Couldn't create the data directory. You should create it yourself! <br>";
			$ok = false;
		}

		if($ok) header("Location: setup.php?step={$nextStep}");
		exit;

		break;
	case 1: // Database setup - step 1
		
		$html .= <<<EOD
	<h2>Database setup</h2>
		<label for="db">Database type:</label>
		<select name="db">
			<option value="sqlite">SQLite</option>
			<option value="mysql">mySQL</option>
		</select>
		<br><br>
EOD;
		break;
	
	case 2: // database setup - step 2
		$db = isset($_POST['db']) ? $_POST['db'] : -1;
		$html .= "<h2>Database setup</h2>";
		if($db == "mysql") {
			$html .= "<label for='dsn'>DSN for mySQL: </label>";
			$html .= "<input type='text' id='dsn' name='dsn' value='mysql:host=localhost;port=3306;dbname=effy' required> <br>";
		}
		else {
			$dsn = "sqlite:data/.htdb.sqlite'";
			$html .= "<input type='hidden' id='dsn' name='dsn' value='{$dsn}'>";
		}
		$html .= <<<EOD
		<label for="user">Username: </label>
		<input type="text" id="user" name="user" required>		<br>
		<label for="pass">Passowrd: </label>
		<input type="password" id="pass" name="pass" required>	<br>
		<label for="dbprefix">Table prefix: </label>
		<input type="text" id="dbprefix" name="dbprefix" value="ef_">	<br>

EOD;
		break;

	case 3: // database check, if success redirect to step 4
		$dsn = isset($_POST['dsn']) ? $_POST['dsn'] : null;
		$user = isset($_POST['user']) ? $_POST['user'] : null;
		$pass = isset($_POST['pass']) ? $_POST['pass'] : null;
		$prefix = isset($_POST['dbprefix']) ? $_POST['dbprefix'] : null;
		
		try {
			$db = new PDO($dsn, $user, $pass);

			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch(PDOException $e) {
			$html .= "<br>Connection failed with error: " . $e->getMessage();
			$html .= "<br>Is the data-directory writable? <br> Is the info correct?";
			$html .= "<a href='setup.php?step=2'>Back</a>";
			break;
		}
		$replace = array(
				'BASEURL' => '{BASEURL}',
				'DSN' => $dsn,
				'USERNAME' => $user,
				'PASSWORD' => $pass,
				'DBPREFIX' => $prefix,
			);
		createConfigFile('config-sample.php', $replace);

		$html .= "Database connection OK.<br>";
		break;

	case 4: // Site setup. title, url etc.
		$currentUrl = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
		$parts = parse_url($currentUrl);
	
		$script = $_SERVER['SCRIPT_NAME'];
		$dir = dirname($script);
		$guessedUrl = "{$parts['scheme']}://{$parts['host']}" . (isset($parts['port']) ? ":{$parts['port']}" : "") . "{$dir}" . "/";
		
		$html .= <<<EOD
		<h2>Site setup</h2>
		<label for='title'>Site title: </label>
		<input type='text' name='title' id='title'> <br>

		<label for='url'>Site url: </label>
		<input type='text' name='url' id='url' value='{$guessedUrl}' required> <br>

		<label for='owner_firstname'>Owner firstname: </label>
		<input type='text' name='owner_firstname' id='owner_firstname'> <br>

		<label for='owner_lastname'>Owner lastname: </label>
		<input type='text' name='owner_lastname' id='owner_lastname'> <br>

		<label for='mail'>Admin mail: </label>
		<input type='email' name='mail' id='mail'> <br>



EOD;
		
		break;
	
	case 5: // Create config and insert database stuff
		@include($app_path . 'config.php');
		
		extract($ef->cfg['db']);
		$db = new PDO($dsn, $username, $password);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$query = <<<EOD
CREATE TABLE IF NOT EXISTS {$db_prefix}Effy(
			`ef_module` VARCHAR(255),
			`ef_key` VARCHAR(255),
			`ef_value` TEXT,
			PRIMARY KEY(`ef_module`, `ef_key`)) ENGINE=InnoDB;
EOD;
	
		$stmt = $db->prepare($query);
		if($stmt->execute()) {
			$title = isset($_POST['title']) ? $_POST['title'] : null;
			$_SESSION['url'] = isset($_POST['url']) ? $_POST['url'] : null;
			$firstname = isset($_POST['owner_firstname']) ? $_POST['owner_firstname'] : null;
			$lastname = isset($_POST['owner_lastname']) ? $_POST['owner_lastname'] : null;
			$mail = isset($_POST['mail']) ? $_POST['mail'] : null;


			$ef->cfg['config-db']['general']['sitetitle'] = $title;
			$ef->cfg['config-db']['general']['siteurl'] = $_SESSION['url'];
			$ef->cfg['config-db']['general']['owner_firstname'] = $firstname;
			$ef->cfg['config-db']['general']['owner_lastname'] = $lastname;
			$ef->cfg['config-db']['general']['owner_mail'] = $mail;
			$ef->cfg['config-db']['general']['char_encoding'] = 'UTF-8';
			$ef->cfg['config-db']['general']['timezone'] = "Europe/Stockholm";
			$ef->cfg['config-db']['theme']['name'] = 'default';
			$ef->cfg['config-db']['general']['standard_controller'] = 'index';

			$cfg = serialize($ef->cfg['config-db']);

			$query = "INSERT INTO {$db_prefix}Effy(ef_module, ef_key, ef_value) VALUES('effy', 'config', ?)";

			$stmt = $db->prepare($query);

			$stmt->execute(array($cfg));
		}
		else {
			echo "NEJ!";
		}
		$replace = array(
			'BASEURL' => $_SESSION['url']
			);
		createConfigFile('config.php', $replace);
		installModels();
	case 6:
		$html .= "Setup done!";
		$html .= "<a href='{$_SESSION['url']}'>Continue</a>";
		$done = true;

		break;
	default:
		break;
}


if(!$done) $html .= "<input type='submit' value='next'>";
$html .= <<<EOD

</form>
</div> 
</body>
</html>
EOD;

$step++;
if($submit) {
	header("Location: setup.php?step={$step}");
	exit;
}

echo $html;


function createConfigFile($fileName, $replace) {
	$app_path = isset($_SESSION['APP_PATH']) ? $_SESSION['APP_PATH'] : 'application/';

	$configFile = $app_path . 'config-temp.php';

	$configFile = (is_file($configFile)) ? file_get_contents($configFile) : file_get_contents($app_path . $fileName);


	$patterns = array('/[{](.*)[}]/e');

	$newConfig = preg_replace($patterns, '$replace["$1"]', $configFile);

	file_put_contents($app_path .'config.php', $newConfig);

	require_once($app_path . 'config.php');
}


/**
 * installModels runs the installModel-function on all the core models.
 *
 * @return void
 * @author 
 **/
function installModels() {
		require_once('system/core/Model.php');
		require_once('system/core/interfaces/SQL.php');
		require_once('system/core/interfaces/Singleton.php');
		require_once('system/core/Database.php');

		$models = array(
				'Page_model' => array('class' => 'Page_model', 'path' => 'system/models/Page_model.php'),
				'CanonicalUrl' => array('class' => 'CanonicalUrl', 'path' => 'system/core/CanonicalUrl.php'),
				'User' => array('class' => 'User', 'path' => 'system/core/User.php'),
			);

		foreach($models as $model) {
			require_once($model['path']);

			if($model['class'] == 'User') {
				$temp = $model['class']::GetInstance();
			} else {
				$temp = new $model['class']();
			}

			$temp->installModel();
		}
	}