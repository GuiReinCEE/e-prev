<?php
class Info_indisp_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	public function listar($cd_indicador_tabela)
	{
		$qr_sql = "
			SELECT ii.cd_info_indisp,
				   ii.cd_indicador_tabela,
				   TO_CHAR(ii.dt_referencia,'YYYY') as ano_referencia,
				   TO_CHAR(ii.dt_referencia,'MM/YYYY') as mes_referencia,
				   ii.dt_referencia,
				   ii.nr_expediente,
				   ii.nr_minutos_a,
				   ii.nr_minutos_b,
				   ii.nr_percentual_a,
				   ii.nr_percentual_b,
				   ii.nr_meta,
				   ii.fl_media,
				   ii.observacao,
				   ii.fl_meta,
				   ii.fl_direcao,
				   (SELECT i1.tp_analise
					  FROM indicador.indicador_tabela it
					  JOIN indicador.indicador i1
						ON i1.cd_indicador = it.cd_indicador
					 WHERE it.cd_indicador_tabela = ii.cd_indicador_tabela) AS tp_analise
			  FROM indicador_plugin.info_indisp  ii
			 WHERE ii.dt_exclusao IS NULL
			   AND (ii.fl_media = 'S' OR ii.cd_indicador_tabela = ".intval($cd_indicador_tabela).")
			 ORDER BY ii.dt_referencia ASC;";
			 
		return $this->db->query($qr_sql)->result_array();
	}
	
	public function carrega_referencia($cd_indicador_tabela)
	{
		$qr_sql = "
			SELECT TO_CHAR(dt_referencia + '1 month'::interval,'DD/MM/YYYY') AS mes_referencia, 
				   dt_referencia, 
				   nr_meta, 
				   cd_indicador_tabela
			  FROM indicador_plugin.info_indisp 
			 WHERE dt_exclusao IS NULL 
			   AND cd_indicador_tabela = ".intval($cd_indicador_tabela)."
			 ORDER BY dt_referencia DESC 
			 LIMIT 1;";
			 
		return $this->db->query($qr_sql)->row_array();
	}
	
	public function carrega($cd_info_indisp)
	{
		$qr_sql = "
			SELECT cd_info_indisp,
				   TO_CHAR(dt_referencia,'DD/MM/YYYY') AS dt_referencia,
				   cd_indicador_tabela,
				   nr_expediente,
				   nr_minutos_a,
				   nr_minutos_b,
				   nr_percentual_a,
				   nr_percentual_b,
				   nr_meta,
				   observacao
			  FROM indicador_plugin.info_indisp
			 WHERE cd_info_indisp = ".intval($cd_info_indisp).";";
					 
		return $this->db->query($qr_sql)->row_array();
	}	

	public function salvar($args)
	{
		$qr_sql = "
			INSERT INTO indicador_plugin.info_indisp 
				 ( 
				   dt_referencia, 
				   cd_indicador_tabela,
				   fl_media,
				   nr_expediente, 
				   nr_minutos_a, 
				   nr_minutos_b,
				   nr_percentual_a,
				   nr_percentual_b, 
				   nr_meta,
				   observacao,
				   cd_usuario_inclusao,
				   cd_usuario_alteracao
				 ) 
			VALUES 
				 ( 
				   ".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY')" : "DEFAULT").",
				   ".(intval($args['cd_indicador_tabela']) > 0 ? intval($args['cd_indicador_tabela']) : "DEFAULT").",
				   ".(trim($args['fl_media']) != '' ?  str_escape(trim($args['fl_media'])) : "DEFAULT").",
				   ".(trim($args['nr_expediente']) != '' ? intval($args['nr_expediente']) : "DEFAULT").",
				   ".(trim($args['nr_minutos_a']) != '' ? intval($args['nr_minutos_a']) : "DEFAULT").",
				   ".(trim($args['nr_minutos_b']) != '' ? intval($args['nr_minutos_b']) : "DEFAULT").",
				   ".(trim($args['nr_percentual_a']) != '' ? floatval($args['nr_percentual_a']) : "DEFAULT").",
				   ".(trim($args['nr_percentual_b']) != '' ? floatval($args['nr_percentual_b']) : "DEFAULT").",
				   ".(trim($args['nr_meta']) != '' ? floatval($args['nr_meta']) : "DEFAULT").",
				   ".(trim($args['observacao']) != '' ?  str_escape(trim($args['observacao'])) : "DEFAULT").",
				   ".intval($args['cd_usuario']).",
				   ".intval($args['cd_usuario'])."
				 );";
					 
		$this->db->query($qr_sql);
	}
	
	public function atualizar($cd_info_indisp, $args)
	{
		$qr_sql = "
			UPDATE indicador_plugin.info_indisp 
			   SET dt_referencia        = ".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY')" : "DEFAULT").",
				   fl_media             = ".(trim($args['fl_media']) != '' ?  str_escape(trim($args["fl_media"])) : "DEFAULT").",
			       nr_expediente        = ".(trim($args['nr_expediente']) != '' ? floatval($args['nr_expediente']) : "DEFAULT").",
			       nr_minutos_a         = ".(trim($args['nr_minutos_a']) != '' ? intval($args['nr_minutos_a']) : "DEFAULT").",
			       nr_minutos_b         = ".(trim($args['nr_minutos_b']) != '' ? floatval($args['nr_minutos_b']) : "DEFAULT").",
			       nr_percentual_a      = ".(trim($args['nr_percentual_a']) != '' ? floatval($args['nr_percentual_a']) : "DEFAULT").",
			       nr_percentual_b      = ".(trim($args['nr_percentual_b']) != '' ? floatval($args['nr_percentual_b']) : "DEFAULT").",
				   nr_meta              = ".(trim($args['nr_meta']) != '' ? floatval($args['nr_meta']) : "DEFAULT").",
				   observacao           = ".(trim($args['observacao']) != '' ?  str_escape(trim($args["observacao"])) : "DEFAULT").",
				   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
				   dt_alteracao         = CURRENT_TIMESTAMP
			 WHERE cd_info_indisp = ".$cd_info_indisp.";";
	
		$this->db->query($qr_sql);	
	}
	
	public function excluir($cd_info_indisp, $cd_usuario)
	{
		$qr_sql = " 
			UPDATE indicador_plugin.info_indisp 
			   SET dt_exclusao          = CURRENT_TIMESTAMP, 
				   cd_usuario_exclusao  = ".intval($cd_usuario)."
			 WHERE cd_info_indisp = ".intval($cd_info_indisp).";"; 

		$this->db->query($qr_sql);
	}

	public function fechar_indicador($cd_indicador_tabela, $cd_usuario)
	{
		$qr_sql = " 
			UPDATE indicador.indicador_tabela 
			   SET dt_fechamento_periodo 	     = CURRENT_TIMESTAMP,
				   cd_usuario_fechamento_periodo = ".intval($cd_usuario)."
			 WHERE cd_indicador_tabela = ".intval($cd_indicador_tabela).";";

		$this->db->query($qr_sql);
	}	
}
?>