<?php

/**
* Controller for handling canonical URLs
*/
class Canonical extends Controller implements active
{
	protected $canonical_model;



	function __construct() {
		parent::__construct();

		$this->canonical_model = new CanonicalUrl();

		Auth::loginAndRedirect('admin/canurls');
	}

	/**
	 * Every controller must have an index function
	 *
	 * @return void
	 * @author 
	 **/
	public function index() {
		global $ef;
		$html = "<h2>Canonical urls</h2>";

		$canurls = $ef->db->executeAndFetchAll(CanonicalUrl::SQL('get all'));

		$html .= "<table><thead><tr>
				<th>Canonical url</th>
				<th>Real url</th>
				</tr></thead>";
		foreach ($canurls as $url) {
			$html .= "<tr>
					<td>{$url['can_url']}</td>
					<td>{$url['real_url']}
					<span class='description'><a href='{$this->url}canurls/remove/{$url['id']}' class='danger'>X</a></span>
					</td>
					</tr>";
		}
		$html .= "<tr><td><a href='{$this->url}canurls/add' class='btn'>Add canonical url</a></td><td></td></tr>";
		$html .= "</table>";

		$this->theme->addView($html);
	}


	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	public function add() {
		$html = "<h2>Add canonical url</h2>";


		$html .= <<<EOD
		<table class="form-table">
			<form action="{$this->url}canurls/doAdd" method="POST">
			<thead>
			<tr>
			<th>Canonical url</th>
			<th>Real url</th>
			</tr>
			</thead>

			<tr>
			<td><input type="text" name="can_url" id="can_url"></td>
			<td><input type="text" name="real_url" id="real_url"></td>
			</tr>

			<tr>
			<td><input type="submit" value="Add" class="btn"></td>
			<td></td>
			</tr>
			</form>
		</table>

EOD;


		$this->theme->addView($html);
	}


	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	public function doAdd() {
		global $ef;

		$this->canonical_model->can_url['can_url'] = htmlentities($_POST['can_url']);
		$this->canonical_model->can_url['real_url'] = htmlentities($_POST['real_url']);

		$this->canonical_model->addUrl();

		$ef->req->redirectTo('admin/canurls');

	}



	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	public function remove($id=null) {
		global $ef;
		if(!isset($id)) {
			
		}

		$this->canonical_model->removeUrl($id);

		$ef->req->redirectTo('admin', 'canurls');
	}
}