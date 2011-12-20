<?php

/**
* 
*/
class database
{
	
	protected $dsn;
	protected $user;
	protected $pass;
	protected $driver_options;
	protected $db_prefic;

	function __construct()
	{
		global $ef;

		$this->dsn = $ef->cfg['db']['dsn'];
		$this->user = $ef->cfg['db']['username'];
		$this->pass = $ef->cfg['db']['password'];
		$this->driver_options = $ef->cfg['db']['driver_options'];
		$this->db_prefix = $ef->cfg['db']['db_prefix'];


		$this->connect();
	}

	public function connect() {
		try {
			$pdo = new PDO($this->dsn, $this->user, $this->pass, $this->driver_options);
		} catch(PDOException $e) {
			echo "Connection failed " . $e->getMessage();
		}
	}
	

}