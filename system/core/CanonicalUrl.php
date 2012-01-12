<?php


/**
* 
*/
class CanonicalUrl extends Model implements SQL, Installable
{
	
	public $can_url = array();



	/**
	 * checks if url is a canonical url
	 *
	 * @return void
	 * @author 
	 **/
	public function checkUrl($url) {
		$url = $this->db->executeAndFetchAll($this->SQL('get real_url from can_url'), array($url));
		$url = isset($url[0]) ? $url[0] : null;


		return $url['real_url'];
	}

	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	public function getById($id) {
		return $this->load('get url from id', $id);
	}

	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	public function getByCanUrl($canUrl) {
		return $this->load('get real_url from can_url', $canUrl);
	}


	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	public function load($sql, $value) {
		$this->can_url = $this->db->executeAndFetchAll($this->SQL($sql), array($value));

		if(isset($this->can_url[0])) {
			$this->can_url = $this->can_url[0];
		}
	}
	/**
	 * add canonical url to database
	 *
	 * @return void
	 * @author 
	 **/
	public function addUrl($url=null) {
		if(isset($url)) {
			$this->can_url = $url;
		}

		$this->db->executeQuery($this->SQL('add url'), $this->can_url);
	}

	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	public function removeUrl($id) {
		if(is_numeric($id))
			$this->getById($id);
		else
			$this->getbyCanUrl($id);

		$this->db->executeQuery($this->SQL('remove url'), array($this->can_url['can_url']));
	}

	/**
	 * installs the model
	 *
	 * @return void
	 * @author 
	 **/
	public function installModel() {
		$this->db->executeQuery($this->SQL('create model'));
	}

	public function updateModel() {;}

	public function removeModel() {;}


	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	public static function SQL($id=null) {
		global $ef;

		$db_prefix = $ef->cfg['db']['db_prefix'];
		$tableName = $db_prefix . 'can_url';
		$query = array(
				'create model' => "CREATE TABLE IF NOT EXISTS {$tableName} (
										id INT PRIMARY KEY AUTO_INCREMENT,
										can_url VARCHAR(255) UNIQUE NOT NULL,
										real_url VARCHAR(255) NOT NULL,

									)ENGINE=InnoDB;
									",
				'get all'		=> "SELECT * FROM {$tableName};",
				'add url'		=> "INSERT INTO {$tableName}(can_url, real_url)VALUES(:can_url,:real_url);",
				'remove url'	=> "DELETE FROM {$tableName} WHERE can_url=? LIMIT 1;",
				'get can_url from real_url'		=> "SELECT can_url FROM {$tableName} WHERE real_url = ?;",
				'get real_url from can_url'	=> "SELECT real_url FROM {$tableName} WHERE can_url = ?;",
				'get url from id'		=> "SELECT * FROM {$tableName} WHERE id = ?;",
			);

		if(!isset($query[$id])) {
	  		throw new Exception('#class error: Out of range. Query = @id');

		}
		return $query[$id];

	}
}