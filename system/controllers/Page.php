<?php



/**
*  Controller for making some posts
*/
class Page extends Controller implements Active
{
	protected $page_model;

	function __construct()
	{
		parent::__construct();

		$this->page_model = $this->load->model('Page_model');

		$this->theme->editor();

		global $ef;
	}


	/**
	 * every controller must have an index function
	 *
	 * @return void
	 * @author 
	 **/
	public function index() {
		//$this->view('home');

	}

	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	public function create() {
		Auth::LoginAndRedirect();
		$this->pageForm();
	}

	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	public function edit($key) {

		Auth::LoginAndRedirect();

		$this->page_model->getByKey($key);

		$this->pageForm();
	}


	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	public function listAll() {
		Auth::LoginAndRedirect();

		$html = "";

		$html .= "<p><a href='{$this->url}page/create' class='btn'>New page</a></p>";

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
						<a href='{$this->url}page/edit/{$page['key']}'>	{$page['title']} </a>
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
	 * view - Shows a page
	 *
	 * @return void
	 * @author 
	 **/
	public function view($key) {
		$page = $this->page_model->getByKey($key);

		$this->theme->siteTitle .= " - " . $page['title'];

		$this->theme->addView("<h1>{$page['title']}</h1>");
		$this->theme->addView($page['content']);
	}

	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	private function pageForm() {
		global $ef;

		extract($this->page_model->page);

		$publishBtn = isset($id) ? "Update" : "Publish";
		$html = <<<EOD
		<form action="{$this->url}page/action" method="POST">
		<input type="hidden" name="id" value="{$id}">
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
				<td>
				<label for="key">Page key: </label>
				</td>
				<td>
				<input type="text" name="key" id="key" value="{$key}">
				<span class="description">url: <a href='{$url}'>{$url}</span>
				</td>
			</tr>
			<tr>
				<td></td>
				<td>
				<textarea name="content" id="content" cols=300 rows=20>{$content}</textarea>
				</td>
			</tr>

			<tr>
				<th>
				<input type="submit" value="{$publishBtn}">
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
		global $ef;

		$id = $_POST['id'];
		$this->page_model->getById($id);
		$this->page_model->page['title']   = $_POST['title'];
		$this->page_model->page['key']     = $_POST['key'];
		$this->page_model->page['content'] = $_POST['content'];

		$this->page_model->Save();

		$ef->req->redirectTo('admin', 'page', "edit/{$this->page_model->page['key']}");
	}
}