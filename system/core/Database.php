<?php


/**
* 
*/
class Database
{
	protected $db;
	protected $stmt;
	public $res;

	public $db_prefix;
	public $query;

	function __construct()
	{	

		global $ef;
		
		$cfg = (func_num_args() == 1) ? func_get_arg(0) : $ef->cfg['db'];
		extract($cfg);
		$this->db_prefix = $db_prefix;


		try {
			$this->db = new PDO($dsn, $username, $password, $driver_options);

			$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch(PDOException $e) {
			echo "Connection failed " . $e->getMessage();
		}

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

		$this->res = $this->stmt->fetchAll(PDO::FETCH_ASSOC);

		return $this->res;	
	}

	/**
	 * executeQuery executes, well, a query
	 *
	 * @return void
	 * @author 
	 **/
	public function executeQuery($query, $params = null) {
		$this->stmt = $this->db->prepare($query);

		return $this->stmt->execute($params);
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


	/**
	 * gets the last inserted ID
	 *
	 * @return void
	 * @author 
	 **/
	public function lastInsertId() {
		return $this->db->lastInsertId();
	}

}