<?php
class administrativo_absenteismo_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	public function listar($cd_indicador_tabela)
	{
		$qr_sql = "
            SELECT cd_administrativo_absenteismo,
                   TO_CHAR(dt_referencia,'YYYY') AS ano_referencia,
                   TO_CHAR(dt_referencia,'MM/YYYY') AS mes_referencia,
                   dt_referencia,
                   TO_CHAR(dt_inclusao,'DD/MM/YYYY') AS dt_inclusao,
                   cd_usuario_inclusao,
                   TO_CHAR(dt_exclusao,'DD/MM/YYYY') AS dt_exclusao,
                   cd_usuario_exclusao,
                   cd_indicador_tabela,
                   fl_media,
                   nr_valor_1,
                   nr_valor_2,
                   nr_percentual_f,
                   nr_meta ,
                   nr_referencial,
                   observacao
		      FROM indicador_plugin.administrativo_absenteismo
		     WHERE dt_exclusao IS NULL
		       AND (fl_media = 'S' OR cd_indicador_tabela = ".intval($cd_indicador_tabela).")
		     ORDER BY dt_referencia ASC;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function carregar($cd_administrativo_absenteismo)
	{
		$qr_sql = "
            SELECT cd_administrativo_absenteismo,
                   TO_CHAR(dt_referencia,'YYYY') AS ano_referencia,
                   TO_CHAR(dt_referencia,'MM/YYYY') AS mes_referencia,
                   TO_CHAR(dt_referencia,'DD/MM/YYYY') AS dt_referencia,
                   TO_CHAR(dt_inclusao,'DD/MM/YYYY') AS dt_inclusao,
                   cd_usuario_inclusao,
                   TO_CHAR(dt_exclusao,'DD/MM/YYYY') AS dt_exclusao,
                   cd_usuario_exclusao,
                   cd_indicador_tabela,
                   fl_media,
                   nr_valor_1,
                   nr_valor_2,
                   nr_percentual_f,
                   nr_meta,
                   nr_referencial,
                   observacao
	          FROM indicador_plugin.administrativo_absenteismo 
	         WHERE dt_exclusao IS NULL
	           AND cd_administrativo_absenteismo = ".intval($cd_administrativo_absenteismo).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function carrega_referencia()
	{
		$qr_sql = "
			SELECT TO_CHAR(dt_referencia + '1 month'::interval,'DD/MM/YYYY') AS mes_referencia, 
				   dt_referencia, 
				   nr_meta,
				   nr_referencial,
				   cd_indicador_tabela
			  FROM indicador_plugin.administrativo_absenteismo
			 WHERE dt_exclusao IS NULL
			 ORDER BY dt_referencia DESC 
			 LIMIT 1;";

		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args = array())
	{
		$qr_sql="
			INSERT INTO indicador_plugin.administrativo_absenteismo
				(
					cd_indicador_tabela,
					dt_referencia,
					observacao,
					fl_media,
					nr_valor_1,
					nr_valor_2,
					nr_meta,
					nr_referencial,
					nr_percentual_f,
					cd_usuario_inclusao,
					cd_usuario_alteracao
				)
			VALUES
				(
					".(intval($args['cd_indicador_tabela']) > 0 ? intval($args['cd_indicador_tabela']) : "DEFAULT").",
					".(trim($args['dt_referencia']) != '' ? "TO_DATE('".$args['dt_referencia']."', 'DD/MM/YYYY')" : "DEFAULT").",
					".(trim($args['observacao']) != '' ? str_escape($args['observacao']) : "DEFAULT").",
					".(trim($args['fl_media']) != '' ? str_escape($args['fl_media']) : "DEFAULT").",
					".(trim($args['nr_valor_1']) != '' ? floatval($args['nr_valor_1']) : "DEFAULT").",
					".(trim($args['nr_valor_2']) != '' ? floatval($args['nr_valor_2']) : "DEFAULT").",
					".(trim($args['nr_meta']) != '' ? floatval($args['nr_meta']) : "DEFAULT").",
					".(trim($args['nr_referencial']) != '' ? floatval($args['nr_referencial']) : "DEFAULT").",
					".(trim($args['nr_percentual_f']) != '' ? floatval($args['nr_percentual_f']) : "DEFAULT").",
					".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
					".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT")."

				);";

		$this->db->query($qr_sql);
	}

	public function atualizar($cd_administrativo_absenteismo, $args = array())
	{
		$qr_sql="
            UPDATE indicador_plugin.administrativo_absenteismo
               SET dt_referencia                 = ".(trim($args['dt_referencia']) != '' ? "TO_DATE('".$args['dt_referencia']."', 'DD/MM/YYYY')" : "DEFAULT").",
                   cd_indicador_tabela           = ".(intval($args['cd_indicador_tabela']) > 0 ? intval($args['cd_indicador_tabela']) : "DEFAULT").",
                   fl_media                      = ".(trim($args['fl_media']) != '' ? str_escape($args['fl_media']) : "DEFAULT").",
                   nr_valor_1                    = ".(trim($args['nr_valor_1']) != '' ? floatval($args['nr_valor_1']) : "DEFAULT").",
                   nr_valor_2                    = ".(trim($args['nr_valor_2']) != '' ? floatval($args['nr_valor_2']) : "DEFAULT").",
                   nr_meta                       = ".(trim($args['nr_meta']) != '' ? floatval($args['nr_meta']) : "DEFAULT").",
                   nr_referencial                = ".(trim($args['nr_referencial']) != '' ? floatval($args['nr_referencial']) : "DEFAULT").",
                   observacao                    = ".(trim($args['observacao']) != '' ? str_escape($args['observacao']) : "DEFAULT").",
                   cd_usuario_alteracao 		 = ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT")."
		     WHERE cd_administrativo_absenteismo = ".intval($cd_administrativo_absenteismo).";";

		$this->db->query($qr_sql);
	}

	public function excluir($cd_administrativo_absenteismo, $cd_usuario)
	{
		$qr_sql = "
            UPDATE indicador_plugin.administrativo_absenteismo
		       SET dt_exclusao         = CURRENT_TIMESTAMP,
                   cd_usuario_exclusao = ".intval($cd_usuario)."
		     WHERE cd_administrativo_absenteismo = ".intval($cd_administrativo_absenteismo).";"; 

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
?>