<?php
class contrib_percentual_programada_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	public function listar($args = array())
	{
		$qr_sql = "
			SELECT cd_contribuicao_percentual_programada,
				   cd_empresa,
				   cd_registro_empregado,
				   seq_dependencia,
				   projetos.participante_nome(cd_empresa, cd_registro_empregado, seq_dependencia) AS nome,
				   TO_CHAR(dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   TO_CHAR(dt_inicio,  'DD/MM/YYYY') AS dt_inicio,
				   vl_percentual AS vl_valor,
				   TO_CHAR(dt_cancelado,'DD/MM/YYYY HH24:MI:SS') AS dt_cancelado,
				   TO_CHAR(dt_confirmacao,'DD/MM/YYYY HH24:MI:SS') AS dt_confirmacao,
				   funcoes.get_usuario_nome(cd_usuario_confirmacao) AS ds_usuario_confirmacao,
				   vl_percentual_anterior as vl_anterior
			  FROM autoatendimento.contribuicao_percentual_programada 
			 WHERE dt_exclusao IS NULL
			   ".(trim($args['dt_cancelado']) == 'S' ? "AND dt_cancelado IS NOT NULL" : '')."
			   ".(trim($args['dt_cancelado']) == 'N' ? "AND dt_cancelado IS NULL" : '')."
			   ".(trim($args['fl_confirmado']) == 'S' ? "AND dt_confirmacao IS NOT NULL" : '')."
			   ".(trim($args['fl_confirmado']) == 'N' ? "AND dt_confirmacao IS NULL" : '')." 
			   ".(trim($args['cd_empresa']) != '' ? "AND cd_empresa = ".intval($args['cd_empresa']) : "")."
			   ".(trim($args['cd_registro_empregado']) != '' ? "AND cd_registro_empregado = ".intval($args['cd_registro_empregado']) : "")."			   
	           ".(trim($args['seq_dependencia']) != '' ? "AND seq_dependencia = ".intval($args['seq_dependencia']) : "")."
			   ".((trim($args['nome']) != "") ? " AND UPPER(projetos.participante_nome(cd_empresa, cd_registro_empregado, seq_dependencia)) like UPPER('%".trim($args['nome'])."%')" : "") . "
			   ".(((trim($args['dt_solicitacao_ini']) != '') AND (trim($args['dt_solicitacao_fim']) != '')) ? " AND DATE_TRUNC('day', dt_inclusao) BETWEEN TO_DATE('".$args['dt_solicitacao_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_solicitacao_fim']."', 'DD/MM/YYYY')" : "")."
	           ".(((trim($args['dt_inicio_ini']) != '') AND (trim($args['dt_inicio_fim']) != '')) ? " AND DATE_TRUNC('day', dt_inicio) BETWEEN TO_DATE('".$args['dt_inicio_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_inicio_fim']."', 'DD/MM/YYYY')" : "")."  
	           ".(((trim($args['dt_confirmacao_ini']) != '') AND (trim($args['dt_confirmacao_fim']) != '')) ? " AND DATE_TRUNC('day', dt_confirmacao) BETWEEN TO_DATE('".$args['dt_confirmacao_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_confirmacao_fim']."', 'DD/MM/YYYY')" : "")."
	           ORDER BY dt_inclusao;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function confirma($cd_contribuicao_percentual_programada, $cd_usuario)
	{
		$qr_sql = "
			UPDATE autoatendimento.contribuicao_percentual_programada
			   SET cd_usuario_confirmacao = ".intval($cd_usuario).",
			   	   dt_confirmacao         = CURRENT_TIMESTAMP
			 WHERE cd_contribuicao_percentual_programada = ".intval($cd_contribuicao_percentual_programada).";";
		
		$this->db->query($qr_sql);
	}

	public function cancelar($cd_contribuicao_percentual_programada, $ip_cancelado, $cd_usuario)
	{
		$qr_sql = "
			UPDATE autoatendimento.contribuicao_percentual_programada
			   SET ip_cancelado			= '".trim($ip_cancelado)."',
			       cd_usuario_cancelado = ".intval($cd_usuario).",
			   	   dt_cancelado         = CURRENT_TIMESTAMP
			 WHERE cd_contribuicao_percentual_programada = ".intval($cd_contribuicao_percentual_programada).";";
		
		$this->db->query($qr_sql);	
	}
}
?>