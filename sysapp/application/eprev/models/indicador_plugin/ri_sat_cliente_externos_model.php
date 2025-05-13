<?php
class ri_sat_cliente_externos_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_ri_sat_cliente_externos,
			       cd_indicador_tabela,
			       dt_referencia,
				   TO_CHAR(dt_referencia,'YYYY') AS ano_referencia,
				   TO_CHAR(dt_referencia,'MM/YYYY') AS mes_referencia,
				   TO_CHAR(dt_inclusao,'DD/MM/YYYY') AS dt_inclusao,
				   TO_CHAR(dt_exclusao,'DD/MM/YYYY') AS dt_exclusao,
				   cd_usuario_inclusao,
				   cd_usuario_exclusao,
				   fl_media,
				   observacao,
				   nr_valor_1,
				   nr_valor_2,
				   nr_percentual_f,
				   nr_meta
		      FROM indicador_plugin.ri_sat_cliente_externos
		     WHERE dt_exclusao IS NULL
		       AND (fl_media = 'S' OR cd_indicador_tabela = ".intval($args['cd_indicador_tabela']).")
		      ORDER BY dt_referencia ASC;";

		$result = $this->db->query($qr_sql);
	}
	
	function carrega_referencia(&$result, $args=array())
	{
		$qr_sql = "
			SELECT TO_CHAR(dt_referencia + '1 month'::interval,'DD/MM/YYYY') AS dt_referencia_n, 
				   nr_meta, 
				   cd_indicador_tabela , 
				   cd_indicador_tabela,
				   (SELECT COUNT(*)
				      FROM indicador_plugin.investimento_rentabilidade_competitiva
				     WHERE TO_CHAR(dt_referencia, 'YYYY')::integer = ".intval($args['nr_ano_referencia'])."
				       AND dt_exclusao IS NULL) AS qt_ano
			  FROM indicador_plugin.ri_sat_cliente_externos 
			 WHERE dt_exclusao IS NULL 
			 ORDER BY dt_referencia DESC 
			 LIMIT 1;";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function carrega(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_ri_sat_cliente_externos,
				   cd_indicador_tabela,
				   TO_CHAR(dt_referencia,'DD/MM/YYYY') AS dt_referencia,
				   nr_valor_1,
				   nr_valor_2,
				   nr_percentual_f,
				   nr_meta,
				   fl_media,
				   observacao
			  FROM indicador_plugin.ri_sat_cliente_externos
			 WHERE cd_ri_sat_cliente_externos = ".intval($args['cd_ri_sat_cliente_externos']).";";
			 
		$result = $this->db->query($qr_sql);	
	}	

	function salvar(&$result, $args=array())
	{
		if(intval($args['cd_ri_sat_cliente_externos']) == 0)
		{
			$qr_sql = "
				INSERT INTO indicador_plugin.ri_sat_cliente_externos 
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
					    ".(trim($args['nr_valor_1']) == "" ? "DEFAULT" : floatval($args['nr_valor_1'])).",
					    ".(trim($args['nr_valor_2']) == "" ? "DEFAULT" : floatval($args['nr_valor_2'])).",
					    ".(trim($args['nr_meta']) == "" ? "DEFAULT" : floatval($args['nr_meta'])).",
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
				UPDATE indicador_plugin.ri_sat_cliente_externos
				   SET dt_referencia        = ".(trim($args['dt_referencia']) == "" ? "DEFAULT" : "TO_DATE('".trim($args["dt_referencia"])."', 'DD/MM/YYYY')").",
				       nr_valor_1           = ".(trim($args['nr_valor_1']) == "" ? "DEFAULT" : floatval($args['nr_valor_1'])).",
					   nr_valor_2           = ".(trim($args['nr_valor_2']) == "" ? "DEFAULT" : floatval($args['nr_valor_2'])).",
	                   nr_meta              = ".(trim($args['nr_meta']) == "" ? "DEFAULT" : floatval($args['nr_meta'])).",
					   cd_indicador_tabela  = ".(intval($args['cd_indicador_tabela']) == 0 ? "DEFAULT" : intval($args['cd_indicador_tabela'])).",
					   fl_media             = ".(trim($args['fl_media']) == "" ? "''" : "'".trim($args["fl_media"])."'").",
					   observacao           = ".(trim($args['observacao']) == "" ? "DEFAULT" : "'".trim($args["observacao"])."'").",
					   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
					   dt_alteracao         = CURRENT_TIMESTAMP
			     WHERE cd_ri_sat_cliente_externos = ".intval($args['cd_ri_sat_cliente_externos']).";";
		}

		$result = $this->db->query($qr_sql);
	}

	function excluir(&$result, $args=array())
	{
		$qr_sql = " 
			UPDATE indicador_plugin.ri_sat_cliente_externos
		       SET dt_exclusao         = CURRENT_TIMESTAMP, 
			       cd_usuario_exclusao = ".intval($args['cd_usuario'])."
		     WHERE cd_ri_sat_cliente_externos = ".intval($args['cd_ri_sat_cliente_externos']).";"; 
			 
		$result = $this->db->query($qr_sql);
	}
	
	function fechar_periodo(&$result, $args=array())
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