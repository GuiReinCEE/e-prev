<?php
class Instancias_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		// mount query
		$sql = "
		SELECT * FROM projetos.instancias
		";

		// parse query ...
		

		// echo "<pre>$sql</pre>";

		// return result ...
		$result = $this->db->query($sql);
	}

	function carregar()
	{}

	function salvar()
	{}

	function excluir()
	{}
}
?>