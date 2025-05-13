<?php
class Exp_satisfacao_com_acoes_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	public function listar($cd_indicador_tabela)
	{
		$qr_sql = "
			SELECT i.cd_exp_satisfacao_com_acoes,
                   TO_CHAR(i.dt_referencia,'YYYY') AS ano_referencia,
                   TO_CHAR(i.dt_referencia,'MM/YYYY') AS mes_referencia,
                   i.dt_referencia,
                   TO_CHAR(i.dt_inclusao,'DD/MM/YYYY') AS dt_inclusao,
                   i.cd_usuario_inclusao,
                   TO_CHAR(i.dt_exclusao,'DD/MM/YYYY') AS dt_exclusao,
                   i.cd_usuario_exclusao,
                   i.cd_indicador_tabela,
                   i.fl_media,
                   i.nr_valor_1,
                   i.nr_valor_2,
                   i.nr_percentual_f,
                   i.nr_meta,
                   i.observacao,
				   CASE WHEN (SELECT MAX(i1.dt_referencia)
				                FROM indicador_plugin.exp_satisfacao_com_acoes i1
			                   WHERE i1.dt_exclusao IS NULL) = i.dt_referencia
						THEN 'S'
						ELSE 'N'
				   END AS fl_editar
			  FROM indicador_plugin.exp_satisfacao_com_acoes i
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
			  FROM indicador_plugin.exp_satisfacao_com_acoes 
			 WHERE dt_exclusao IS NULL 
			 ORDER BY dt_referencia DESC 
			 LIMIT 1;";
			 
		return $this->db->query($qr_sql)->row_array();
	}

	public function carrega($cd_exp_satisfacao_com_acoes)
	{
		$qr_sql = "
            SELECT cd_exp_satisfacao_com_acoes,
                   TO_CHAR(dt_referencia, 'DD/MM/YYYY') AS dt_referencia,
                   TO_CHAR(dt_referencia, 'YYYY') AS ano_referencia,
                   cd_indicador_tabela,
                   fl_media,
                   nr_valor_1,
                   nr_valor_2,
                   nr_percentual_f,
                   nr_meta,
                   observacao
		      FROM indicador_plugin.exp_satisfacao_com_acoes 
			 WHERE cd_exp_satisfacao_com_acoes = ".intval($cd_exp_satisfacao_com_acoes).";";
			 
		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args)
	{
		if(intval($args['cd_exp_satisfacao_com_acoes']) == 0)
		{
			$qr_sql = "
				INSERT INTO indicador_plugin.exp_satisfacao_com_acoes 
				( 
					dt_referencia,  
					dt_inclusao, 
					cd_usuario_inclusao,  
					cd_indicador_tabela, 
					fl_media, 
					nr_valor_1,
                    nr_valor_2,
					nr_percentual_f,
					nr_meta,  
		            observacao
		        ) 
		        VALUES 
		        ( 
					".(trim($args['dt_referencia']) == "" ? "DEFAULT" : "TO_DATE('".trim($args["dt_referencia"])."', 'DD/MM/YYYY')").", 
			 		CURRENT_TIMESTAMP, 
			 		".intval($args['cd_usuario']).", 
					".(intval($args['cd_indicador_tabela']) == 0 ? "DEFAULT" : intval($args['cd_indicador_tabela'])).",  
					".(trim($args['fl_media']) == "" ? "''" : "'".trim($args["fl_media"])."'").",
					".(trim($args['nr_valor_1']) == "" ? "DEFAULT" : intval($args['nr_valor_1'])).",
					".(trim($args['nr_valor_2']) == "" ? "DEFAULT" : intval($args['nr_valor_2'])).", 
					".(trim($args['nr_percentual_f']) == "" ? "DEFAULT" : floatval($args['nr_percentual_f'])).", 
					".(trim($args['nr_meta']) == "" ? "DEFAULT" : floatval($args['nr_meta'])).", 
            		".(trim($args['observacao']) == "" ? "DEFAULT" : "'".trim($args["observacao"])."'")."
				);";
		}
		else
		{
			$qr_sql = "
				UPDATE indicador_plugin.exp_satisfacao_com_acoes 
				SET cd_exp_satisfacao_com_acoes = ".intval($args['cd_exp_satisfacao_com_acoes']).",
					dt_referencia          = ".(trim($args['dt_referencia']) == "" ? "DEFAULT" : "TO_DATE('".trim($args["dt_referencia"])."', 'DD/MM/YYYY')").",  
					cd_indicador_tabela    = ".(intval($args['cd_indicador_tabela']) == 0 ? "DEFAULT" : intval($args['cd_indicador_tabela'])).",  
					fl_media               = ".(trim($args['fl_media']) == "" ? "''" : "'".trim($args["fl_media"])."'").", 
					nr_valor_1             =".(trim($args['nr_valor_1']) == "" ? "DEFAULT" : intval($args['nr_valor_1'])).",					
                    nr_valor_2             = ".(trim($args['nr_valor_2']) == "" ? "DEFAULT" : intval($args['nr_valor_2'])).", 
					nr_percentual_f        = ".(trim($args['nr_percentual_f']) == "" ? "DEFAULT" : floatval($args['nr_percentual_f'])).",  
					nr_meta                = ".(trim($args['nr_meta']) == "" ? "DEFAULT" : floatval($args['nr_meta'])).",  
	            	observacao             = ".(trim($args['observacao']) == "" ? "DEFAULT" : "'".trim($args["observacao"])."'")."
			  WHERE cd_exp_satisfacao_com_acoes = ".intval($args['cd_exp_satisfacao_com_acoes']).";";
		}
			
		$this->db->query($qr_sql);
	}

	public function excluir($cd_exp_satisfacao_com_acoes, $cd_usuario)
	{
		$qr_sql = " 
			UPDATE indicador_plugin.exp_satisfacao_com_acoes 
			   SET dt_exclusao = CURRENT_TIMESTAMP, 
			       cd_usuario_exclusao = ".intval($cd_usuario)." 
			 WHERE cd_exp_satisfacao_com_acoes =".intval($cd_exp_satisfacao_com_acoes).";"; 
		 
		$this->db->query($qr_sql);
	}
}
?>