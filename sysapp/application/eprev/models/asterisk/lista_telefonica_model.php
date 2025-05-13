<?php
class Lista_telefonica_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	public function listar($args = array())
	{
		$qr_sql = "
			SELECT nome, 
				   nr_ramal, 
				   grupo, 
				   email, 
				   default_address, 
				   default_address_type
			  FROM asterisk.lista_telefonica;";
			  
		return $this->db->query($qr_sql)->result_array();
	}
}