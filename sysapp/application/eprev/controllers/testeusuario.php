<?php
class testeusuario extends Controller
{
	function __construct()
	{
		parent::Controller();
	}
	
	function index()
	{
		$this->load->view('testeusuario');
	}
}
?>