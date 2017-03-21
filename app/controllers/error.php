<?php
class error extends Controller{

	private $exportURL;

	public function __construct()
	{
		parent:: __construct();
		$this->exportURL = "#"; 
	}

	public function index()
	{
		$data = array("menu" => $this->userRole,"exportURL" => $this->exportURL,);
		$this->view('error', $data);
	}
}