<?php
class Atuarial_exigencia_previc_regulamento_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	public function listar($cd_indicador_tabela)
	{
		$qr_sql = "
			SELECT cd_atuarial_exigencia_previc_regulamento,
				   cd_indicador_tabela,
				   TO_CHAR(dt_referencia,'YYYY') AS ano_referencia,
				   TO_CHAR(dt_referencia,'MM') AS mes_referencia,
				   TO_CHAR(dt_referencia,'MM/YYYY') AS mes_ano_referencia,
				   dt_referencia,
				   fl_media,
				   ds_evento,
				   CASE WHEN nr_houve_exigencia = 0 THEN 'Não'
				        WHEN nr_houve_exigencia = 1 THEN 'Sim'
				        ELSE ''
                   END AS ds_houve_exigencia,
                   CASE WHEN nr_meta = 0 THEN 'Não'
				        WHEN nr_meta = 1 THEN 'Sim'
				        ELSE ''
                   END AS ds_meta,
                   CASE WHEN nr_meta_resultado = 0 THEN 'Não Atendido'
				        WHEN nr_meta_resultado = 1 THEN 'Atendido'
				        ELSE ''
                   END AS ds_meta_resultado,
				   nr_houve_exigencia,
				   nr_meta,
				   nr_meta_resultado,
				   ds_observacao
			  FROM indicador_plugin.atuarial_exigencia_previc_regulamento 
			 WHERE dt_exclusao IS NULL
			   AND (fl_media = 'S' OR cd_indicador_tabela = ".intval($cd_indicador_tabela).")
			 ORDER BY dt_referencia ASC;";
			 
		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega_referencia($cd_indicador_tabela)
	{
		$qr_sql = "
			SELECT nr_meta, 
				   cd_indicador_tabela 
			  FROM indicador_plugin.atuarial_exigencia_previc_regulamento 
			 WHERE dt_exclusao IS NULL 
			   AND cd_indicador_tabela = ".intval($cd_indicador_tabela)."
			 ORDER BY dt_inclusao DESC 
			 LIMIT 1;";
			 
		return $this->db->query($qr_sql)->row_array();
	}

	public function carrega($cd_atuarial_exigencia_previc_regulamento)
	{
		$qr_sql = "
			SELECT cd_atuarial_exigencia_previc_regulamento,
				   cd_indicador_tabela,
				   TO_CHAR(dt_referencia,'DD/MM/YYYY') AS dt_referencia,
				   ds_evento,
				   nr_houve_exigencia,
				   nr_meta,
				   ds_observacao
			  FROM indicador_plugin.atuarial_exigencia_previc_regulamento 
			 WHERE dt_exclusao IS NULL
			   AND cd_atuarial_exigencia_previc_regulamento = ".intval($cd_atuarial_exigencia_previc_regulamento)."
			 ORDER BY dt_referencia ASC;";
			 
		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args)
	{
		$qr_sql = "
			INSERT INTO indicador_plugin.atuarial_exigencia_previc_regulamento
			     (
                   dt_referencia, 
                   cd_indicador_tabela, 
                   fl_media, 
                   ds_evento,
				   nr_houve_exigencia,
				   nr_meta,
				   nr_meta_resultado,
                   ds_observacao,
				   cd_usuario_inclusao,
				   cd_usuario_alteracao
				 )
            VALUES 
			     (
				   ".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY')" : "DEFAULT").",
				   ".(intval($args['cd_indicador_tabela']) > 0 ? intval($args['cd_indicador_tabela']) : "DEFAULT").",
				   ".(trim($args['fl_media']) != '' ?  str_escape(trim($args["fl_media"])) : "DEFAULT").",
				   ".(trim($args['ds_evento']) != '' ?  str_escape(trim($args['ds_evento'])) : "DEFAULT").",
				   ".(trim($args['nr_houve_exigencia']) != '' ? intval($args['nr_houve_exigencia']) : "DEFAULT").",
				   ".(trim($args['nr_meta']) != '' ? floatval($args['nr_meta']) : "DEFAULT").",
				   ".(trim($args['nr_meta_resultado']) != '' ? intval($args['nr_meta_resultado']) : "DEFAULT").",
				   ".(trim($args['ds_observacao']) != '' ?  str_escape(trim($args['ds_observacao'])) : "DEFAULT").",
				   ".intval($args['cd_usuario']).",
				   ".intval($args['cd_usuario'])."
				 );";

		$this->db->query($qr_sql);	
	}

	public function atualizar($cd_atuarial_exigencia_previc_regulamento, $args)
	{
		$qr_sql = "
			UPDATE indicador_plugin.atuarial_exigencia_previc_regulamento
			   SET dt_referencia        = ".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY')" : "DEFAULT").",
			       fl_media             = ".(trim($args['fl_media']) != '' ?  str_escape(trim($args["fl_media"])) : "DEFAULT").",
			       cd_indicador_tabela  = ".(intval($args['cd_indicador_tabela']) > 0 ? intval($args['cd_indicador_tabela']) : "DEFAULT").",
			       ds_evento            = ".(trim($args['ds_evento']) != '' ?  str_escape(trim($args['ds_evento'])) : "DEFAULT").",
				   nr_houve_exigencia   = ".(trim($args['nr_houve_exigencia']) != '' ? intval($args['nr_houve_exigencia']) : "DEFAULT").",
				   nr_meta              = ".(trim($args['nr_meta']) != '' ? floatval($args['nr_meta']) : "DEFAULT").",
				   nr_meta_resultado    = ".(trim($args['nr_meta_resultado']) != '' ? intval($args['nr_meta_resultado']) : "DEFAULT").",
				   ds_observacao        = ".(trim($args['ds_observacao']) != '' ?  str_escape(trim($args["ds_observacao"])) : "DEFAULT").",
				   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
				   dt_alteracao         = CURRENT_TIMESTAMP
			 WHERE cd_atuarial_exigencia_previc_regulamento = ".intval($cd_atuarial_exigencia_previc_regulamento).";";

		$this->db->query($qr_sql);	
	}

	public function excluir($cd_atuarial_exigencia_previc_regulamento, $cd_usuario)
	{
		$qr_sql = " 
			UPDATE indicador_plugin.atuarial_exigencia_previc_regulamento 
			   SET dt_exclusao          = CURRENT_TIMESTAMP, 
				   cd_usuario_exclusao  = ".intval($cd_usuario)."
			 WHERE cd_atuarial_exigencia_previc_regulamento = ".intval($cd_atuarial_exigencia_previc_regulamento).";"; 

		$this->db->query($qr_sql);
	}	
}