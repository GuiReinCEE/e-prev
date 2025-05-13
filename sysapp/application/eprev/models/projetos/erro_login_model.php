<?php
class Erro_login_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		// mount query
		$sql = "
		SELECT 

		empresa
		, re
		, erro
		, TO_CHAR(data, 'DD/MM/YYYY HH24:MI:SS') as data
		, senha 

		FROM public.erros_login_autoatendimento

		WHERE re = {cd_registro_empregado}

		ORDER BY data DESC
		";

		// parse query ...
		esc( "{cd_registro_empregado}", $args["cd_registro_empregado"], $sql, "int" );


		// echo "<pre>$sql</pre>";

		// return result ...
		$result = $this->db->query($sql);
	}
}
?>