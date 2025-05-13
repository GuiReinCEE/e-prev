<?php
class Senge_previdencia_inscricao_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	function listar(&$result, $args=array())
	{
		$qr_sql = "
			SELECT i.cd_interessado,
				   i.nome, 
				   i.cpf, 
				   (SELECT TO_CHAR(MAX(CAST(ic.dt_contato AS DATE)),'DD/MM/YYYY')
					  FROM senge_previdencia.interessado_contato ic
					 WHERE ic.dt_exclusao IS NULL
					   AND ic.fl_inscricao = 'S'
					   AND ic.cd_interessado = i.cd_interessado) AS dt_inscricao,
				   TO_CHAR(p.dt_inclusao,'DD/MM/YYYY') AS dt_inclusao_gap,
				   TO_CHAR(t.dt_ingresso_eletro,'DD/MM/YYYY') AS dt_ingresso_eletro                    
			  FROM senge_previdencia.interessado i
			  JOIN senge_previdencia.situacao s
				ON s.cd_situacao = i.cd_situacao
			  JOIN senge_previdencia.usuario u
				ON u.cd_usuario = i.cd_usuario_alteracao
			  LEFT JOIN public.participantes p
				ON p.cd_empresa = 7
			   AND funcoes.format_cpf(p.cpf_mf::bigint) = i.cpf 
			  LEFT JOIN public.titulares t
				ON t.cd_empresa            = p.cd_empresa
			   AND t.cd_registro_empregado = p.cd_registro_empregado
			   AND t.seq_dependencia       = p.seq_dependencia                 
			 WHERE i.dt_exclusao IS NULL
			   AND i.cd_situacao = 3
			   ".(trim($args['fl_cadastro_gap']) == 'S' ? "AND p.dt_inclusao IS NOT NULL" : '')."
			   ".(trim($args['fl_cadastro_gap']) == 'N' ? "AND p.dt_inclusao IS NULL" : '')."
			   ".(trim($args['fl_participante']) == 'S' ? "AND t.dt_ingresso_eletro IS NOT NULL" : '')."
			   ".(trim($args['fl_participante']) == 'N' ? "AND t.dt_ingresso_eletro IS NULL" : '')."
			   ".(((trim($args['dt_inscricao_ini']) != "") AND (trim($args['dt_inscricao_fim']) != "")) ? "AND DATE_TRUNC('day', (SELECT MAX(CAST(ic.dt_contato AS DATE))
																																    FROM senge_previdencia.interessado_contato ic
																																   WHERE ic.dt_exclusao IS NULL
																																     AND ic.fl_inscricao = 'S'
																																     AND ic.cd_interessado = i.cd_interessado)) BETWEEN TO_DATE('".$args['dt_inscricao_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_inscricao_fim']."', 'DD/MM/YYYY')" : "")."		   
			   ".(((trim($args['dt_inclusao_gap_ini']) != "") AND (trim($args['dt_inclusao_gap_fim']) != "")) ? "AND DATE_TRUNC('day', p.dt_inclusao) BETWEEN TO_DATE('".$args['dt_inclusao_gap_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_inclusao_gap_fim']."', 'DD/MM/YYYY')" : "")."		   
			   ".(((trim($args['dt_ingresso_eletro_ini']) != "") AND (trim($args['dt_ingresso_eletro_fim']) != "")) ? "AND DATE_TRUNC('day', t.dt_ingresso_eletro) BETWEEN TO_DATE('".$args['dt_ingresso_eletro_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_ingresso_eletro_fim']."', 'DD/MM/YYYY')" : "")."
			 ORDER BY dt_inscricao DESC;";

		$result = $this->db->query($qr_sql);
	}	
	
}
?>