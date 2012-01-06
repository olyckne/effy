<?php


/**
* 
*/
class Database
{
	protected $db;
	protected $stmt;
	public $db_prefix;
	public $query;

	function __construct()
	{	

		global $ef;
		
		$cfg = (func_num_args() == 1) ? func_get_arg(0) : $ef->cfg['db'];
		extract($cfg);
		$this->db_prefix = $db_prefix;

		$this->setQuery();

		try {
			$this->db = new PDO($dsn, $username, $password, $driver_options);

			$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch(PDOException $e) {
			echo "Connection failed " . $e->getMessage();
		}

	}

	/**
	 * setQuery sets some basic queries in the query-variable
	 *
	 * @return void
	 * @author 
	 **/
	public function setQuery() {

		$this->query = array(
				'create ef::' => "CREATE TABLE IF NOT EXISTS {$this->db_prefix}Effy(ef_module VARCHAR(255),ef_key VARCHAR(255), ef_value TEXT, PRIMARY KEY(ef_module, ef_key))ENGINE=InnoDB;",
				'save ef::config' => "UPDATE {$this->db_prefix}Effy SET `ef_value` = ? WHERE `ef_module` = 'effy' AND `ef_key` = 'config';",
				'load ef::config' => "SELECT ef_value FROM {$this->db_prefix}Effy WHERE ef_module = 'effy' AND ef_key = 'config';",
			);


	}
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	public function executeAndFetchAll($query, $params = null) {
		$this->stmt = $this->db->prepare($query);

		$this->stmt->execute($params);


		return $this->stmt->fetchAll();

	}

	/**
	 * executeQuery executes, well, a query
	 *
	 * @return void
	 * @author 
	 **/
	public function executeQuery($query, $params = null) {
		$this->stmt = $this->db->prepare($query);

		$this->stmt->execute($params);
	}


	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	public function saveCfg() {
		global $ef;
		$this->executeQuery($this->query['save ef::config'], array(serialize($ef->cfg['config-db'])));
	}

}