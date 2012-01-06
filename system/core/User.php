<?php


/**
*  User handle
*/
class User implements useSQL {
	
	/**
		 * undocumented class variable
		 *
		 * @var string
		 **/
		private $username;
		private $firstname;
		private $lastname;
		
		private $mail;

		private $role;

		/**
		 * An array with social sites and users username, like 'site' => 'twitter', 'username' => 'twitter', 'url' => 'https://twitter.com/twitter'
		 *
		 * @var string
		 **/
		private $socialSites = array();
		

		function __construct() {
			global $ef;

			$db = $ef->db;

			$this->db->executeQuery($this->SQL('create model'));
		}

		/**
		 * undocumented function
		 *
		 * @return void
		 * @author 
		 **/
		public function SQL($id = null) {
			global $ef;

			$db_prefix = $ef->cfg['db']['db_prefix'];

			$query = array(
				'create model' 	=> "CREATE TABLE IF NOT EXISTS {$db_prefix}user_role (
										id CHAR(5) PRIMARY KEY,
										name CHAR(40) NOT NULL
									)ENGINE=InnoDB;

									CREATE TABLE IF NOT EXISTS {$db_prefix}user (
									id INT PRIMARY KEY AUTO_INCREMENT,
									username VARCHAR(255) NOT NULL,
									firstname VARCHAR(255) NOT NULL,
									lastname VARCHAR(255) NOT NULL,
									mail VARCHAR(255) NOT NULL,
									role CHAR(5) NOT NULL DEFAULT user,
									password VARCHAR(255) NOT NULL,

									FOREIGN KEY(role) REFERENCES {$db_prefix}user_role(id)
									)ENGINE=InnoDB;

				",

				'add user'		=> "INSERT INTO {$db_prefix}user(username,firstname,lastname,mail,role,password)VALUES(?,?,?,?,?,?)",

				'edit user'		=> "",
				'remove user' 	=> "",


				);
			
			if(!isset($query[$id])) {
	  		throw new Exception(t('#class error: Out of range. Query = @id', array('#class'=>get_class(), '@id'=>$id)));
				
			}

			return $query[$id];
		}

}