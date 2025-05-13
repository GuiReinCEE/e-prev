<?php
class Financeiro_controle_atrasos_patrocinadora_model extends Model
{
	function __constuct()
	{
		parent::Model();
	}

	public function listar($cd_indicador_tabela)
	{
		$qr_sql = "
			SELECT cd_financeiro_controle_atrasos_patrocinadora,
                   TO_CHAR(dt_referencia, 'MM') AS mes_referencia,
                   TO_CHAR(dt_referencia, 'YYYY') AS ano_referencia,
                   TO_CHAR(dt_referencia, 'MM/YYYY') AS mes_ano_referencia,
				   dt_referencia,
				   fl_media,
				   nr_resultado,
				   nr_meta,
				   ds_observacao
			  FROM indicador_plugin.financeiro_controle_atrasos_patrocinadora
			 WHERE (fl_media = 'S' OR cd_indicador_tabela = ".intval($cd_indicador_tabela).")
			   AND dt_exclusao IS NULL
             ORDER BY dt_referencia ASC;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega($cd_financeiro_controle_atrasos_patrocinadora)
	{
		$qr_sql = "
			SELECT cd_financeiro_controle_atrasos_patrocinadora,
				   cd_indicador_tabela,
				   TO_CHAR(dt_referencia,'DD/MM/YYYY') AS dt_referencia,
				   fl_media,
				   nr_resultado,
				   nr_meta,
				   ds_observacao
			  FROM indicador_plugin.financeiro_controle_atrasos_patrocinadora
			 WHERE cd_financeiro_controle_atrasos_patrocinadora = ".intval($cd_financeiro_controle_atrasos_patrocinadora).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function carrega_referencia($nr_ano_referencia)
	{
		$qr_sql = "
			SELECT TO_CHAR(dt_referencia + '1 month'::interval, 'DD/MM/YYYY') AS dt_referencia_n, 
				   cd_indicador_tabela,
				   nr_meta,
				   (SELECT COUNT(*)
				      FROM indicador_plugin.financeiro_controle_atrasos_patrocinadora
				     WHERE TO_CHAR(dt_referencia, 'YYYY')::integer = ".intval($nr_ano_referencia)."
				       AND dt_exclusao IS NULL) AS qt_ano
			  FROM indicador_plugin.financeiro_controle_atrasos_patrocinadora
			 WHERE dt_exclusao IS NULL 
			 ORDER BY dt_referencia DESC 
             LIMIT 1;";

		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args = array())
	{
		$qr_sql = "
			INSERT INTO indicador_plugin.financeiro_controle_atrasos_patrocinadora
				(
	                cd_indicador_tabela,
	                dt_referencia,
	                fl_media,
					nr_resultado,
					nr_meta,
					ds_observacao,
	                cd_usuario_inclusao,
	                cd_usuario_alteracao
				)
			VALUES
				(
					".(intval($args['cd_indicador_tabela']) > 0 ? intval($args['cd_indicador_tabela']) : "DEFAULT").",
					".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY')" : "DEFAULT").",
					".(trim($args['fl_media']) != '' ? str_escape($args['fl_media']) : "DEFAULT").",
					".(intval($args['nr_resultado']) > 0 ? floatval($args['nr_resultado']) : "DEFAULT").",
					".(intval($args['nr_meta']) > 0 ? intval($args['nr_meta']) : "DEFAULT").",
					".(trim($args['ds_observacao']) != '' ? str_escape($args['ds_observacao']) : "DEFAULT").",
					".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
					".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT")."
				);";

		$this->db->query($qr_sql);
	}

	public function atualizar($cd_financeiro_controle_atrasos_patrocinadora, $args = array())
	{
		$qr_sql = "
			UPDATE indicador_plugin.financeiro_controle_atrasos_patrocinadora
			   SET dt_referencia        = ".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY')" : "DEFAULT").",
				   nr_resultado 		= ".(intval($args['nr_resultado']) > 0 ? intval($args['nr_resultado']) : "DEFAULT").",
				   nr_meta 				= ".(intval($args['nr_meta']) > 0 ? intval($args['nr_meta']) : "DEFAULT").",
				   ds_observacao 		= ".(trim($args['ds_observacao']) != '' ? str_escape($args['ds_observacao']) : "DEFAULT").",
				   cd_usuario_alteracao = ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT")."
			 WHERE cd_financeiro_controle_atrasos_patrocinadora = ".intval($cd_financeiro_controle_atrasos_patrocinadora).";";

		$this->db->query($qr_sql);
	}

    public function excluir($cd_financeiro_controle_atrasos_patrocinadora, $cd_usuario)
    {
        $qr_sql = "
            UPDATE indicador_plugin.financeiro_controle_atrasos_patrocinadora 
               SET cd_usuario_exclusao = ".intval($cd_usuario).",
                   dt_exclusao         = CURRENT_TIMESTAMP
             WHERE cd_financeiro_controle_atrasos_patrocinadora = ".intval($cd_financeiro_controle_atrasos_patrocinadora).";";

        $this->db->query($qr_sql);
    }

    public function fechar_periodo($cd_indicador_tabela, $cd_usuario)
	{
		$qr_sql = "
			UPDATE indicador.indicador_tabela 
			   SET dt_fechamento_periodo         = CURRENT_TIMESTAMP, 
			       cd_usuario_fechamento_periodo = ".intval($cd_usuario)." 
		     WHERE cd_indicador_tabela = ".intval($cd_indicador_tabela).";";

		$this->db->query($qr_sql);
	}
}