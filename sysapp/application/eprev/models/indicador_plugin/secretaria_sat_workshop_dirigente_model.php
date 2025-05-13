<?php
class Secretaria_sat_workshop_dirigente_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	public function listar($cd_indicador_tabela)
	{
		$qr_sql = "
			SELECT i.cd_secretaria_sat_workshop_dirigente,
                   TO_CHAR(i.dt_referencia,'YYYY') AS ano_referencia,
                   TO_CHAR(i.dt_referencia,'MM/YYYY') AS mes_referencia,
                   i.dt_referencia,
                   TO_CHAR(i.dt_inclusao,'DD/MM/YYYY') AS dt_inclusao,
                   i.cd_usuario_inclusao,
                   TO_CHAR(i.dt_exclusao,'DD/MM/YYYY') AS dt_exclusao,
                   i.cd_usuario_exclusao,
                   i.cd_indicador_tabela,
                   i.fl_media,
				   i.nr_respondentes,
				   i.nr_satisfeitos,
				   i.nr_resultado,
				   i.ds_observacao,
                   i.nr_meta,
				   CASE WHEN (SELECT MAX(i1.dt_referencia)
				                FROM indicador_plugin.secretaria_sat_workshop_dirigente i1
			                   WHERE i1.dt_exclusao IS NULL) = i.dt_referencia
						THEN 'S'
						ELSE 'N'
				   END AS fl_editar
			  FROM indicador_plugin.secretaria_sat_workshop_dirigente i
			 WHERE i.dt_exclusao IS NULL
             ORDER BY i.dt_referencia ASC";

		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega_referencia()
	{
		$qr_sql = "
			SELECT TO_CHAR(dt_referencia + '2 year'::interval, 'DD/MM/YYYY') AS dt_referencia, 
			       TO_CHAR(dt_referencia + '2 year'::interval, 'YYYY') AS ano_referencia,
			       nr_meta, 
				   cd_indicador_tabela
			  FROM indicador_plugin.secretaria_sat_workshop_dirigente 
			 WHERE dt_exclusao IS NULL 
			 ORDER BY dt_referencia DESC 
			 LIMIT 1;";
			 
		return $this->db->query($qr_sql)->row_array();
	}

	public function carrega($cd_secretaria_sat_workshop_dirigente)
	{
		$qr_sql = "
            SELECT cd_secretaria_sat_workshop_dirigente,
                   TO_CHAR(dt_referencia, 'DD/MM/YYYY') AS dt_referencia,
                   TO_CHAR(dt_referencia, 'YYYY') AS ano_referencia,
                   cd_indicador_tabela,
                   fl_media,
    			   nr_respondentes,
				   nr_satisfeitos,
				   nr_resultado,
				   ds_observacao,
				   nr_meta
		      FROM indicador_plugin.secretaria_sat_workshop_dirigente 
			 WHERE cd_secretaria_sat_workshop_dirigente = ".intval($cd_secretaria_sat_workshop_dirigente).";";
			 
		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args = array())
	{
		$qr_sql = "
			INSERT INTO indicador_plugin.secretaria_sat_workshop_dirigente
				(
					dt_referencia,
					cd_indicador_tabela,
					fl_media,
					nr_respondentes,
					nr_satisfeitos,
					nr_resultado,
					nr_meta,
					ds_observacao,
					cd_usuario_inclusao,
					cd_usuario_alteracao
				)
			VALUES
				(
					".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY')" : "DEFAULT").",
					".(intval($args['cd_indicador_tabela']) > 0 ? intval($args['cd_indicador_tabela']) : "DEFAULT").",
					".(trim($args['fl_media']) != '' ? str_escape($args['fl_media']) : "DEFAULT").",
					".(floatval($args['nr_respondentes']) > 0 ? floatval($args['nr_respondentes']) : "DEFAULT").",
					".(floatval($args['nr_satisfeitos']) > 0 ? floatval($args['nr_satisfeitos']) : "DEFAULT").",
					".(floatval($args['nr_resultado']) > 0 ? floatval($args['nr_resultado']) : "DEFAULT").",
					".(floatval($args['nr_meta']) > 0 ? floatval($args['nr_meta']) : "DEFAULT").",
					".(trim($args['ds_observacao']) != '' ? str_escape($args['ds_observacao']) : "DEFAULT").",
					".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
					".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT")."
				);";

		$this->db->query($qr_sql);
	}

	public function atualizar($cd_secretaria_sat_workshop_dirigente, $args = array())
	{
		$qr_sql = "
			UPDATE indicador_plugin.secretaria_sat_workshop_dirigente 
			   SET dt_referencia   		= ".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY')" : "DEFAULT").",
				   nr_respondentes 		= ".(floatval($args['nr_respondentes']) > 0 ? floatval($args['nr_respondentes']) : "DEFAULT").",
				   nr_satisfeitos  		= ".(floatval($args['nr_satisfeitos']) > 0 ? floatval($args['nr_satisfeitos']) : "DEFAULT").",
				   nr_resultado    		= ".(floatval($args['nr_resultado']) > 0 ? floatval($args['nr_resultado']) : "DEFAULT").",
				   nr_meta         		= ".(floatval($args['nr_meta']) > 0 ? floatval($args['nr_meta']) : "DEFAULT").",
				   ds_observacao        = ".(trim($args['ds_observacao']) != '' ? str_escape($args['ds_observacao']) : "DEFAULT").",
				   cd_usuario_alteracao = ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT")."
			 WHERE cd_secretaria_sat_workshop_dirigente = ".intval($cd_secretaria_sat_workshop_dirigente).";";

		$this->db->query($qr_sql);
	}

	public function excluir($cd_secretaria_sat_workshop_dirigente, $cd_usuario)
	{
		$qr_sql = " 
			UPDATE indicador_plugin.secretaria_sat_workshop_dirigente 
			   SET dt_exclusao = CURRENT_TIMESTAMP, 
			       cd_usuario_exclusao = ".intval($cd_usuario)." 
			 WHERE cd_secretaria_sat_workshop_dirigente =".intval($cd_secretaria_sat_workshop_dirigente).";"; 
		 
		$this->db->query($qr_sql);
	}
}