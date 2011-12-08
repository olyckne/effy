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
		$ef = Effy::GetInstance();

		print_r('<pre>');
		print_r($ef);
		print_r('</pre>');
		$this->dsn = $ef->config['db']['dsn'];
		$this->user = $ef->config['db']['username'];
		$this->pass = $ef->config['db']['password'];
		$this->driver_options = $ef->config['db']['driver_options'];
		$this->db_prefix = $ef->config['db']['db_prefix'];

		echo $this->dsn;

		try {
			$pdo = new PDO($this->dsn, $this->user, $this->pass, $this->driver_options);
		} catch(PDOException $e) {
			echo "Connection failed " . $e->getMessage();
		}
	}


}