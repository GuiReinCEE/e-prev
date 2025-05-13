<?php
class administrativo_aval_fornecedor_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	public function listar($cd_indicador_tabela)
	{
		$qr_sql = "
			SELECT cd_administrativo_aval_fornecedor, 
			       TO_CHAR(dt_referencia,'MM/YYYY') AS mes_ano_referencia,
				   TO_CHAR(dt_referencia,'YYYY') AS ano_referencia, 
				   TO_CHAR(dt_referencia,'MM') AS mes_referencia, 
				   dt_referencia, 
				   cd_indicador_tabela, 
				   fl_media, 
				   nr_valor_1, 
				   nr_valor_2, 
				   nr_percentual_f, 
				   nr_meta, 
			       observacao
			  FROM indicador_plugin.administrativo_aval_fornecedor 
			 WHERE dt_exclusao IS NULL
	           AND (fl_media ='S' OR cd_indicador_tabela = ".intval($cd_indicador_tabela).")
			 ORDER BY dt_referencia ASC";

		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega_referencia($nr_ano_referencia)
	{
		$qr_sql = "
			SELECT TO_CHAR(dt_referencia + '1 month'::interval,'DD/MM/YYYY') AS dt_referencia_n, 
				   nr_meta, 
				   cd_indicador_tabela,
				   (SELECT COUNT(*)
				      FROM indicador_plugin.administrativo_aval_fornecedor
				     WHERE TO_CHAR(dt_referencia, 'YYYY')::integer = ".intval($nr_ano_referencia)."
				       AND dt_exclusao IS NULL) AS qt_ano
			  FROM indicador_plugin.administrativo_aval_fornecedor 
			 WHERE dt_exclusao IS NULL
			 ORDER BY dt_referencia DESC 
			 LIMIT 1;";
			 
		return $this->db->query($qr_sql)->row_array();
	}

	public function carrega($cd_administrativo_aval_fornecedor)
	{
		$qr_sql = " 
			SELECT cd_administrativo_aval_fornecedor,
			 	   TO_CHAR(dt_referencia,'YYYY') as ano_referencia, 
				   TO_CHAR(dt_referencia,'MM/YYYY') as mes_referencia, 
				   TO_CHAR(dt_referencia,'DD/MM/YYYY') as dt_referencia, 
				   TO_CHAR(dt_inclusao,'DD/MM/YYYY') as dt_inclusao, 
				   cd_usuario_inclusao, 
				   TO_CHAR(dt_exclusao,'DD/MM/YYYY') as dt_exclusao, 
				   cd_usuario_exclusao, 
				   cd_indicador_tabela, 
				   fl_media, 
			       observacao, 
				   nr_valor_1, 
				   nr_valor_2, 
				   nr_percentual_f, 
				   nr_meta 
			  FROM indicador_plugin.administrativo_aval_fornecedor 
		 	 WHERE cd_administrativo_aval_fornecedor= ".intval($cd_administrativo_aval_fornecedor).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args)
	{
		if(intval($args['cd_administrativo_aval_fornecedor']) == 0)
		{
			$qr_sql = "
				INSERT INTO indicador_plugin.administrativo_aval_fornecedor 
				( 
					dt_referencia,  
					dt_inclusao, 
					cd_usuario_inclusao,  
					cd_indicador_tabela, 
					fl_media, 
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
					".(trim($args['nr_percentual_f']) == "" ? "DEFAULT" : floatval($args['nr_percentual_f'])).", 
					".(trim($args['nr_meta']) == "" ? "DEFAULT" : floatval($args['nr_meta'])).", 
            		".(trim($args['observacao']) == "" ? "DEFAULT" : "'".trim($args["observacao"])."'")."
				);";
		}
		else
		{
			$qr_sql = "
				UPDATE indicador_plugin.administrativo_aval_fornecedor 
				SET cd_administrativo_aval_fornecedor = ".intval($args['cd_administrativo_aval_fornecedor']).",
					dt_referencia					  = ".(trim($args['dt_referencia']) == "" ? "DEFAULT" : "TO_DATE('".trim($args["dt_referencia"])."', 'DD/MM/YYYY')").",  
					cd_indicador_tabela               = ".(intval($args['cd_indicador_tabela']) == 0 ? "DEFAULT" : intval($args['cd_indicador_tabela'])).",  
					fl_media                          = ".(trim($args['fl_media']) == "" ? "''" : "'".trim($args["fl_media"])."'").", 
					nr_percentual_f                   = ".(trim($args['nr_percentual_f']) == "" ? "DEFAULT" : floatval($args['nr_percentual_f'])).",  
					nr_meta                           = ".(trim($args['nr_meta']) == "" ? "DEFAULT" : floatval($args['nr_meta'])).",  
	            	observacao                        = ".(trim($args['observacao']) == "" ? "DEFAULT" : "'".trim($args["observacao"])."'")."
			  WHERE cd_administrativo_aval_fornecedor = ".intval($args['cd_administrativo_aval_fornecedor']).";";
		}
			
		$this->db->query($qr_sql);
	}

	function excluir($cd_administrativo_aval_fornecedor, $cd_usuario)
	{
		$qr_sql = " 
			UPDATE indicador_plugin.administrativo_aval_fornecedor 
			   SET dt_exclusao=current_timestamp, cd_usuario_exclusao=".intval($cd_usuario)." 
			 WHERE cd_administrativo_aval_fornecedor =".intval($cd_administrativo_aval_fornecedor).";"; 
		 
		$this->db->query($qr_sql);
	}

	public function atualiza_fechar_periodo($args = array())
	{
		$qr_sql = "
			INSERT INTO indicador_plugin.administrativo_aval_fornecedor 
				 (
					dt_referencia, 
					dt_inclusao, 
					nr_percentual_f,
					nr_meta,
					cd_indicador_tabela,
					fl_media, 
					cd_usuario_inclusao
				  ) 
			 VALUES 
				  ( 
					".(trim($args['dt_referencia']) == "" ? "DEFAULT" : "TO_DATE('".trim($args["dt_referencia"])."', 'DD/MM/YYYY')").",
					CURRENT_TIMESTAMP,
					".(trim($args['nr_percentual_f']) == "" ? "DEFAULT" : floatval($args['nr_percentual_f'])).",
					".(trim($args['nr_meta']) == "" ? "DEFAULT" : floatval($args['nr_meta'])).",
					".(intval($args['cd_indicador_tabela']) == 0 ? 'DEFAULT' : intval($args['cd_indicador_tabela'])).",
					'S',
					".intval($args['cd_usuario'])."
				  );";

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