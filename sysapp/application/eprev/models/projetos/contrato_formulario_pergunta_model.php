<?php
class Contrato_formulario_pergunta_model extends Model
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

  a.cd_contrato_formulario_pergunta
  , a.ds_contrato_formulario_pergunta
  , a.nr_ordem
  , to_char(a.dt_inclusao, 'DD/MM/YYYY') as dt_inclusao
  , b.nome as nome_usuario_inclusao
  , c.ds_contrato_formulario_grupo as grupo
  , d.ds_contrato_formulario as formulario

FROM 

  projetos.contrato_formulario_pergunta a

  JOIN projetos.usuarios_controledi b 
  on a.cd_usuario_inclusao=b.codigo

  JOIN projetos.contrato_formulario_grupo c 
  on a.cd_contrato_formulario_grupo=c.cd_contrato_formulario_grupo

  JOIN projetos.contrato_formulario d
  on d.cd_contrato_formulario=c.cd_contrato_formulario

WHERE 

  a.cd_contrato_formulario_grupo={cd_contrato_formulario_grupo}
		";

		// parse query ...
		esc( "{cd_contrato_formulario}", $args["cd_contrato_formulario"], $sql, "int" );
esc( "{cd_contrato_formulario_grupo}", $args["cd_contrato_formulario_grupo"], $sql, "int" );


		// echo "<pre>$sql</pre>";

		// return result ...
		$result = $this->db->query($sql);
	}
}
