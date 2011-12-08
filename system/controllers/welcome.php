<?php 

/**
* 
*/
class welcome extends Controller
{
	private $model;
	public function index() {
		$this->model = $this->load->model('welcome_model');
		$this->db = $this->load->helper('database');
	}

	public function test($value='') {
		echo "test: ". $value;
	}
}