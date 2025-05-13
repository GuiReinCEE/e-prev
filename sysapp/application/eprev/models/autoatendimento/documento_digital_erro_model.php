<?php
class Documento_digital_erro_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	public function listar($args = array())
	{
		$qr_sql = "
			SELECT d.ds_documento_digital_erro,
			       d.cd_registro_empregado,
				   d.cd_empresa,
				   d.seq_dependencia,
				   TO_CHAR(d.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   p.nome AS nome
			  FROM autoatendimento.documento_digital_erro d
			  JOIN public.participantes p
			    ON p.cd_empresa            = d.cd_empresa
			   AND p.cd_registro_empregado = d.cd_registro_empregado
			   AND p.seq_dependencia       = d.seq_dependencia
			 WHERE 1 = 1
			   ".(trim($args['cd_empresa']) != '' ? "AND d.cd_empresa = ".intval($args['cd_empresa']) : "")."			   
			   ".(trim($args['cd_registro_empregado']) != '' ? "AND d.cd_registro_empregado = ".intval($args['cd_registro_empregado']) : "")."			   
			   ".(trim($args['seq_dependencia']) != '' ? "AND d.seq_dependencia = ".intval($args['seq_dependencia']) : "")."
			   ".(((trim($args['dt_inclusao_ini']) != '') AND (trim($args['dt_inclusao_fim']) != '')) ? " AND DATE_TRUNC('day', d.dt_inclusao) BETWEEN TO_DATE('".$args['dt_inclusao_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_inclusao_fim']."', 'DD/MM/YYYY')" : "").";";
			   
		return $this->db->query($qr_sql)->result_array();
	}
}