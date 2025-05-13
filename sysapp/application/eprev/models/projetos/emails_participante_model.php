<?php
class Emails_participante_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar(&$result, $args=array())
	{
		$qr_sql = "
					SELECT ee.cd_email, 
					       ee.cd_empresa,
						   ee.cd_registro_empregado,
						   ee.seq_dependencia,
						   ee.para,
						   ee.cc,
						   ee.cco,
					       TO_CHAR(ee.dt_envio, 'dd/mm/yyyy HH24:MI') AS dt_email, 
					       TO_CHAR(ee.dt_email_enviado, 'dd/mm/yyyy HH24:MI') AS dt_envio, 
					       TO_CHAR(ee.dt_schedule_email, 'dd/mm/yyyy HH24:MI') AS dt_schedule_email, 
						   ee.assunto,
						   ee.fl_retornou AS fl_retorno,
						   ee.fl_visualizado
                      FROM projetos.envia_emails ee		
                      JOIN public.participantes p
                        ON p.cd_empresa            = ee.cd_empresa
                       AND p.cd_registro_empregado = ee.cd_registro_empregado
                       AND p.seq_dependencia       = ee.seq_dependencia
                     WHERE 1 = 1
                       ".(trim($args['cd_empresa']) != '' ? "AND ee.cd_empresa = ".$args['cd_empresa'] : "")."
                       ".(trim($args['cd_registro_empregado']) != '' ? "AND ee.cd_registro_empregado = ".$args['cd_registro_empregado'] : "")."
                       ".(trim($args['seq_dependencia']) != '' ? "AND ee.seq_dependencia = ".$args['seq_dependencia'] : "")."
                       ".(trim($args['cpf']) != '' ? "AND funcoes.format_cpf(p.cpf_mf::bigint) = '".trim($args['cpf'])."'" : "")."
					  {PERIODO_DATA_EMAIL}
					  {PERIODO_DATA_ENVIO}
					 ORDER BY COALESCE(ee.dt_email_enviado,ee.dt_envio) DESC, 
					          ee.assunto ASC 		
		       ";
			   
		if((trim($args["dt_email_ini"]) != "") and (trim($args["dt_email_fim"]) != ""))
		{
			$periodo = "AND DATE_TRUNC('day', ee.dt_envio) BETWEEN TO_DATE('{dt_ini}','DD/MM/YYYY') AND TO_DATE('{dt_fim}','DD/MM/YYYY')";
			$periodo = str_replace("{dt_ini}", $args["dt_email_ini"],$periodo);
			$periodo = str_replace("{dt_fim}", $args["dt_email_fim"],$periodo);
			$qr_sql = str_replace("{PERIODO_DATA_EMAIL}", $periodo, $qr_sql);
		}
		else
		{
			$qr_sql = str_replace("{PERIODO_DATA_EMAIL}", "", $qr_sql);
		}
		
		if((trim($args["dt_envio_ini"]) != "") and (trim($args["dt_envio_fim"]) != ""))
		{
			$periodo = "AND DATE_TRUNC('day', ee.dt_email_enviado) BETWEEN TO_DATE('{dt_ini}','DD/MM/YYYY') AND TO_DATE('{dt_fim}','DD/MM/YYYY')";
			$periodo = str_replace("{dt_ini}", $args["dt_envio_ini"],$periodo);
			$periodo = str_replace("{dt_fim}", $args["dt_envio_fim"],$periodo);
			$qr_sql = str_replace("{PERIODO_DATA_ENVIO}", $periodo, $qr_sql);
		}
		else
		{
			$qr_sql = str_replace("{PERIODO_DATA_ENVIO}", "", $qr_sql);
		}		
			
		#echo "<pre>$qr_sql</pre>";	
		$result = $this->db->query($qr_sql);
	}
}
?>