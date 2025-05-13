<?php
class Contrato_resultado_controle_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, &$count, $args=array() )
	{
		// mount query
		$sql = "
		SELECT cd_divisao, nome, fl_avaliou
  FROM consultas.contrato_resultado_controle
 WHERE cd_contrato_avaliacao={cd_contrato_avaliacao}
		";

		// parse query ...
		esc( "{cd_contrato_avaliacao}", $args["avaliacao"], $sql, "int" );


		// echo "<pre>$sql</pre>";

		// return result ...
		$result = $this->db->query($sql);
	}
}
