<?php
class Atend_reclamacoes_fora_prazo_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	public function listar($cd_indicador_tabela)
	{
		$qr_sql = "
			SELECT cd_atend_reclamacoes_fora_prazo,
				   cd_indicador_tabela,
				   TO_CHAR(dt_referencia,'YYYY') AS ano_referencia,
				   TO_CHAR(dt_referencia,'MM') AS mes_referencia,
				   TO_CHAR(dt_referencia,'MM/YYYY') AS mes_ano_referencia,
				   dt_referencia,
				   fl_media,
				   nr_reclamacao,
				   nr_reclamacao_fora_prazo,
				   nr_percent_fora_prazo,
				   nr_meta,
				   observacao
			  FROM indicador_plugin.atend_reclamacoes_fora_prazo 
			 WHERE dt_exclusao IS NULL
			   AND (fl_media = 'S' OR cd_indicador_tabela = ".intval($cd_indicador_tabela).")
			 ORDER BY dt_referencia ASC;";
			 
		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega_referencia($cd_indicador_tabela, $nr_ano_referencia)
	{
		$qr_sql = "
			SELECT TO_CHAR(dt_referencia + '1 month'::interval,'DD/MM/YYYY') AS mes_referencia, 
				   dt_referencia, 
				   nr_meta, 
				   cd_indicador_tabela,
				   (SELECT COUNT(*)
				      FROM indicador_plugin.atend_reclamacoes_fora_prazo
				     WHERE TO_CHAR(dt_referencia, 'YYYY')::integer = ".intval($nr_ano_referencia)."
				       AND dt_exclusao IS NULL) AS qt_ano
			  FROM indicador_plugin.atend_reclamacoes_fora_prazo 
			 WHERE dt_exclusao IS NULL 
			   AND cd_indicador_tabela = ".intval($cd_indicador_tabela)."
			 ORDER BY dt_referencia DESC 
			 LIMIT 1;";
			 
		return $this->db->query($qr_sql)->row_array();
	}

	public function carrega($cd_atend_reclamacoes_fora_prazo)
	{
		$qr_sql = "
			SELECT cd_atend_reclamacoes_fora_prazo,
				   cd_indicador_tabela,
				   TO_CHAR(dt_referencia,'DD/MM/YYYY') AS dt_referencia,
				   nr_reclamacao,
				   nr_reclamacao_fora_prazo,
				   nr_percent_fora_prazo,
				   nr_meta,
				   observacao
			  FROM indicador_plugin.atend_reclamacoes_fora_prazo 
			 WHERE dt_exclusao IS NULL
			   AND cd_atend_reclamacoes_fora_prazo = ".intval($cd_atend_reclamacoes_fora_prazo)."
			 ORDER BY dt_referencia ASC;";
			 
		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args)
	{
		$qr_sql = "
			INSERT INTO indicador_plugin.atend_reclamacoes_fora_prazo
			     (
                   dt_referencia, 
                   cd_indicador_tabela, 
                   fl_media, 
				   nr_reclamacao,
				   nr_reclamacao_fora_prazo,
				   nr_percent_fora_prazo,
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
				   ".(trim($args['nr_reclamacao_fora_prazo']) != '' ? intval($args['nr_reclamacao_fora_prazo']) : 'DEFAULT').",
				   ".(trim($args['nr_percent_fora_prazo']) != '' ? floatval($args['nr_percent_fora_prazo']) : 'DEFAULT').",
				   ".(trim($args['nr_meta']) != '' ? floatval($args['nr_meta']) : "DEFAULT").",
				   ".(trim($args['observacao']) != '' ?  str_escape(trim($args['observacao'])) : 'DEFAULT').",
				   ".intval($args['cd_usuario']).",
				   ".intval($args['cd_usuario'])."
				 );";

		$this->db->query($qr_sql);	
	}

	public function atualizar($cd_atend_reclamacoes_fora_prazo, $args)
	{
		$qr_sql = "
			UPDATE indicador_plugin.atend_reclamacoes_fora_prazo
			   SET dt_referencia         = ".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY')" : 'DEFAULT').",
			       fl_media              = ".(trim($args['fl_media']) != '' ?  str_escape(trim($args['fl_media'])) : 'DEFAULT').",
			       cd_indicador_tabela   = ".(intval($args['cd_indicador_tabela']) > 0 ? intval($args['cd_indicador_tabela']) : "DEFAULT").",
				   nr_reclamacao         = ".(trim($args['nr_reclamacao']) != '' ? intval($args['nr_reclamacao']) : 'DEFAULT').",
				   nr_reclamacao_fora_prazo = ".(trim($args['nr_reclamacao_fora_prazo']) != '' ? intval($args['nr_reclamacao_fora_prazo']) : 'DEFAULT').",
				   nr_percent_fora_prazo = ".(trim($args['nr_percent_fora_prazo']) != '' ? floatval($args['nr_percent_fora_prazo']) : 'DEFAULT').",
				   nr_meta               = ".(trim($args['nr_meta']) != '' ? floatval($args['nr_meta']) : "DEFAULT").",
				   observacao            = ".(trim($args['observacao']) != '' ?  str_escape(trim($args['observacao'])) : 'DEFAULT').",
				   cd_usuario_alteracao  = ".intval($args['cd_usuario']).",
				   dt_alteracao          = CURRENT_TIMESTAMP
			 WHERE cd_atend_reclamacoes_fora_prazo = ".intval($cd_atend_reclamacoes_fora_prazo).";";

		$this->db->query($qr_sql);	
	}

	public function excluir($cd_atend_reclamacoes_fora_prazo, $cd_usuario)
	{
		$qr_sql = " 
			UPDATE indicador_plugin.atend_reclamacoes_fora_prazo 
			   SET dt_exclusao          = CURRENT_TIMESTAMP, 
				   cd_usuario_exclusao  = ".intval($cd_usuario)."
			 WHERE cd_atend_reclamacoes_fora_prazo = ".intval($cd_atend_reclamacoes_fora_prazo).";"; 

		$this->db->query($qr_sql);
	}	

	public function fechar_ano($args)
	{
		$qr_sql = "
			INSERT INTO indicador_plugin.atend_reclamacoes_fora_prazo
			     (
                   dt_referencia, 
                   cd_indicador_tabela, 
                   fl_media, 
				   nr_reclamacao,
				   nr_reclamacao_fora_prazo,
				   nr_percent_fora_prazo,
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
				   ".(trim($args['nr_reclamacao_fora_prazo_total']) != '' ? intval($args['nr_reclamacao_fora_prazo_total']) : 'DEFAULT').",
				   ".(trim($args['nr_percent_fora_prazo_total']) != '' ? floatval($args['nr_percent_fora_prazo_total']) : 'DEFAULT').",
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