<?php

class Formulario_fatca_model extends Model
{
	function __construct()
  	{
    	parent::Model();
  	}

	public function listar($args = array())
	{
		$qr_sql = "
		
			SELECT ff.cd_empresa,
				   ff.cd_registro_empregado,
				   ff.seq_dependencia,
				   (CASE WHEN ff.id_us_person = 'S' THEN 'Sim'
						ELSE 'Não'
				   END) AS id_us_person,
				   p.nome,
				   TO_CHAR(ff.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao
			  FROM autoatendimento.formulario_fatca ff
			  JOIN public.participantes p
				ON p.cd_empresa            = ff.cd_empresa
			   AND p.cd_registro_empregado = ff.cd_registro_empregado
			   AND p.seq_dependencia       = ff.seq_dependencia
			 WHERE 1 = 1
			   ".(trim($args['cd_empresa']) != '' ? "AND ff.cd_empresa = ".intval($args['cd_empresa']) : "")."			   
			   ".(trim($args['cd_registro_empregado']) != '' ? "AND ff.cd_registro_empregado = ".intval($args['cd_registro_empregado']) : "")."			   
			   ".(trim($args['seq_dependencia']) != '' ? "AND ff.seq_dependencia = ".intval($args['seq_dependencia']) : "")."
			   ".(((trim($args['dt_ini']) != '') AND (trim($args['dt_fim']) != '')) ? " AND DATE_TRUNC('day', ff.dt_inclusao) BETWEEN TO_DATE('".$args['dt_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_fim']."', 'DD/MM/YYYY')" : "")."
			 ORDER BY ff.dt_inclusao;";
			 
		return $this->db->query($qr_sql)->result_array();
	}
}