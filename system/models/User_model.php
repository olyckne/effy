<?php


/**
*  User handle
*/
class User_model implements Singleton, SQL, Installable  {
	

		const sessionName = 'ef-user';
		private static $instance = null;

		public $db;
		/**
		 * undocumented class variable
		 *
		 * @var string
		 **/
		public $userData = array(
			'id' => null,
			'username' => null,
			'firstname' => null,
			'lastname' => null,
			'mail' => null,
			'role' => null,
			'socialSites' => array(),
			);
		

		protected function __construct($userData=array()) {
			$this->userData = $userData;
			$this->db = new Database();
		}


		/**
	 	 * called when installing a model.
	 	 * Nothing happens in the base class but override it in the sub-class, for creating database table etc.
	 	 *
		 * @return void
		 * @author 
		 **/
		public function installModel() {
			$this->db->executeQuery($this->SQL('create model'));
		}

		public function updateModel() {;}

		public function removeModel() {;}


		public static function GetInstance() {
			if(self::$instance == null) {
				$obj = __CLASS__;
				if(isset($_SESSION[self::sessionName])) {
					self::$instance = new $obj($_SESSION[self::sessionName]);
				} else {			
					self::$instance = new $obj();
				}
			}
		
			return self::$instance;
		}

		/**
		 * undocumented function
		 *
		 * @return void
		 * @author 
		 **/
		public function storeInSession() {
			$_SESSION[self::sessionName] = $this->userData;
		}



		/**
		 * tries to log in a user
		 *
		 * @return void
		 * @author 
		 **/
		public function login($username, $password) {
			$user = $this->db->executeAndFetchAll($this->SQL('get user'), array($username));
			$ok = false;

			if($this->checkPassword($password)) {
				$user = $user[0];
				$this->userData = $user;

				$ok = true;
				$this->storeInSession();
			}

			return $ok;

		}

		/**
		 * log out user
		 *
		 * @return void
		 * @author 
		 **/
		public function logout() {
			global $ef;
			$ef->destroyAndRestartSession();
		}
		
		/**
		 * undocumented function
		 *
		 * @return void
		 * @author 
		 **/
		public function checkPassword($password) {
			$res = isset($this->db->res[0]) ? $this->db->res[0] : null;

			$correctPass = false;
			if($res['password'] == md5($res['salt'].$password)) {
				$correctPass = true;
			}
			
			return $correctPass;
		}


		/**
		 * undocumented function
		 *
		 * @return void
		 * @author 
		 **/
		public function isAuthenticated() {
			return empty($this->userData) ? false : true;
		}

		/**
		 * undocumented function
		 *
		 * @return void
		 * @author 
		 **/
		public static function SQL($id = null) {
			global $ef;

			$db_prefix = $ef->cfg['db']['db_prefix'];
			$tableName = $db_prefix . 'user';
			
			$salt = "";
			if($id == 'create model' || $id == 'add user') {
				$salt = User::getRandomSalt();
			}

			$query = array(
				'create model' 	=> "CREATE TABLE IF NOT EXISTS {$tableName}_role (
										id CHAR(5) PRIMARY KEY,
										name CHAR(40) NOT NULL
									)ENGINE=InnoDB;

									CREATE TABLE IF NOT EXISTS {$tableName} (
									id INT PRIMARY KEY AUTO_INCREMENT,
									username VARCHAR(255) NOT NULL,
									firstname VARCHAR(255) NOT NULL,
									lastname VARCHAR(255) NOT NULL,
									mail VARCHAR(255) NOT NULL,
									role CHAR(5) NOT NULL DEFAULT 'user',
									password VARCHAR(255) NOT NULL,
									salt VARCHAR(255) NOT NULL,

									FOREIGN KEY(role) REFERENCES {$tableName}_role(id)
									)ENGINE=InnoDB;

									INSERT INTO {$tableName}_role(id, name) VALUES('admin', 'Administrator of the site'),
																			('user', 'User of the site.');

									INSERT INTO {$tableName}(username,firstname,lastname,mail,role,password,salt)
													VALUES('admin', '{$ef->cfg['config-db']['general']['owner_firstname']}',
																	 '{$ef->cfg['config-db']['general']['owner_lastname']}',
																	 '{$ef->cfg['config-db']['general']['owner_mail']}',
																	 'admin', md5('{$salt}admin'), '{$salt}'
																	 );

				",
				'add user'		=> "INSERT INTO {$tableName}(username,firstname,lastname,mail,role,password)VALUES(?,?,?,?,?,?)",

				'edit user'		=> "UPDATE {$tableName} SET username=?, firstname=?, lastname=?, mail=?, role=?, password=?;",
				'remove user' 	=> "DELETE FROM {$tableName} WHERE username=? LIMIT 1;",
				'get user'			=> "SELECT * FROM {$tableName} WHERE username=?;",


				);
			
			if(!isset($query[$id])) {
	  		throw new Exception(t('#class error: Out of range. Query = @id', array('#class'=>get_class(), '@id'=>$id)));
				
			}

			return $query[$id];
		}


		/**
		 * undocumented function
		 *
		 * @return void
		 * @author 
		 **/
		public static function getRandomSalt($pattern = null) {
			$pattern = isset($pattern) ? $pattern : "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890!@#$%&/()";
			$totalChars = strlen($pattern)-2;

			$salt = $pattern[rand(0,$totalChars)];

			for($i = 0; $i < 9; $i++) {
				$salt .= $pattern[rand(0,$totalChars)];
			}


			return $salt;
		}

}