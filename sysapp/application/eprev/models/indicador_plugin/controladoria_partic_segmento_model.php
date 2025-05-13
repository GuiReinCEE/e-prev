<?php
class Controladoria_partic_segmento_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	public  function listar($cd_indicador_tabela)
	{
		$qr_sql = "
			SELECT c1.cd_controladoria_partic_segmento,
			       c1.cd_indicador_tabela,
				   TO_CHAR(c1.dt_referencia, 'YYYY') AS ano_referencia,
				   TO_CHAR(c1.dt_referencia, 'MM') AS mes_referencia,
				   TO_CHAR(c1.dt_referencia, 'MM/YYYY') AS mes_ano_referencia,
				   c1.dt_referencia,
				   c1.nr_invest_segmento,
				   c1.nr_invest_fceee,
				   c1.nr_participacao,
				   COALESCE(c1.nr_meta, (SELECT c2.nr_participacao
				                           FROM indicador_plugin.controladoria_partic_segmento c2
				                          WHERE c2.dt_exclusao IS NULL 
				                            AND c2.fl_media = 'S'
				                            AND c2.dt_referencia < c1.dt_referencia
				                          ORDER BY c2.dt_referencia DESC
				                          LIMIT 1)) AS nr_meta,
				   c1.fl_media,
				   c1.ds_observacao
		      FROM indicador_plugin.controladoria_partic_segmento c1
		     WHERE c1.dt_exclusao IS NULL
		       AND (c1.fl_media = 'S' OR c1.cd_indicador_tabela = ".intval($cd_indicador_tabela).")
		     ORDER BY c1.dt_referencia ASC;";

		return $this->db->query($qr_sql)->result_array();
	}
	
	public function carrega_referencia()
	{
		$qr_sql = "
			SELECT TO_CHAR(dt_referencia + '1 month'::interval, 'DD/MM/YYYY') AS dt_referencia_n, 
			       TO_CHAR(dt_referencia + '1 month'::interval, 'MM') AS ds_mes_referencia_n, 
			       TO_CHAR(dt_referencia + '1 month'::interval, 'YYYY') AS ds_ano_referencia_n,
				   cd_indicador_tabela 
			  FROM indicador_plugin.controladoria_partic_segmento
			 WHERE dt_exclusao IS NULL 
			 ORDER BY dt_referencia DESC 
			 LIMIT 1;";
			 
		return $this->db->query($qr_sql)->row_array();
	}
	
	public function carrega($cd_controladoria_partic_segmento)
	{
		$qr_sql = "
            SELECT cd_controladoria_partic_segmento,
                   TO_CHAR(dt_referencia, 'YYYY') AS ano_referencia,
				   TO_CHAR(dt_referencia, 'MM') AS mes_referencia,
                   TO_CHAR(dt_referencia,'DD/MM/YYYY') AS dt_referencia,
                   cd_indicador_tabela,
                   fl_media,
                   nr_invest_segmento,
				   nr_invest_fceee,
				   nr_participacao,
				   nr_meta,
                   ds_observacao
		      FROM indicador_plugin.controladoria_partic_segmento 
			 WHERE cd_controladoria_partic_segmento = ".intval($cd_controladoria_partic_segmento).";";
			 
		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args = array())
	{
		if(intval($args['cd_controladoria_partic_segmento']) == 0)
		{
			$qr_sql = "
				INSERT INTO indicador_plugin.controladoria_partic_segmento 
				     (
				        cd_indicador_tabela, 
						dt_referencia, 
					    fl_media,
						nr_invest_segmento,
					    nr_invest_fceee,
					    nr_participacao,
					    nr_meta,
                        ds_observacao,
					    cd_usuario_inclusao,
					    cd_usuario_alteracao
			          ) 
			     VALUES 
				      ( 
				        ".(intval($args['cd_indicador_tabela']) != 0 ? intval($args['cd_indicador_tabela']) : "DEFAULT").",
						".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY')" : "DEFAULT").",
					    ".(trim($args['fl_media']) != '' ? str_escape($args['fl_media']) : "DEFAULT").",
					    ".(trim($args['nr_invest_segmento']) != '' ? floatval($args['nr_invest_segmento']) : "DEFAULT").",
					    ".(trim($args['nr_invest_fceee']) != '' ? floatval($args['nr_invest_fceee']) : "DEFAULT").",
					    ".(trim($args['nr_participacao']) != '' ? floatval($args['nr_participacao']) : "DEFAULT").",
					    ".(trim($args['nr_meta']) != '' ? floatval($args['nr_meta']) : "DEFAULT").",
					    ".(trim($args['ds_observacao']) != '' ? str_escape($args['ds_observacao']) : "DEFAULT").",
					    ".intval($args['cd_usuario']).",
					    ".intval($args['cd_usuario'])."
                      );";
		}
		else
		{
			$qr_sql = "
				UPDATE indicador_plugin.controladoria_partic_segmento
				   SET dt_referencia        = ".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY')" : "DEFAULT").",
				       nr_invest_segmento   = ".(trim($args['nr_invest_segmento']) != '' ? floatval($args['nr_invest_segmento']) : "DEFAULT").",
				       nr_invest_fceee      = ".(trim($args['nr_invest_fceee']) != '' ? floatval($args['nr_invest_fceee']) : "DEFAULT").",
				       nr_participacao      = ".(trim($args['nr_participacao']) != '' ? floatval($args['nr_participacao']) : "DEFAULT").",
				       nr_meta              = ".(trim($args['nr_meta']) != '' ? floatval($args['nr_meta']) : "DEFAULT").",
                       ds_observacao        = ".(trim($args['ds_observacao']) != '' ? str_escape($args['ds_observacao']) : "DEFAULT").",
					   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
					   dt_alteracao         = CURRENT_TIMESTAMP
			     WHERE cd_controladoria_partic_segmento = ".intval($args['cd_controladoria_partic_segmento']).";";
		}

		$this->db->query($qr_sql);
	}
	
	public function excluir($cd_controladoria_partic_segmento, $cd_usuario)
	{
		$qr_sql = " 
			UPDATE indicador_plugin.controladoria_partic_segmento
		       SET dt_exclusao         = CURRENT_TIMESTAMP, 
			       cd_usuario_exclusao = ".intval($cd_usuario)."
		     WHERE cd_controladoria_partic_segmento = ".intval($cd_controladoria_partic_segmento).";"; 
			 
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