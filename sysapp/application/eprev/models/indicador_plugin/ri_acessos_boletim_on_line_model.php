<?php
class ri_acessos_boletim_on_line_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_ri_acessos_boletim_on_line,
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
		      FROM indicador_plugin.ri_acessos_boletim_on_line
		     WHERE dt_exclusao IS NULL
		       AND (
			          fl_media = 'S' 
			          OR cd_indicador_tabela = ".intval($args['cd_indicador_tabela'])."
		            )
		      ORDER BY dt_referencia ASC;";

		$result = $this->db->query($qr_sql);
	}
	
	function carrega_novo(&$result, $args=array())
	{
		$qr_sql = "
			SELECT TO_CHAR(dt_referencia + '1 month'::interval,'DD/MM/YYYY') AS dt_referencia, 
				   nr_meta, 
				   cd_indicador_tabela
			  FROM indicador_plugin.ri_acessos_boletim_on_line
			 WHERE dt_exclusao IS NULL 
			 ORDER BY dt_referencia DESC LIMIT 1;";
	 
		$result = $this->db->query($qr_sql);
	}
	
	function carrega(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_ri_acessos_boletim_on_line,
			       cd_indicador_tabela,
				   TO_CHAR(dt_referencia,'YYYY') AS ano_referencia,
				   TO_CHAR(dt_referencia,'MM/YYYY') AS mes_referencia,
				   TO_CHAR(dt_referencia,'DD/MM/YYYY') AS dt_referencia,
				   TO_CHAR(dt_inclusao,'DD/MM/YYYY') AS dt_inclusao,
				   TO_CHAR(dt_exclusao,'DD/MM/YYYY') AS dt_exclusao,
				   cd_usuario_inclusao,
				   cd_usuario_exclusao,
				   fl_media,
				   nr_valor_1,
				   nr_valor_2,
				   nr_percentual_f,
				   nr_meta 
		      FROM indicador_plugin.ri_acessos_boletim_on_line 
			 WHERE cd_ri_acessos_boletim_on_line = ".intval($args['cd']).";";
			 
		$result = $this->db->query($qr_sql);
	}

	function salvar(&$result, $args=array())
	{
		if(intval($args['cd_ri_acessos_boletim_on_line']) == 0)
		{
			$qr_sql = "
				INSERT INTO indicador_plugin.ri_acessos_boletim_on_line 
				     (
						dt_referencia,
						dt_inclusao,
						cd_usuario_inclusao,
						cd_indicador_tabela,
						fl_media,
						nr_valor_1,
						nr_valor_2,
						nr_meta,
						nr_percentual_f
			          ) 
			     VALUES 
				      ( 
						TO_DATE('".$args["dt_referencia"]."', 'DD/MM/YYYY'),
						CURRENT_TIMESTAMP,
						".intval($args["cd_usuario_inclusao"]).",
						".intval($args["cd_indicador_tabela"]).",
						'".trim($args["fl_media"])."',
						".$args["nr_valor_1"].",
						".$args["nr_valor_2"].",
						".$args["nr_meta"].",
						((".floatval($args["nr_valor_2"]) / floatval($args["nr_valor_1"]).") * 100)
                      );";
		}
		else
		{
			$qr_sql = "
				UPDATE indicador_plugin.ri_acessos_boletim_on_line
				   SET dt_referencia       = TO_DATE('".$args["dt_referencia"]."', 'DD/MM/YYYY'),
					   cd_indicador_tabela = ".intval($args["cd_indicador_tabela"]).",
					   fl_media            = '".trim($args["fl_media"])."',
					   nr_valor_1          = ".$args["nr_valor_1"].",
					   nr_valor_2          = ".$args["nr_valor_2"].",
					   nr_meta             = ".$args["nr_meta"].",
					   nr_percentual_f     = ((".floatval($args["nr_valor_2"]) / floatval($args["nr_valor_1"]).") * 100)
			     WHERE cd_ri_acessos_boletim_on_line = ".intval($args['cd_ri_acessos_boletim_on_line']).";";
		}

		$result = $this->db->query($qr_sql);
	}

	function excluir(&$result, $args=array())
	{
		$qr_sql = " 
			UPDATE indicador_plugin.ri_acessos_boletim_on_line
		       SET dt_exclusao         = CURRENT_TIMESTAMP, 
			       cd_usuario_exclusao = ".intval($args['cd_usuario'])."
		     WHERE cd_ri_acessos_boletim_on_line = ".intval($args['cd']).";"; 
			 
		$result = $this->db->query($qr_sql);
	}
	
	function fecha_indicador(&$result, $args=array())
	{
		$qr_sql = "
			INSERT INTO indicador_plugin.ri_acessos_boletim_on_line
				 ( 
					dt_referencia,
					dt_inclusao,
					cd_usuario_inclusao, 
					nr_valor_1,
					nr_valor_2,
					nr_percentual_f, 
					nr_meta, 
					fl_media 
				  ) 
			 VALUES 
				  ( 
					TO_DATE('".intval($args['nr_ano_referencia'])."-01-01','YYYY-MM-DD'),
					CURRENT_TIMESTAMP,
					".$args['cd_usuario'].", 
					".$args['nr_tot_ano_visita'].", 
					".$args['nr_tot_ano_contato'].", 
					".$args['resultado_ano'].", 
					".$args['nr_meta'].",
					'S' 
				  );
				  
			UPDATE indicador.indicador_tabela 
			   SET dt_fechamento_periodo         = CURRENT_TIMESTAMP, 
			       cd_usuario_fechamento_periodo = ".$args['cd_usuario']." 
		     WHERE cd_indicador_tabela = ".intval($args['cd_indicador_tabela']).";";
							  
		$result = $this->db->query($qr_sql);
	}
}
?>