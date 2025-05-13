<?php
class newlayout extends Controller
{
	function __construct()
	{
		parent::Controller();
	}

	function index()
	{
		$this->load->view('newlayout.php');
	}
}
?>