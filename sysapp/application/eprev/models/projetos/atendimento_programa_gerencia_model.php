<?php
class Atendimento_programa_gerencia_model extends Model
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

l.descricao AS ds_programa,
uc.divisao AS cd_divisao,
uc.nome AS ds_usuario,
atg.cd_atendimento_programa_gerencia, 
atg.cd_programa, 
atg.cd_usuario

FROM

public.listas l,
projetos.atendimento_programa_gerencia atg,
projetos.usuarios_controledi uc

WHERE

l.categoria = 'PRFC'
AND l.codigo = atg.cd_programa
AND atg.cd_usuario = uc.codigo
AND atg.dt_exclusao IS NULL

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