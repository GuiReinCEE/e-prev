<?php
class Raiz_indicadores_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		// mount query
		$sql = "
		SELECT cd_indic, nome_indic
FROM acs.raiz_indicadores
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