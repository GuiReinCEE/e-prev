<?php
class Variacao_orcamentaria_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT cd_variacao_orcamentaria,
				   cd_indicador_tabela,
				   TO_CHAR(dt_referencia,'YYYY') AS ano_referencia,
				   TO_CHAR(dt_referencia,'MM/YYYY') AS mes_referencia,
				   dt_referencia,
				   nr_valor_1,
				   nr_valor_2,
				   nr_resultado,
				   nr_meta_min,
				   nr_meta_max,
				   fl_media,
				   observacao
			  FROM indicador_pga.variacao_orcamentaria 
			 WHERE dt_exclusao IS NULL
			   AND (fl_media = 'S' OR cd_indicador_tabela = ".intval($args['cd_indicador_tabela']).")
			 ORDER BY dt_referencia ASC;";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function carrega_referencia(&$result, $args=array())
	{
		$qr_sql = "
			SELECT TO_CHAR(dt_referencia + '1 month'::interval,'DD/MM/YYYY') AS dt_referencia_n, 
				   nr_meta_min, 
				   nr_meta_max, 
				   cd_indicador_tabela 
			  FROM indicador_pga.variacao_orcamentaria 
			 WHERE dt_exclusao IS NULL 
			 ORDER BY dt_referencia DESC 
			 LIMIT 1;";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function carrega(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_variacao_orcamentaria,
				   cd_indicador_tabela,
				   TO_CHAR(dt_referencia,'DD/MM/YYYY') AS dt_referencia,
				   nr_valor_1,
				   nr_valor_2,
				   nr_resultado,
				   nr_meta_min,
				   nr_meta_max,
				   fl_media,
				   observacao
			  FROM indicador_pga.variacao_orcamentaria
			 WHERE cd_variacao_orcamentaria = ".intval($args['cd_variacao_orcamentaria']).";";
			 
		$result = $this->db->query($qr_sql);	
	}	

	function salvar(&$result, $args=array())
	{
		if(intval($args['cd_variacao_orcamentaria']) == 0)
		{
			$qr_sql = "
				INSERT INTO indicador_pga.variacao_orcamentaria
				     (
                       dt_referencia, 
					   nr_valor_1, 
                       nr_valor_2, 
					   nr_meta_min, 
					   nr_meta_max, 
					   cd_indicador_tabela, 
					   fl_media, 
                       observacao,
					   cd_usuario_inclusao,
					   cd_usuario_alteracao
					 )
                VALUES 
				     (
					   ".(trim($args['dt_referencia']) == "" ? "DEFAULT" : "TO_DATE('".trim($args["dt_referencia"])."', 'DD/MM/YYYY')").",
					   ".(trim($args['nr_valor_1']) == "" ? "DEFAULT" : floatval($args['nr_valor_1'])).",
					   ".(trim($args['nr_valor_2']) == "" ? "DEFAULT" : floatval($args['nr_valor_2'])).",
					   ".(trim($args['nr_meta_min']) == "" ? "DEFAULT" : floatval($args['nr_meta_min'])).",
					   ".(trim($args['nr_meta_max']) == "" ? "DEFAULT" : floatval($args['nr_meta_max'])).",
					   ".(intval($args['cd_indicador_tabela']) == 0 ? "DEFAULT" : intval($args['cd_indicador_tabela'])).",
					   ".(trim($args['fl_media']) == "" ? "''" : "'".trim($args["fl_media"])."'").",
					   ".(trim($args['observacao']) == "" ? "DEFAULT" : "'".trim($args["observacao"])."'").",
					   ".intval($args['cd_usuario']).",
					   ".intval($args['cd_usuario'])."
					 );";
		
		}
		else
		{
			$qr_sql = "
				UPDATE indicador_pga.variacao_orcamentaria
				   SET dt_referencia        = ".(trim($args['dt_referencia']) == "" ? "DEFAULT" : "TO_DATE('".trim($args["dt_referencia"])."', 'DD/MM/YYYY')").",
				       nr_valor_1           = ".(trim($args['nr_valor_1']) == "" ? "DEFAULT" : floatval($args['nr_valor_1'])).",
					   nr_valor_2           = ".(trim($args['nr_valor_2']) == "" ? "DEFAULT" : floatval($args['nr_valor_2'])).",
	                   nr_meta_min          = ".(trim($args['nr_meta_min']) == "" ? "DEFAULT" : floatval($args['nr_meta_min'])).",
	                   nr_meta_max          = ".(trim($args['nr_meta_max']) == "" ? "DEFAULT" : floatval($args['nr_meta_max'])).",
					   cd_indicador_tabela  = ".(intval($args['cd_indicador_tabela']) == 0 ? "DEFAULT" : intval($args['cd_indicador_tabela'])).",
					   fl_media             = ".(trim($args['fl_media']) == "" ? "''" : "'".trim($args["fl_media"])."'").",
					   observacao           = ".(trim($args['observacao']) == "" ? "DEFAULT" : "'".trim($args["observacao"])."'").",
					   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
					   dt_alteracao         = CURRENT_TIMESTAMP
				 WHERE cd_variacao_orcamentaria = ".intval($args['cd_variacao_orcamentaria']).";";
		}
	
		$this->db->query($qr_sql);		
	}

	function excluir(&$result, $args=array())
	{
		$qr_sql = " 
			UPDATE indicador_pga.variacao_orcamentaria 
			   SET dt_exclusao          = CURRENT_TIMESTAMP, 
				   cd_usuario_exclusao  = ".intval($args['cd_usuario'])."
			 WHERE cd_variacao_orcamentaria = ".intval($args['cd_variacao_orcamentaria']).";"; 
		$this->db->query($qr_sql);
	}	
	
	function fechar_periodo(&$result, $args=array())
	{
		$qr_sql = " 
			UPDATE indicador.indicador_tabela 
			   SET dt_fechamento_periodo         = CURRENT_TIMESTAMP, 
			       cd_usuario_fechamento_periodo = ".intval($args['cd_usuario'])."
			 WHERE cd_indicador_tabela = ".(intval($args['cd_indicador_tabela']) == 0 ? "DEFAULT" : intval($args['cd_indicador_tabela']))."; ";
			 
		$this->db->query($qr_sql);
	}	
	
	
}
?>