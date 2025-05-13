<?php
class Habilidades_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		// mount query
		$sql = "
		SELECT *
FROM projetos.habilidades
WHERE upper(descricao) like upper('%{descricao}%')
		";

		// parse query ...
		esc( "{descricao}", $args["descricao"], $sql );


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