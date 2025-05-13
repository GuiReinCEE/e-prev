<?php
class Programas_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		// mount query
		$sql = "
		SELECT DISTINCT 

programa
, cd_divisao 
, definicao
, to_char(dt_cadastro, 'DD/MM/YYYY') as dt_cadastro

FROM 

projetos.programas

WHERE

upper(programa) LIKE upper('%{programa}%')
		";

		// parse query ...
		esc( "{programa}", $args["programa"], $sql );


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