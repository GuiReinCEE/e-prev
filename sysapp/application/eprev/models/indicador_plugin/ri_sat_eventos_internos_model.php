<?php
class ri_sat_eventos_internos_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT i.cd_ri_sat_eventos_internos,
				   TO_CHAR(i.dt_referencia,'YYYY') AS ano_referencia,
				   TO_CHAR(i.dt_referencia,'MM/YYYY') AS mes_referencia,
				   i.dt_referencia,
				   i.cd_indicador_tabela,
				   i.fl_media,
				   i.observacao,
				   i.nr_valor_1,
				   i.nr_valor_2,
				   i.nr_percentual_f,
				   i.nr_meta,
				   CASE WHEN (SELECT MAX(i1.dt_referencia)
                                FROM indicador_plugin.ri_sat_eventos_internos i1
                               WHERE i1.dt_exclusao IS NULL
							     AND i1.dt_exclusao IS NULL) = i.dt_referencia THEN 'S'
					    ELSE 'N'
				   END AS fl_editar
		      FROM indicador_plugin.ri_sat_eventos_internos i
		     WHERE i.dt_exclusao IS NULL
		     ORDER BY i.dt_referencia ASC;";
		
		$result = $this->db->query($qr_sql);
	}
	
	function carrega_referencia(&$result, $args=array())
	{
		$qr_sql = "
			SELECT CASE WHEN TO_CHAR(dt_referencia, 'MM') = '02' THEN TO_CHAR(dt_referencia + '1 year'::interval, 'YYYY')
				        ELSE TO_CHAR(dt_referencia,'YYYY')
				   END AS ano_referencia,
				   CASE WHEN TO_CHAR(dt_referencia, 'MM') = '02' THEN '01'
				        ELSE '02'
				   END AS mes_referencia,
				   nr_meta, 
				   cd_indicador_tabela 
			  FROM indicador_plugin.ri_sat_eventos_internos
			 WHERE dt_exclusao IS NULL 
			 ORDER BY dt_referencia DESC 
			 LIMIT 1;";
			 
		$result = $this->db->query($qr_sql);
	}

	function carrega(&$result, $args=array())
	{
		$qr_sql = "
            SELECT cd_ri_sat_eventos_internos,
                   TO_CHAR(dt_referencia,'YYYY') AS ano_referencia,
                   TO_CHAR(dt_referencia,'MM') AS mes_referencia,
                   cd_indicador_tabela,
                   fl_media,
                   nr_valor_1,
                   nr_valor_2,
                   nr_percentual_f,
                   nr_meta,
                   observacao
		      FROM indicador_plugin.ri_sat_eventos_internos 
			 WHERE cd_ri_sat_eventos_internos = ".intval($args['cd_ri_sat_eventos_internos']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function salvar(&$result, $args=array())
	{
		if(intval($args['cd_ri_sat_eventos_internos']) == 0)
		{
			$qr_sql = "
				INSERT INTO indicador_plugin.ri_sat_eventos_internos
				     (
                       dt_referencia, 
					   nr_valor_1, 
                       nr_valor_2, 
					   nr_meta, 
					   cd_indicador_tabela, 
					   fl_media, 
                       observacao,
					   cd_usuario_inclusao,
					   cd_usuario_alteracao
					 )
                VALUES 
				     (
					   ".(trim($args['dt_referencia']) == "" ? "DEFAULT" : "TO_DATE('".trim($args["dt_referencia"])."', 'DD/MM/YYYY')").",
					   ".(trim($args['nr_valor_1']) == "" ? "DEFAULT" : intval($args['nr_valor_1'])).",
					   ".(trim($args['nr_valor_2']) == "" ? "DEFAULT" : intval($args['nr_valor_2'])).",
					   ".(trim($args['nr_meta']) == "" ? "DEFAULT" : intval($args['nr_meta'])).",
					   ".(intval($args['cd_indicador_tabela']) == 0 ? "DEFAULT" : intval($args['cd_indicador_tabela'])).",
					   ".(trim($args['fl_media']) == "" ? "DEFAULT" : "'".trim($args["fl_media"])."'").",
					   ".(trim($args['observacao']) == "" ? "DEFAULT" : "'".trim($args["observacao"])."'").",
					   ".intval($args['cd_usuario']).",
					   ".intval($args['cd_usuario'])."
					 );";
		
		}
		else
		{
			$qr_sql = "
				UPDATE indicador_plugin.ri_sat_eventos_internos
				   SET dt_referencia        = ".(trim($args['dt_referencia']) == "" ? "DEFAULT" : "TO_DATE('".trim($args["dt_referencia"])."', 'DD/MM/YYYY')").",
				       nr_valor_1           = ".(trim($args['nr_valor_1']) == "" ? "DEFAULT" : intval($args['nr_valor_1'])).",
					   nr_valor_2           = ".(trim($args['nr_valor_2']) == "" ? "DEFAULT" : intval($args['nr_valor_2'])).",
	                   nr_meta              = ".(trim($args['nr_meta']) == "" ? "DEFAULT" : intval($args['nr_meta'])).",
					   cd_indicador_tabela  = ".(intval($args['cd_indicador_tabela']) == 0 ? "DEFAULT" : intval($args['cd_indicador_tabela'])).",
					   fl_media             = ".(trim($args['fl_media']) == "" ? "DEFAULT" : "'".trim($args["fl_media"])."'").",
					   observacao           = ".(trim($args['observacao']) == "" ? "DEFAULT" : "'".trim($args["observacao"])."'").",
					   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
					   dt_alteracao         = CURRENT_TIMESTAMP
				 WHERE cd_ri_sat_eventos_internos = ".intval($args['cd_ri_sat_eventos_internos']).";";
		}
	

		$this->db->query($qr_sql);		
	}
	
	function excluir(&$result, $args=array())
	{
		$qr_sql = " 
			UPDATE indicador_plugin.ri_sat_eventos_internos 
			   SET dt_exclusao          = CURRENT_TIMESTAMP, 
				   cd_usuario_exclusao  = ".intval($args['cd_usuario'])."
			 WHERE cd_ri_sat_eventos_internos = ".intval($args['cd_ri_sat_eventos_internos']).";"; 
		$this->db->query($qr_sql);
	}	
}
?>