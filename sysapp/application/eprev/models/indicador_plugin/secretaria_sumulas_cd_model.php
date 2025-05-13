<?php
class Secretaria_sumulas_cd_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT i.cd_secretaria_sumulas_cd,
				   TO_CHAR(i.dt_referencia,'YYYY') as ano_referencia,
				   TO_CHAR(i.dt_referencia,'MM/YYYY') as mes_referencia,
				   i.dt_referencia,
				   TO_CHAR(i.dt_inclusao,'DD/MM/YYYY') as dt_inclusao,
				   i.cd_usuario_inclusao,
				   TO_CHAR(i.dt_exclusao,'DD/MM/YYYY') as dt_exclusao,
				   i.cd_usuario_exclusao,
				   i.cd_indicador_tabela,
				   i.fl_media,
        		   i.observacao,
				   i.nr_valor_1,
				   i.nr_valor_2,
				   i.nr_percentual_f,
				   i.nr_meta
			  FROM indicador_plugin.secretaria_sumulas_cd i
			 WHERE i.dt_exclusao IS NULL
		       AND (i.fl_media='S' OR i.cd_indicador_tabela = ".intval($args['cd_indicador_tabela']).")
		     ORDER BY dt_referencia ASC";

		$result = $this->db->query($qr_sql);
	}


	public function carrega_referencia(&$result)
	{
		$qr_sql = "
			SELECT TO_CHAR(dt_referencia + '1 month'::interval,'DD/MM/YYYY') AS dt_referencia_n, 
				   nr_meta, 
				   cd_indicador_tabela
			  FROM indicador_plugin.secretaria_sumulas_cd 
			 WHERE dt_exclusao IS NULL 
			 ORDER BY dt_referencia DESC
			 LIMIT 1;";

		$result = $this->db->query($qr_sql);
	}

	public function carregar(&$result, $args=array())
	{
		$qr_sql = " 
			SELECT cd_secretaria_sumulas_cd, 
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
			  FROM indicador_plugin.secretaria_sumulas_cd 
		     WHERE cd_secretaria_sumulas_cd = ".intval($args['cd_secretaria_sumulas_cd']).";";

		$result = $this->db->query($qr_sql);
	}

	function salvar($args)
	{
		if(intval($args['cd_secretaria_sumulas_cd'])==0)
		{
			$qr_sql="
			  INSERT INTO indicador_plugin.secretaria_sumulas_cd 
			  		(
			  			dt_referencia, 
						dt_inclusao,
						nr_meta, 
						cd_indicador_tabela,
						fl_media,
            			observacao,
						nr_valor_1,
						nr_valor_2,
						cd_usuario_inclusao
					) 
			   VALUES 
					( 
						".(trim($args['dt_referencia']) == "" ? "DEFAULT" : "TO_DATE('".trim($args["dt_referencia"])."', 'DD/MM/YYYY')").", 
						CURRENT_TIMESTAMP,
						".(trim($args['nr_meta']) == "" ? "DEFAULT" : floatval($args['nr_meta'])).",
						".(intval($args['cd_indicador_tabela']) == 0 ? "DEFAULT" : intval($args['cd_indicador_tabela'])).", 
						".(trim($args['fl_media']) == "" ? "''" : "'".trim($args["fl_media"])."'").",
           				".(trim($args['observacao']) == "" ? "DEFAULT" : "'".trim($args["observacao"])."'").",
						".(trim($args['nr_valor_1']) == "" ? "DEFAULT" : floatval($args['nr_valor_1'])).",
					    ".(trim($args['nr_valor_2']) == "" ? "DEFAULT" : floatval($args['nr_valor_2'])).",
						".intval($args['cd_usuario'])."
					)";
		}
		else
		{
			$qr_sql = "
				UPDATE indicador_plugin.secretaria_sumulas_cd 
				   SET dt_referencia         = ".(trim($args['dt_referencia']) == "" ? "DEFAULT" : "TO_DATE('".trim($args["dt_referencia"])."', 'DD/MM/YYYY')").",
			           cd_indicador_tabela   = ".(intval($args['cd_indicador_tabela']) == 0 ? "DEFAULT" : intval($args['cd_indicador_tabela'])).",  
					   fl_media              = ".(trim($args['fl_media']) == "" ? "''" : "'".trim($args["fl_media"])."'").",
			           observacao            = ".(trim($args['observacao']) == "" ? "DEFAULT" : "'".trim($args["observacao"])."'").",
					   nr_valor_1            = ".(trim($args['nr_valor_1']) == "" ? "DEFAULT" : floatval($args['nr_valor_1'])).",
					   nr_valor_2            = ".(trim($args['nr_valor_2']) == "" ? "DEFAULT" : floatval($args['nr_valor_2'])).",
					   nr_meta               = ".(trim($args['nr_meta']) == "" ? "DEFAULT" : floatval($args['nr_meta']))."
				 WHERE cd_secretaria_sumulas_cd = ".intval($args['cd_secretaria_sumulas_cd']).";";
		}

		
		$query = $this->db->query($qr_sql);
	}

	public function excluir($cd_secretaria_sumulas_cd, $cd_usuario)
	{
		$qr_sql = " 
			UPDATE indicador_plugin.secretaria_sumulas_cd 
			   SET dt_exclusao         = CURRENT_TIMESTAMP, 
			   	   cd_usuario_exclusao = ".intval($cd_usuario)." 
			 WHERE cd_secretaria_sumulas_cd = ".intval($cd_secretaria_sumulas_cd).";";

		$result = $this->db->query($qr_sql);
	}

	public function atualiza_fechar_periodo(&$result, $args=array())
	{
		$qr_sql = "
			INSERT INTO indicador_plugin.secretaria_sumulas_cd 
				 (
					dt_referencia, 
					dt_inclusao, 
					nr_percentual_f,
					nr_meta,
					nr_valor_1,
					nr_valor_2,
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
					".(trim($args['nr_valor_1']) == "" ? "DEFAULT" : intval($args['nr_valor_1'])).",
					".(trim($args['nr_valor_2']) == "" ? "DEFAULT" : intval($args['nr_valor_2'])).",
					".(intval($args['cd_indicador_tabela']) == 0 ? 'DEFAUL' : intval($args['cd_indicador_tabela'])).",
					'S',
					".intval($args['cd_usuario'])."
				  );";

		$result = $this->db->query($qr_sql);
	}
	
	public function fechar_periodo(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE indicador.indicador_tabela 
			   SET dt_fechamento_periodo         = CURRENT_TIMESTAMP, 
			   	   cd_usuario_fechamento_periodo = ".$args['cd_usuario']." 
		     WHERE cd_indicador_tabela = ".intval($args['cd_indicador_tabela']).";";

		$result = $this->db->query($qr_sql);
	}
}
?>