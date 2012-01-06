<?php



/**
*  Controller for making some posts
*/
class CtrlContent extends Controller
{
	protected $page_model;

	function __construct()
	{
		parent::__construct();

		$this->page_model = $this->load->model('page_model');
	}


	/**
	 * every controller must have an index function
	 *
	 * @return void
	 * @author 
	 **/
	public function index() {
		$html = "";

		$html .= "<p><a href='pages/newPage'>New page</a></p>";

		$pages = $this->page_model->getAll();

		$html .= "<table>
					<thead>
					<tr>
						<th>Title</th>
						<th>Summary</th>
					</tr>
					</thead>
					";

		foreach($pages as $page) {
			$summary = strip_tags(substr($page['content'], 0, 50)) . '...';
			$html .= "<tr>
						<td>
						<a href='pages/edit/{$page['id']}'>	{$page['title']} </a>
						</td>
						<td>
							{$summary}
						</td>
					</tr>";
		}

		$html .= "</table>";

		$this->theme->addView($html);
	}

	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	public function newPage() {

		$this->pageForm();
	}

	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	public function edit($id) {

		$page = $this->page_model->getById($id);

		$this->pageForm();
	}


	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	private function pageForm() {

		extract($this->page_model->page);
		$html = <<<EOD
		<form action="{$this->url}pages/action" method="POST">
		<table id="post-form" class="form-table">
			<tr>
				<td>
				<label for="title">Page title: </label>
				</td>
				<td>
				<input type="text" name="title" id="title" value='{$title}'>
				</td>
			</tr>
			<tr>
				<td></td>
				<td>
				<textarea name="content" id="content">{$content}</textarea>
				</td>
			</tr>

			<tr>
				<th>
				<input type="submit" value="Save draft">
				<input type="submit" value="Publish">
				</th>
			</tr>
		</table>
		</form>
EOD;

		$this->theme->addView($html);
	}

	/**
	 * takes care of the action from form
	 *
	 * @return void
	 * @author 
	 **/
	public function action() {
		$page = &$_POST;
		$this->page_model->page = $page;
		
		$this->page_model->Save();
	}
}