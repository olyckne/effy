<?php

/**
*  
*/
class Page_model implements useSQL
{
	
	public $db;


	public $page = array(
			'id' => -1,
			'url' => null,
			'title' => null,
			'content' => null,
			'owner' => null,
			'created' => null,
			'published' => null,
			'modified' => null,
			'deleted'  => null,
		);

	function __construct() {
		global $ef;

		$this->db = $ef->db;
		$this->install();
	}

	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	public function install() {
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
				'create model' => "CREATE TABLE IF NOT EXISTS {$db_prefix}pages (
									id INT PRIMARY KEY AUTO_INCREMENT,
									url VARCHAR(255) NOT NULL,
									title TINYTEXT,
									content LONGTEXT,
									owner INT NOT NULL,
									created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
									published TIMESTAMP,
									modified TIMESTAMP
									)ENGINE=InnoDB;",
				'add page' 		=> "INSERT INTO `{$db_prefix}pages`(title, content, url, owner, created)VALUES(:title,:content,:url,:owner, NOW());",
				'edit page' 	=> "",
				'remove page'	=> "",
				'get all'		=> "SELECT * FROM {$db_prefix}pages;",
				'get page'		=> "SELECT * FROM {$db_prefix}pages WHERE id = ?;",
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
	public function getAll() {
		$pages = $this->db->executeAndFetchAll($this->SQL('get all'));

		return $pages;
	}


	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	public function getById($id) {
		$this->page =  $this->db->executeAndFetchAll($this->SQL('get page'), array($id));
		$this->page = $this->page[0];
			
		return $this->page;
	}

	/**
	 * save a page
	 *
	 * @return void
	 * @author 
	 **/
	public function save() {
		global $ef;
		$this->page['url'] = $this->page['title'];
		$this->page['owner'] = 1;

		print_r('<pre>');
		print_r($this->page);
		print_r('</pre>');


		$this->db->executeQuery($this->SQL('add page'), $this->page);
	}
}