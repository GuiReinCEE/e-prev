<?php
class Gravacoes_callcenter_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		$qr_sql = "
				SELECT ac.nome_arquivo, 
				       TO_CHAR(ac.dt_gravacao, 'DD/MM/YYYY HH24:MI') AS dt_gravacao,
					   ac.cd_atendimento,
                       ac.cd_empresa,
                       ac.cd_registro_empregado,
					   ac.seq_dependencia,
					   'PADRAO' AS tp_arquivo
				  FROM projetos.arquivos_call ac
	             WHERE ac.cd_empresa            =  ".$args['cd_empresa']."
	               AND ac.cd_registro_empregado =  ".$args['cd_registro_empregado']."
				   {PERIODO_DATA}
	            
				UNION
				 
				SELECT TO_CHAR(ac.data,'YYYY_MM_DD') || '/' || ac.nome_arquivo AS nome_arquivo, 
				       TO_CHAR(ac.dt_gravacao, 'DD/MM/YYYY HH24:MI') AS dt_gravacao,
                       ac.cd_atendimento,
					   ac.cd_empresa,
                       ac.cd_registro_empregado,
					   ac.seq_dependencia,
					   'PADRAO' AS tp_arquivo
				  FROM projetos.arquivos_call_teledata ac
	             WHERE ac.cd_empresa            =  ".$args['cd_empresa']."
	               AND ac.cd_registro_empregado =  ".$args['cd_registro_empregado']."
				   {PERIODO_DATA}
				
				
				UNION
				
				SELECT TRIM(COALESCE('xcally/' || ac.nome_arquivo,'')) AS nome_arquivo, 
				       TO_CHAR(ac.dt_gravacao, 'DD/MM/YYYY HH24:MI') AS dt_gravacao,
					   ac.cd_atendimento,
                       ac.cd_empresa,
                       ac.cd_registro_empregado,
					   ac.seq_dependencia,
					   'XCALLY' AS tp_arquivo
				  FROM projetos.arquivos_call_xcally ac
	             WHERE ac.cd_empresa            =  ".$args['cd_empresa']."
	               AND ac.cd_registro_empregado =  ".$args['cd_registro_empregado']."
				   {PERIODO_DATA}				   
				   
	             ORDER BY dt_gravacao DESC				 
		       ";
			   
		if((trim($args["dt_gravacao_ini"]) != "") and (trim($args["dt_gravacao_fim"]) != ""))
		{
			$periodo = "AND DATE_TRUNC('day', ac.dt_gravacao) BETWEEN TO_DATE('{dt_ini}','DD/MM/YYYY') AND TO_DATE('{dt_fim}','DD/MM/YYYY')";
			$periodo = str_replace("{dt_ini}", $args["dt_gravacao_ini"],$periodo);
			$periodo = str_replace("{dt_fim}", $args["dt_gravacao_fim"],$periodo);
			$qr_sql = str_replace("{PERIODO_DATA}", $periodo, $qr_sql);
		}
		else
		{
			$qr_sql = str_replace("{PERIODO_DATA}", "", $qr_sql);
		}
			   
		$result = $this->db->query($qr_sql);
		$count = $result->num_rows();
	}
}
?>