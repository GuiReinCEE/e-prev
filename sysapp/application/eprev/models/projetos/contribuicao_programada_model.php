<?php
class Contribuicao_programada_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	public function listar($args = array())
	{
		$qr_sql = "
			SELECT aacp.cd_auto_atendimento_contrib_programada,
				   aacp.cd_empresa,
				   aacp.cd_registro_empregado,
				   aacp.seq_dependencia,
				   projetos.participante_nome(aacp.cd_empresa, aacp.cd_registro_empregado, aacp.seq_dependencia) AS nome,
				   TO_CHAR(aacp.dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   TO_CHAR(aacp.dt_inicio,  'DD/MM/YYYY') AS dt_inicio,
				   aacp.vl_valor,
				   TO_CHAR(aacp.dt_cancelado,'DD/MM/YYYY HH24:MI:SS') AS dt_cancelado,
				   TO_CHAR(aacp.dt_confirmacao,'DD/MM/YYYY HH24:MI:SS') AS dt_confirmacao,
				   funcoes.get_usuario_nome(aacp.cd_usuario_confirmacao) AS usuario_confirmacao,
				   TO_CHAR(aacp.dt_exclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_exclusao,
				   funcoes.get_usuario_nome(aacp.cd_usuario_exclusao) AS usuario_exclusao,
				   (CASE WHEN aacp.vl_valor_anterior > 0 THEN aacp.vl_valor_anterior
				         ELSE projetos.alteracao_contribuicao_programada(
					 		    aacp.cd_empresa::integer,
							    aacp.cd_registro_empregado::integer,
							    aacp.seq_dependencia::integer,
							    oracle.fnc_busca_forma_pag_inst(
							    	aacp.cd_empresa::integer,
							    	aacp.cd_registro_empregado::integer,
							    	aacp.seq_dependencia::integer
							   	),
							    aacp.dt_inclusao::date
						)
				   END) AS vl_anterior
			  FROM projetos.auto_atendimento_contrib_programada aacp
			 WHERE aacp.dt_exclusao IS NULL
			   ".(trim($args['fl_cancelado']) == 'S' ? "AND aacp.dt_cancelado IS NOT NULL" : '')."
			   ".(trim($args['fl_cancelado']) == 'N' ? "AND aacp.dt_cancelado IS NULL" : '')."
			   ".(trim($args['cd_empresa']) != '' ? "AND aacp.cd_empresa = ".intval($args['cd_empresa']) : "")."
			   ".(trim($args['cd_registro_empregado']) != '' ? "AND aacp.cd_registro_empregado = ".intval($args['cd_registro_empregado']) : "")."			   
	           ".(trim($args['seq_dependencia']) != '' ? "AND aacp.seq_dependencia = ".intval($args['seq_dependencia']) : "")."
			   ".((trim($args['nome']) != "") ? " AND UPPER(projetos.participante_nome(aacp.cd_empresa, aacp.cd_registro_empregado, aacp.seq_dependencia)) like UPPER('%".trim($args['nome'])."%')" : "") . "
			   ".(((trim($args['dt_solicitacao_ini']) != '') AND (trim($args['dt_solicitacao_fim']) != '')) ? " AND DATE_TRUNC('day', aacp.dt_inclusao) BETWEEN TO_DATE('".$args['dt_solicitacao_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_solicitacao_fim']."', 'DD/MM/YYYY')" : "")."
	           ".(((trim($args['dt_inicio_ini']) != '') AND (trim($args['dt_inicio_fim']) != '')) ? " AND DATE_TRUNC('day', aacp.dt_inicio) BETWEEN TO_DATE('".$args['dt_inicio_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_inicio_fim']."', 'DD/MM/YYYY')" : "")."  
	           ORDER BY aacp.dt_inclusao;";

		return $this->db->query($qr_sql)->result_array();
	}
}
?>