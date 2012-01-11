<?php

/**
* 
*/
abstract class Model implements installable
{
	
	public $db;

	public function __construct()
	{
		$this->db = new Database();	
	}




}