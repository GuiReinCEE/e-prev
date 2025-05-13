<?php
class Contrato_formulario_grupo_model extends Model
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

cd_contrato_formulario_grupo, ds_contrato_formulario_grupo, nr_ordem,
to_char(a.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
b.ds_contrato_formulario

FROM projetos.contrato_formulario_grupo a
JOIN projetos.contrato_formulario b
ON a.cd_contrato_formulario=b.cd_contrato_formulario

WHERE a.dt_exclusao IS NULL
AND upper(b.ds_contrato_formulario) LIKE upper('%{ds_contrato_formulario}%')
		";

		// parse query ...
		esc( "{ds_contrato_formulario}", $args["formulario"], $sql );


		// echo "<pre>$sql</pre>";

		// return result ...
		$result = $this->db->query($sql);
	}
}
