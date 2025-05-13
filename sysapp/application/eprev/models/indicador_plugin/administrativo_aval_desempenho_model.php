<?php
class administrativo_aval_desempenho_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	public function listar($cd_indicador_tabela)
	{
		$qr_sql = "
			SELECT i.cd_administrativo_aval_desempenho,
                   TO_CHAR(i.dt_inclusao,'DD/MM/YYYY') AS dt_inclusao,
                   i.cd_usuario_inclusao,
                   TO_CHAR(i.dt_exclusao,'DD/MM/YYYY') AS dt_exclusao,
                   i.cd_usuario_exclusao,
                   i.cd_indicador_tabela,
                   i.fl_media,
                   i.periodo_ini,
				   i.periodo_fim,
                   i.nr_valor_1,
                   i.nr_valor_2,
                   i.nr_percentual_f,
                   i.nr_meta,
                   i.observacao
			  FROM indicador_plugin.administrativo_aval_desempenho i
			 WHERE i.dt_exclusao IS NULL";           

		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega_referencia()
	{
		$qr_sql = "
			SELECT nr_meta, 
			       cd_indicador_tabela,
			       periodo_fim AS periodo_ini,
			       periodo_fim + 1 AS periodo_ref
			  FROM indicador_plugin.administrativo_aval_desempenho 
			 WHERE dt_exclusao IS NULL 
			 ORDER BY periodo_ini desc
			 LIMIT 1";
			 
		return $this->db->query($qr_sql)->row_array();
	}

	public function carrega($cd_administrativo_aval_desempenho)
	{
		$qr_sql = "
            SELECT cd_administrativo_aval_desempenho,
                   cd_indicador_tabela,
                   fl_media,
                   periodo_ini,
				   periodo_fim,
                   nr_valor_1,
                   nr_valor_2,
                   nr_percentual_f,
                   nr_meta,
                   observacao
		      FROM indicador_plugin.administrativo_aval_desempenho 
			 WHERE cd_administrativo_aval_desempenho = ".intval($cd_administrativo_aval_desempenho).";";
			 
		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args)
	{
		if(intval($args['cd_administrativo_aval_desempenho']) == 0)
		{
			$qr_sql = "
				INSERT INTO indicador_plugin.administrativo_aval_desempenho 
				( 
					dt_inclusao, 
					cd_usuario_inclusao,  
					cd_indicador_tabela, 
					fl_media, 
					periodo_ini,
					periodo_fim,
					nr_valor_1,
                    nr_valor_2,
					nr_percentual_f,
					nr_meta,  
		            observacao
		        ) 
		        VALUES 
		        ( 
					CURRENT_TIMESTAMP, 
			 		".intval($args['cd_usuario']).", 
					".(intval($args['cd_indicador_tabela']) == 0 ? "DEFAULT" : intval($args['cd_indicador_tabela'])).",  
					".(trim($args['fl_media']) == "" ? "''" : "'".trim($args["fl_media"])."'").",
					".(trim($args['periodo_ini']) == "" ? "DEFAULT" : intval($args['periodo_ini'])).",
					".(trim($args['periodo_fim']) == "" ? "DEFAULT" : intval($args['periodo_fim'])).",
					".(trim($args['nr_valor_1']) == "" ? "DEFAULT" : floatval($args['nr_valor_1'])).",
					".(trim($args['nr_valor_2']) == "" ? "DEFAULT" : floatval($args['nr_valor_2'])).", 
					".(trim($args['nr_percentual_f']) == "" ? "DEFAULT" : floatval($args['nr_percentual_f'])).", 
					".(trim($args['nr_meta']) == "" ? "DEFAULT" : floatval($args['nr_meta'])).", 
            		".(trim($args['observacao']) == "" ? "DEFAULT" : "'".trim($args["observacao"])."'")."
				);";
		}
		else
		{
			$qr_sql = "
				UPDATE indicador_plugin.administrativo_aval_desempenho 
				SET cd_administrativo_aval_desempenho = ".intval($args['cd_administrativo_aval_desempenho']).",
					cd_indicador_tabela    = ".(intval($args['cd_indicador_tabela']) == 0 ? "DEFAULT" : intval($args['cd_indicador_tabela'])).",  
					fl_media               = ".(trim($args['fl_media']) == "" ? "''" : "'".trim($args["fl_media"])."'").", 
					periodo_ini            = ".(trim($args['periodo_ini']) == "" ? "DEFAULT" : intval($args['periodo_ini'])).",
					periodo_fim            = ".(trim($args['periodo_fim']) == "" ? "DEFAULT" : intval($args['periodo_fim'])).",
					nr_valor_1             = ".(trim($args['nr_valor_1']) == "" ? "DEFAULT" : floatval($args['nr_valor_1'])).",					
                    nr_valor_2             = ".(trim($args['nr_valor_2']) == "" ? "DEFAULT" : floatval($args['nr_valor_2'])).", 
					nr_percentual_f        = ".(trim($args['nr_percentual_f']) == "" ? "DEFAULT" : floatval($args['nr_percentual_f'])).",  
					nr_meta                = ".(trim($args['nr_meta']) == "" ? "DEFAULT" : floatval($args['nr_meta'])).",  
	            	observacao             = ".(trim($args['observacao']) == "" ? "DEFAULT" : "'".trim($args["observacao"])."'")."
			  WHERE cd_administrativo_aval_desempenho = ".intval($args['cd_administrativo_aval_desempenho']).";";
		}
			
		$this->db->query($qr_sql);
	}

	public function excluir($cd_administrativo_aval_desempenho, $cd_usuario)
	{
		$qr_sql = " 
			UPDATE indicador_plugin.administrativo_aval_desempenho 
			   SET dt_exclusao = CURRENT_TIMESTAMP, 
			       cd_usuario_exclusao = ".intval($cd_usuario)." 
			 WHERE cd_administrativo_aval_desempenho =".intval($cd_administrativo_aval_desempenho).";"; 
		 
		$this->db->query($qr_sql);
	}
}
?>