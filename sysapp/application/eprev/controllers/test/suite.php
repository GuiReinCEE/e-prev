<?php
class suite extends Controller
{
	function __construct()
	{
		parent::Controller();
	}

	function index()
	{
		$this->load->helper('menu');
		echo menu_extjs_start( 8 );
	}


	function performance()
	{
		$this->db->query("");
	}
}
?>