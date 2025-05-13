<?php
class test extends Controller
{
	function __construct()
	{
		parent::Controller();
	}
	
	function index()
	{
	}
	
	function teste()
	{
		$this->load->view('teste');
	}
}
?>