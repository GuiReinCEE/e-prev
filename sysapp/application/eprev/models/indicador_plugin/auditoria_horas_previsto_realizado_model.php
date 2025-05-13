<?php
class Auditoria_horas_previsto_realizado_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	public function listar($cd_indicador_tabela)
	{
		$qr_sql = "
			SELECT cd_auditoria_horas_previsto_realizado, 
			       TO_CHAR(dt_referencia,'MM/YYYY') AS mes_ano_referencia,
				   TO_CHAR(dt_referencia,'YYYY') AS ano_referencia, 
				   TO_CHAR(dt_referencia,'MM') AS mes_referencia, 
				   dt_referencia, 
				   ds_evento,
				   cd_indicador_tabela, 
				   fl_media, 
				   nr_horas_previstas, 
				   nr_horas_realizadas,
				   nr_previstas_realizadas, 
				   nr_meta, 
				   nr_percentual_acima_meta,
			       observacao
			  FROM indicador_plugin.auditoria_horas_previsto_realizado 
			 WHERE dt_exclusao IS NULL
	           AND (fl_media ='S' OR cd_indicador_tabela = ".intval($cd_indicador_tabela).")
			 ORDER BY dt_inclusao ASC";

		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega_referencia($cd_indicador_tabela)
	{
		$qr_sql = "
			SELECT TO_CHAR(dt_referencia,'YYYY') AS ano_referencia,
				   TO_CHAR(dt_referencia + '1 month'::interval,'MM') AS mes_referencia, 
				   nr_meta, 
				   cd_indicador_tabela
			  FROM indicador_plugin.auditoria_horas_previsto_realizado 
			 WHERE dt_exclusao IS NULL 
			   AND cd_indicador_tabela = ".intval($cd_indicador_tabela)."
			 ORDER BY dt_referencia DESC 
			 LIMIT 1;";
			 
		return $this->db->query($qr_sql)->row_array();
	}

	public function carrega($cd_auditoria_horas_previsto_realizado)
	{
		$qr_sql = " 
			SELECT cd_auditoria_horas_previsto_realizado,
			       ds_evento,
			 	   TO_CHAR(dt_referencia,'YYYY') AS ano_referencia, 
				   TO_CHAR(dt_referencia,'MM') AS mes_referencia, 
				   TO_CHAR(dt_referencia,'DD/MM/YYYY') AS dt_referencia, 
				   TO_CHAR(dt_inclusao,'DD/MM/YYYY') AS dt_inclusao, 
				   cd_usuario_inclusao, 
				   TO_CHAR(dt_exclusao,'DD/MM/YYYY') AS dt_exclusao, 
				   cd_usuario_exclusao, 
				   cd_indicador_tabela, 
				   fl_media, 
				   nr_percentual_acima_meta,
			       observacao, 
				   nr_horas_previstas, 
				   nr_horas_realizadas,
				   nr_previstas_realizadas,
				   nr_meta 
			  FROM indicador_plugin.auditoria_horas_previsto_realizado 
		 	 WHERE cd_auditoria_horas_previsto_realizado= ".intval($cd_auditoria_horas_previsto_realizado).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args)
	{
		if(intval($args['cd_auditoria_horas_previsto_realizado']) == 0)
		{
			$qr_sql = "
				INSERT INTO indicador_plugin.auditoria_horas_previsto_realizado 
				( 
					ds_evento,  
					dt_inclusao,
					dt_alteracao, 
					cd_usuario_inclusao,  
					cd_indicador_tabela, 
					fl_media, 
					nr_horas_previstas, 
				   	nr_horas_realizadas,
				   	nr_previstas_realizadas,
				   	nr_percentual_acima_meta,
					nr_meta,  
		            observacao,
		            cd_usuario_alteracao
		        ) 
		        VALUES 
		        ( 
					".(trim($args['ds_evento']) == "" ? "DEFAULT" : "'".trim($args["ds_evento"])."'").",
			 		CURRENT_TIMESTAMP,
			 		CURRENT_TIMESTAMP, 
			 		".intval($args['cd_usuario']).", 
					".(intval($args['cd_indicador_tabela']) == 0 ? "DEFAULT" : intval($args['cd_indicador_tabela'])).",  
					".(trim($args['fl_media']) == "" ? "''" : "'".trim($args["fl_media"])."'").", 
					".(trim($args['nr_horas_previstas']) == "" ? "DEFAULT" : floatval($args['nr_horas_previstas'])).", 
					".(trim($args['nr_horas_realizadas']) == "" ? "DEFAULT" : floatval($args['nr_horas_realizadas'])).", 
					".(trim($args['nr_previstas_realizadas']) == "" ? "DEFAULT" : floatval($args['nr_previstas_realizadas'])).", 
					".(trim($args['nr_percentual_acima_meta']) == "" ? "DEFAULT" : floatval($args['nr_percentual_acima_meta'])).",
					".(trim($args['nr_meta']) == "" ? "DEFAULT" : floatval($args['nr_meta'])).", 
            		".(trim($args['observacao']) == "" ? "DEFAULT" : "'".trim($args["observacao"])."'").",
            		".intval($args['cd_usuario'])."
				);";
		}
		else
		{
			$qr_sql = "
				UPDATE indicador_plugin.auditoria_horas_previsto_realizado 
				SET cd_auditoria_horas_previsto_realizado = ".intval($args['cd_auditoria_horas_previsto_realizado']).",
					ds_evento                         = ".(trim($args['ds_evento']) == "" ? "DEFAULT" : "'".trim($args["ds_evento"])."'").", 
					cd_indicador_tabela               = ".(intval($args['cd_indicador_tabela']) == 0 ? "DEFAULT" : intval($args['cd_indicador_tabela'])).",  
					fl_media                          = ".(trim($args['fl_media']) == "" ? "''" : "'".trim($args["fl_media"])."'").", 
					nr_horas_previstas                = ".(trim($args['nr_horas_previstas']) == "" ? "DEFAULT" : floatval($args['nr_horas_previstas'])).", 
					nr_horas_realizadas               = ".(trim($args['nr_horas_realizadas']) == "" ? "DEFAULT" : floatval($args['nr_horas_realizadas'])).",
					nr_previstas_realizadas           = ".(trim($args['nr_previstas_realizadas']) == "" ? "DEFAULT" : floatval($args['nr_previstas_realizadas'])).",
					nr_percentual_acima_meta          = ".(trim($args['nr_percentual_acima_meta']) == "" ? "DEFAULT" : floatval($args['nr_percentual_acima_meta'])).",
					nr_meta                           = ".(trim($args['nr_meta']) == "" ? "DEFAULT" : floatval($args['nr_meta'])).",  
	            	observacao                        = ".(trim($args['observacao']) == "" ? "DEFAULT" : "'".trim($args["observacao"])."'")."
			  WHERE cd_auditoria_horas_previsto_realizado = ".intval($args['cd_auditoria_horas_previsto_realizado']).";";
		}
			
		$this->db->query($qr_sql);
	}

	function excluir($cd_auditoria_horas_previsto_realizado, $cd_usuario)
	{
		$qr_sql = " 
			UPDATE indicador_plugin.auditoria_horas_previsto_realizado 
			   SET dt_exclusao=current_timestamp, 
			   	   cd_usuario_exclusao=".intval($cd_usuario)." 
			 WHERE cd_auditoria_horas_previsto_realizado =".intval($cd_auditoria_horas_previsto_realizado).";"; 
		 
		$this->db->query($qr_sql);
	}

	/*public function atualiza_fechar_periodo($args = array())
	{
		$qr_sql = "
			INSERT INTO indicador_plugin.auditoria_horas_previsto_realizado 
				 (
					dt_referencia, 
					dt_inclusao,
					dt_alteracao, 
					nr_horas_previstas, 
				   	nr_horas_realizadas,
				   	nr_previstas_realizadas,
					nr_meta,
					cd_indicador_tabela,
					fl_media, 
					cd_usuario_inclusao,
					cd_usuario_alteracao
				  ) 
			 VALUES 
				  ( 
					".(trim($args['dt_referencia']) == "" ? "DEFAULT" : "TO_DATE('".trim($args["dt_referencia"])."', 'DD/MM/YYYY')").",
					CURRENT_TIMESTAMP,
					CURRENT_TIMESTAMP,
					".(trim($args['nr_horas_previstas']) == "" ? "DEFAULT" : floatval($args['nr_horas_previstas'])).", 
					".(trim($args['nr_horas_realizadas']) == "" ? "DEFAULT" : floatval($args['nr_horas_realizadas'])).",
					".(trim($args['nr_previstas_realizadas']) == "" ? "DEFAULT" : floatval($args['nr_previstas_realizadas'])).",
					".(trim($args['nr_meta']) == "" ? "DEFAULT" : floatval($args['nr_meta'])).",
					".(intval($args['cd_indicador_tabela']) == 0 ? 'DEFAULT' : intval($args['cd_indicador_tabela'])).",
					'S',
					".intval($args['cd_usuario']).",
					".intval($args['cd_usuario'])."
				  );";

		$this->db->query($qr_sql);
	}
	public function fechar_periodo($args = array())
	{
		$qr_sql = "
			UPDATE indicador.indicador_tabela 
			   SET dt_fechamento_periodo         = CURRENT_TIMESTAMP, 
			       cd_usuario_fechamento_periodo = ".intval($args['cd_usuario'])." 
		     WHERE cd_indicador_tabela = ".intval($args['cd_indicador_tabela']).";";

		$this->db->query($qr_sql);
	}*/
	
}
?>