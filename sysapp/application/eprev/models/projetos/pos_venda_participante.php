<?php
class Pos_venda_participante extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function dropdown_usuario_cadastro()
	{
		$query = $this->db->query("SELECT DISTINCT codigo as value, nome as text FROM projetos.usuarios_controledi u JOIN projetos.pos_venda_participante pvp ON u.codigo=pvp.cd_usuario_inicio ORDER BY nome; ");
		if($query)
		{
			$collection = $query->result_array();
			if(!$collection)
			{
				$collection = array();
			}
		}
		else
		{
			$collection = array();
		}

		return $collection;
	}

	function dropdown_usuario_encerramento()
	{
		$query = $this->db->query("SELECT DISTINCT codigo as value, nome as text FROM projetos.usuarios_controledi u JOIN projetos.pos_venda_participante pvp ON u.codigo=pvp.cd_usuario_final ORDER BY nome;");
		if($query)
		{
			$collection = $query->result_array();
			if(!$collection)
			{
				$collection = array();
			}
		}
		else
		{
			$collection = array();
		}

		return $collection;
	}

	function lista_relatorio( &$result, &$count, $args=array() )
	{
		$sql_select = "
						SELECT pvp.cd_pos_venda_participante,
							   p.cd_empresa,
							   p.cd_registro_empregado,
							   p.seq_dependencia,
							   p.nome,
							   TO_CHAR( pvp.dt_inicio, 'DD/MM/YYYY' ) AS dt_inicio,
							   TO_CHAR( pvp.dt_final, 'DD/MM/YYYY' ) AS dt_final,
							   uc.divisao as divisao_usuario_inicio,
							   uc.nome as nome_usuario_inicio,
							   ue.divisao as divisao_usuario_final,
							   ue.nome as nome_usuario_final,
							   pvp.cd_atendimento,
							   TO_CHAR(t.dt_ingresso_eletro,'DD/MM/YYYY') AS dt_ingresso,
							   (SELECT TO_CHAR(MAX(pvpa.dt_inclusao),'DD/MM/YYYY HH24:MI') 
							      FROM projetos.pos_venda_participante_acompanhamento pvpa
								 WHERE pvpa.dt_exclusao IS NULL
								   AND pvpa.cd_pos_venda_participante = pvp.cd_pos_venda_participante) AS dt_acompanhamento
						  FROM projetos.pos_venda_participante pvp
				          JOIN public.participantes p
				            ON pvp.cd_empresa            = p.cd_empresa 
				           AND pvp.cd_registro_empregado = p.cd_registro_empregado 
				           AND pvp.seq_dependencia       = p.seq_dependencia
				          JOIN public.titulares t
				            ON pvp.cd_empresa            = t.cd_empresa 
				           AND pvp.cd_registro_empregado = t.cd_registro_empregado 
				           AND pvp.seq_dependencia       = t.seq_dependencia						   
				          JOIN projetos.usuarios_controledi uc
				            ON uc.codigo = pvp.cd_usuario_inicio
				          LEFT JOIN projetos.usuarios_controledi ue
				            ON ue.codigo = pvp.cd_usuario_final
						 WHERE pvp.dt_exclusao IS NULL
						 ".(trim($args['cd_empresa']) != "" ? "AND pvp.cd_empresa = ".(int)$args['cd_empresa'] : "")."
						 ".(trim($args['cd_registro_empregado']) != "" ? "AND pvp.cd_registro_empregado = ".(int)$args['cd_registro_empregado'] : "")."
						 ".(trim($args['seq_dependencia']) != "" ? "AND pvp.seq_dependencia = ".(int)$args['seq_dependencia'] : "")."
						 ".(((trim($args['cadastro_inicio']) != "") and (trim($args['cadastro_fim']) != ""))? "AND DATE_TRUNC('day',pvp.dt_inicio) BETWEEN TO_DATE('".$args['cadastro_inicio']."','DD/MM/YYYY') AND TO_DATE('".$args['cadastro_fim']."','DD/MM/YYYY')" : "")."
						 ".(((trim($args['encerramento_inicio']) != "") and (trim($args['encerramento_fim']) != ""))? "AND DATE_TRUNC('day',pvp.dt_final) BETWEEN TO_DATE('".$args['encerramento_inicio']."','DD/MM/YYYY') AND TO_DATE('".$args['encerramento_fim']."','DD/MM/YYYY')" : "")."
						 ".(((trim($args['dt_ingresso_ini']) != "") and (trim($args['dt_ingresso_fim']) != ""))? "AND DATE_TRUNC('day',t.dt_ingresso_eletro) BETWEEN TO_DATE('".$args['dt_ingresso_ini']."','DD/MM/YYYY') AND TO_DATE('".$args['dt_ingresso_fim']."','DD/MM/YYYY')" : "")."
						 ".(trim($args['cd_atendimento']) != "" ? "AND pvp.cd_atendimento = ".(int)$args['cd_atendimento'] : "")."
						 ".(trim($args['cd_usuario_inicio']) != "" ? "AND pvp.cd_usuario_inicio = ".(int)$args['cd_usuario_inicio'] : "")."
						 ".(trim($args['cd_usuario_final']) != "" ? "AND pvp.cd_usuario_final = ".(int)$args['cd_usuario_final'] : "")."
					";
		//echo "<PRE>".$sql_select."</PRE>";
		
		// RESULTS
		$result = $this->db->query($sql_select);
		$count = $result->num_rows();
	}

	
	function listar_email(&$result, $args=array())
	{
		$qr_sql = "
					SELECT ee.cd_email,
						   TO_CHAR(ee.dt_envio, 'DD/MM/YYYY HH24:MI') AS dt_envio,
						   TO_CHAR(ee.dt_email_enviado, 'DD/MM/YYYY HH24:MI') AS dt_email_enviado,
						   ee.cd_empresa,
						   ee.cd_registro_empregado,
						   ee.seq_dependencia,
						   p.nome,
						   ee.fl_retornou,
						   uc.nome AS nome_usuario
					  FROM projetos.envia_emails ee
					  LEFT JOIN public.participantes p
						ON ee.cd_empresa            = p.cd_empresa
					   AND ee.cd_registro_empregado = p.cd_registro_empregado
					   AND ee.seq_dependencia       = p.seq_dependencia
					  LEFT JOIN projetos.usuarios_controledi uc
					    ON uc.codigo = ee.cd_usuario
					 WHERE ee.cd_evento = 55
					   ".(((trim($args['dt_inclusao_ini']) != "") and  (trim($args['dt_inclusao_fim']) != "")) ? "AND CAST(ee.dt_envio AS DATE) BETWEEN TO_DATE('".trim($args['dt_inclusao_ini'])."','DD/MM/YYYY') AND TO_DATE('".trim($args['dt_inclusao_fim'])."','DD/MM/YYYY')" : "")."
					 ORDER BY ee.cd_email DESC					
		          ";
		#echo "<pre style='text-align:left;'>$qr_sql</pre>"; exit;
		$result = $this->db->query($qr_sql);
	}	
}
?>