<?php

/**
* 
*/
abstract class Model
{
	
	public $db;

	public function __construct()
	{
		$this->db = new Database();	
	}




}