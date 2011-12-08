<?php

/**
* 
*/
class test extends Controller
{
	
	function __construct()
	{
		# code...
	}

	public function index() {
		echo "INDEX!";
	}

	public function testar($value='') {
		print_r('<pre>');
		print_r($value);
		print_r('</pre>');
	}
}