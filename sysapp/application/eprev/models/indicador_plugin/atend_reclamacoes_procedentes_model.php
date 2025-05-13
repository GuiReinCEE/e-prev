<?php
class Atend_reclamacoes_procedentes_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	public function listar($cd_indicador_tabela)
	{
		$qr_sql = "
			SELECT cd_atend_reclamacoes_procedentes,
				   cd_indicador_tabela,
				   TO_CHAR(dt_referencia,'YYYY') AS ano_referencia,
				   TO_CHAR(dt_referencia,'MM') AS mes_referencia,
				   TO_CHAR(dt_referencia,'MM/YYYY') AS mes_ano_referencia,
				   dt_referencia,
				   fl_media,
				   nr_reclamacao,
				   nr_reclamacao_procede,
				   nr_percent_procedente,
				   nr_meta,
				   observacao
			  FROM indicador_plugin.atend_reclamacoes_procedentes 
			 WHERE dt_exclusao IS NULL
			   AND (fl_media = 'S' OR cd_indicador_tabela = ".intval($cd_indicador_tabela).")
			 ORDER BY dt_referencia ASC;";
			 
		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega_referencia($cd_indicador_tabela)
	{
		$qr_sql = "
			SELECT TO_CHAR(dt_referencia + '1 month'::interval,'DD/MM/YYYY') AS mes_referencia, 
				   dt_referencia, 
				   nr_meta, 
				   cd_indicador_tabela
			  FROM indicador_plugin.atend_reclamacoes_procedentes 
			 WHERE dt_exclusao IS NULL 
			   AND cd_indicador_tabela = ".intval($cd_indicador_tabela)."
			 ORDER BY dt_referencia DESC 
			 LIMIT 1;";
			 
		return $this->db->query($qr_sql)->row_array();
	}

	public function carrega($cd_atend_reclamacoes_procedentes)
	{
		$qr_sql = "
			SELECT cd_atend_reclamacoes_procedentes,
				   cd_indicador_tabela,
				   TO_CHAR(dt_referencia,'DD/MM/YYYY') AS dt_referencia,
				   nr_reclamacao,
				   nr_reclamacao_procede,
				   nr_percent_procedente,
				   nr_meta,
				   observacao
			  FROM indicador_plugin.atend_reclamacoes_procedentes 
			 WHERE dt_exclusao IS NULL
			   AND cd_atend_reclamacoes_procedentes = ".intval($cd_atend_reclamacoes_procedentes)."
			 ORDER BY dt_referencia ASC;";
			 
		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args)
	{
		$qr_sql = "
			INSERT INTO indicador_plugin.atend_reclamacoes_procedentes
			     (
                   dt_referencia, 
                   cd_indicador_tabela, 
                   fl_media, 
				   nr_reclamacao,
				   nr_reclamacao_procede,
				   nr_percent_procedente,
				   nr_meta,
				   observacao,
				   cd_usuario_inclusao,
				   cd_usuario_alteracao
				 )
            VALUES 
			     (
				   ".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY')" : 'DEFAULT').",
				   ".(intval($args['cd_indicador_tabela']) > 0 ? intval($args['cd_indicador_tabela']) : 'DEFAULT').",
				   ".(trim($args['fl_media']) != '' ?  str_escape(trim($args['fl_media'])) : 'DEFAULT').",
				   ".(trim($args['nr_reclamacao']) != '' ? intval($args['nr_reclamacao']) : 'DEFAULT').",
				   ".(trim($args['nr_reclamacao_procede']) != '' ? intval($args['nr_reclamacao_procede']) : 'DEFAULT').",
				   ".(trim($args['nr_percent_procedente']) != '' ? floatval($args['nr_percent_procedente']) : 'DEFAULT').",
				   ".(trim($args['nr_meta']) != '' ? floatval($args['nr_meta']) : "DEFAULT").",
				   ".(trim($args['observacao']) != '' ?  str_escape(trim($args['observacao'])) : 'DEFAULT').",
				   ".intval($args['cd_usuario']).",
				   ".intval($args['cd_usuario'])."
				 );";

		$this->db->query($qr_sql);	
	}

	public function atualizar($cd_atend_reclamacoes_procedentes, $args)
	{
		$qr_sql = "
			UPDATE indicador_plugin.atend_reclamacoes_procedentes
			   SET dt_referencia         = ".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY')" : 'DEFAULT').",
			       fl_media              = ".(trim($args['fl_media']) != '' ?  str_escape(trim($args['fl_media'])) : 'DEFAULT').",
			       cd_indicador_tabela   = ".(intval($args['cd_indicador_tabela']) > 0 ? intval($args['cd_indicador_tabela']) : "DEFAULT").",
				   nr_reclamacao         = ".(trim($args['nr_reclamacao']) != '' ? intval($args['nr_reclamacao']) : 'DEFAULT').",
				   nr_reclamacao_procede = ".(trim($args['nr_reclamacao_procede']) != '' ? intval($args['nr_reclamacao_procede']) : 'DEFAULT').",
				   nr_percent_procedente = ".(trim($args['nr_percent_procedente']) != '' ? floatval($args['nr_percent_procedente']) : 'DEFAULT').",
				   nr_meta               = ".(trim($args['nr_meta']) != '' ? floatval($args['nr_meta']) : "DEFAULT").",
				   observacao            = ".(trim($args['observacao']) != '' ?  str_escape(trim($args['observacao'])) : 'DEFAULT').",
				   cd_usuario_alteracao  = ".intval($args['cd_usuario']).",
				   dt_alteracao          = CURRENT_TIMESTAMP
			 WHERE cd_atend_reclamacoes_procedentes = ".intval($cd_atend_reclamacoes_procedentes).";";

		$this->db->query($qr_sql);	
	}

	public function excluir($cd_atend_reclamacoes_procedentes, $cd_usuario)
	{
		$qr_sql = " 
			UPDATE indicador_plugin.atend_reclamacoes_procedentes 
			   SET dt_exclusao          = CURRENT_TIMESTAMP, 
				   cd_usuario_exclusao  = ".intval($cd_usuario)."
			 WHERE cd_atend_reclamacoes_procedentes = ".intval($cd_atend_reclamacoes_procedentes).";"; 

		$this->db->query($qr_sql);
	}	

	public function fechar_ano($args)
	{
		$qr_sql = "
			INSERT INTO indicador_plugin.atend_reclamacoes_procedentes
			     (
                   dt_referencia, 
                   cd_indicador_tabela, 
                   fl_media, 
				   nr_reclamacao,
				   nr_reclamacao_procede,
				   nr_percent_procedente,
				   nr_meta,
				   cd_usuario_inclusao,
				   cd_usuario_alteracao
				 )
            VALUES 
			     (
				   ".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY')" : 'DEFAULT').",
				   ".(intval($args['cd_indicador_tabela']) > 0 ? intval($args['cd_indicador_tabela']) : 'DEFAULT').",
				   ".(trim($args['fl_media']) != '' ?  str_escape(trim($args["fl_media"])) : 'DEFAULT').",
				   ".(trim($args['nr_reclamacao_total']) != '' ? intval($args['nr_reclamacao_total']) : 'DEFAULT').",
				   ".(trim($args['nr_reclamacao_procede_total']) != '' ? intval($args['nr_reclamacao_procede_total']) : 'DEFAULT').",
				   ".(trim($args['nr_percent_procedente_total']) != '' ? floatval($args['nr_percent_procedente_total']) : 'DEFAULT').",
				   ".(trim($args['nr_meta']) != '' ? floatval($args['nr_meta']) : 'DEFAULT').",
				   ".intval($args['cd_usuario']).",
				   ".intval($args['cd_usuario'])."
				 );";

		$this->db->query($qr_sql);	
	}

	public function fechar_indicador($cd_indicador_tabela, $cd_usuario)
	{
		$qr_sql = " 
			UPDATE indicador.indicador_tabela 
			   SET dt_fechamento_periodo         = CURRENT_TIMESTAMP, 
			       cd_usuario_fechamento_periodo = ".intval($cd_usuario)."
			 WHERE cd_indicador_tabela = ".intval($cd_indicador_tabela).";";
			 
		$this->db->query($qr_sql);
	}
}