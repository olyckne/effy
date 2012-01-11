<?php

/**
*  
*/
class Page_model extends Model implements useSQL
{
	
	public $page = array(
			'id'           => null,
			'key'          => null,
			'title'        => null,
			'content'      => null,
			'owner'        => null,
			'created'      => null,
			'published'    => null,
			'modified'     => null,
			'deleted'      => null,
			'draftTitle'   => null,
			'draftContent' => null,
			'url'          => null,
			'can_url'      => null,
		);


	function __construct() {
		parent::__construct();

		$this->can_url = new CanonicalUrl();

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

	public function removeModel() {;}

	public function updateModel() {;}

	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	public static function SQL($id = null) {
		global $ef;

		$db_prefix = $ef->cfg['db']['db_prefix'];
		$tableName = $db_prefix . 'pages';
		$query = array(
				'create model' => "CREATE TABLE IF NOT EXISTS {$tableName} (
									id INT PRIMARY KEY AUTO_INCREMENT,
									key VARCHAR(255) NOT NULL UNIQUE,
									title TINYTEXT,
									content LONGTEXT,
									owner INT NOT NULL,
									created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
									published TIMESTAMP,
									modified TIMESTAMP
									)ENGINE=InnoDB;",
				'add page' 		=> "INSERT INTO `{$tableName}`(title, content, `key`, owner, created)VALUES(?,?,?,?, NOW());",
				'publish page' 	=> "UPDATE {$tableName} SET published=NOW() WHERE id=?;",
				'update page' 	=> "UPDATE {$tableName} SET title=?, content=?, `key`=?, modified=NOW() WHERE id=?;",
				'remove page'	=> "",
				'get all'		=> "SELECT * FROM {$tableName};",
				'get by key'	=> "SELECT * FROM {$tableName} WHERE `key` = ?;",
				'get by id'		=> "SELECT * FROM {$tableName} WHERE `id` = ?;",
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
	public function getByKey($key) {
		return $this->load('get by key', $key);
	}

	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	public function getById($id) {
		return $this->load("get by id", $id);
	}


	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	public function load($sql, $whereValue) {
		global $ef;
		$this->page =  $this->db->executeAndFetchAll($this->SQL($sql),array($whereValue));

		if(isset($this->page[0])) {
			$this->page = $this->page[0];

			$this->page['can_url'] = $this->db->executeAndFetchAll(CanonicalUrl::SQL('get can_url from real_url'), array('page/view/'.$this->page['key']));

			$this->page['can_url'] = isset($this->page['can_url'][0]) ? $this->page['can_url'][0]['can_url'] : 'page/view/'.$this->page['key'];
			$this->page['url'] = $ef->cfg['config-db']['general']['siteurl'] . $this->page['can_url'];
		}
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



		$this->page['owner'] = 1;
		if(isset($this->page['id'])) {
			$this->db->executeQuery($this->SQL('update page'), array($this->page['title'],$this->page['content'],$this->page['key'],$this->page['id']))	;
		}
		else {
			$this->db->executeQuery($this->SQL('add page'), array($this->page['title'], $this->page['content'], $this->page['key'], $this->page['owner']));
			$this->db->executeQuery($this->SQL('publish page'), $this->db->lastInsertId());

		}
	}

}