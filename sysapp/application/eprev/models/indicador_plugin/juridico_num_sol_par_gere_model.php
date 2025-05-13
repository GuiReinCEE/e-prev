<?php
class juridico_num_sol_par_gere_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	function listar( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT i.cd_juridico_num_sol_par_gere,
						   TO_CHAR(i.dt_referencia,'YYYY') AS ano_referencia,
						   TO_CHAR(i.dt_referencia,'MM/YYYY') AS mes_referencia,
						   i.dt_referencia,
						   i.cd_indicador_tabela,
						   i.fl_media,
						   i.observacao,
						   i.nr_meta,
						   i.nr_sg, 
						   i.nr_gri, 
						   i.nr_gap, 
						   i.nr_gb, 
						   i.nr_ga, 
						   i.nr_gc, 
						   i.nr_gf, 
						   i.nr_gin, 
						   i.nr_rh, 
						   i.nr_gad, 
						   i.nr_gi, 
						   i.nr_pre, 
						   i.nr_seg, 
						   i.nr_fin, 
						   i.nr_adm,
						   i.nr_total
					  FROM indicador_plugin.juridico_num_sol_par_gere i
					 WHERE i.dt_exclusao IS NULL
					   AND(i.fl_media = 'S' OR i.cd_indicador_tabela = ".intval($args['cd_indicador_tabela']).")
					 ORDER BY i.dt_referencia ASC;
			      ";

		$result = $this->db->query($qr_sql);
	}	
	
	function carrega_referencia(&$result, $args=array())
	{
		$qr_sql = "
			SELECT TO_CHAR(dt_referencia + '1 month'::interval, 'DD/MM/YYYY') AS dt_referencia_n, 
				   nr_meta, 
				   cd_indicador_tabela 
			  FROM indicador_plugin.juridico_num_sol_par_gere
			 WHERE dt_exclusao IS NULL 
			 ORDER BY dt_referencia DESC 
			 LIMIT 1;";
			 
		$result = $this->db->query($qr_sql);
	}	

	function carrega(&$result, $args=array())
	{
		$qr_sql = "
            SELECT cd_juridico_num_sol_par_gere,
                   TO_CHAR(dt_referencia,'DD/MM/YYYY') AS dt_referencia,
                   cd_indicador_tabela,
                   fl_media,
                   nr_meta,
                   observacao,
				   nr_sg, 
				   nr_gri, 
				   nr_gap, 
				   nr_gb, 
				   nr_ga, 
				   nr_gc, 
				   nr_gf, 
				   nr_gin, 
				   nr_rh, 
				   nr_gad, 
				   nr_gi, 
				   nr_pre, 
				   nr_seg, 
				   nr_fin, 
				   nr_adm,
				   nr_total				   
		      FROM indicador_plugin.juridico_num_sol_par_gere 
			 WHERE cd_juridico_num_sol_par_gere = ".intval($args['cd_juridico_num_sol_par_gere']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function salvar(&$result, $args=array())
	{
		if(intval($args['cd_juridico_num_sol_par_gere']) == 0)
		{
			$qr_sql = "
						INSERT INTO indicador_plugin.juridico_num_sol_par_gere 
							 (
								dt_referencia, 
								nr_sg, 
								nr_gri, 
								nr_gap, 
								nr_gb, 
								nr_ga, 
								nr_gc, 
								nr_gf, 
								nr_gin, 
								nr_rh, 
								nr_gad, 
								nr_gi, 
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
								".(trim($args['nr_sg']) == "" ? "DEFAULT" : floatval($args['nr_sg'])).",
								".(trim($args['nr_gri']) == "" ? "DEFAULT" : floatval($args['nr_gri'])).",
								".(trim($args['nr_gap']) == "" ? "DEFAULT" : floatval($args['nr_gap'])).",
								".(trim($args['nr_gb']) == "" ? "DEFAULT" : floatval($args['nr_gb'])).",
								".(trim($args['nr_ga']) == "" ? "DEFAULT" : floatval($args['nr_ga'])).",
								".(trim($args['nr_gc']) == "" ? "DEFAULT" : floatval($args['nr_gc'])).",
								".(trim($args['nr_gf']) == "" ? "DEFAULT" : floatval($args['nr_gf'])).",
								".(trim($args['nr_gin']) == "" ? "DEFAULT" : floatval($args['nr_gin'])).",
								".(trim($args['nr_rh']) == "" ? "DEFAULT" : floatval($args['nr_rh'])).",
								".(trim($args['nr_gad']) == "" ? "DEFAULT" : floatval($args['nr_gad'])).",
								".(trim($args['nr_gi']) == "" ? "DEFAULT" : floatval($args['nr_gi'])).",
								".(trim($args['nr_meta']) == "" ? "DEFAULT" : floatval($args['nr_meta'])).",
								".(intval($args['cd_indicador_tabela']) == 0 ? "DEFAULT" : intval($args['cd_indicador_tabela'])).",
								".(trim($args['fl_media']) == "" ? "''" : "'".trim($args["fl_media"])."'").",
								".(trim($args['observacao']) == "" ? "DEFAULT" : "'".trim($args["observacao"])."'").",
								".intval($args['cd_usuario']).",
								".intval($args['cd_usuario'])."
							  );
					  ";
		}
		else
		{
			$qr_sql = "
				UPDATE indicador_plugin.juridico_num_sol_par_gere
				   SET dt_referencia        = ".(trim($args['dt_referencia']) == "" ? "DEFAULT" : "TO_DATE('".trim($args["dt_referencia"])."', 'DD/MM/YYYY')").",
				       nr_sg                = ".(trim($args['nr_sg']) == "" ? "DEFAULT" : floatval($args['nr_sg'])).",
				       nr_gri               = ".(trim($args['nr_gri']) == "" ? "DEFAULT" : floatval($args['nr_gri'])).",
				       nr_gap               = ".(trim($args['nr_gap']) == "" ? "DEFAULT" : floatval($args['nr_gap'])).",
				       nr_gb                = ".(trim($args['nr_gb']) == "" ? "DEFAULT" : floatval($args['nr_gb'])).",
				       nr_ga                = ".(trim($args['nr_ga']) == "" ? "DEFAULT" : floatval($args['nr_ga'])).",
				       nr_gc                = ".(trim($args['nr_gc']) == "" ? "DEFAULT" : floatval($args['nr_gc'])).",
				       nr_gf                = ".(trim($args['nr_gf']) == "" ? "DEFAULT" : floatval($args['nr_gf'])).",
				       nr_gin               = ".(trim($args['nr_gin']) == "" ? "DEFAULT" : floatval($args['nr_gin'])).",
				       nr_rh                = ".(trim($args['nr_rh']) == "" ? "DEFAULT" : floatval($args['nr_rh'])).",
				       nr_gad               = ".(trim($args['nr_gad']) == "" ? "DEFAULT" : floatval($args['nr_gad'])).",
				       nr_gi				= ".(trim($args['nr_gi']) == "" ? "DEFAULT" : floatval($args['nr_gi'])).",       
					   nr_meta              = ".(trim($args['nr_meta']) == "" ? "DEFAULT" : floatval($args['nr_meta'])).",
					   cd_indicador_tabela  = ".(intval($args['cd_indicador_tabela']) == 0 ? "DEFAULT" : intval($args['cd_indicador_tabela'])).",
					   fl_media             = ".(trim($args['fl_media']) == "" ? "''" : "'".trim($args["fl_media"])."'").",
					   observacao           = ".(trim($args['observacao']) == "" ? "DEFAULT" : "'".trim($args["observacao"])."'").",
					   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
					   dt_alteracao         = CURRENT_TIMESTAMP
			     WHERE cd_juridico_num_sol_par_gere = ".intval($args['cd_juridico_num_sol_par_gere']).";";
		}

		$result = $this->db->query($qr_sql);
	}	

	function excluir(&$result, $args=array())
	{
		$qr_sql = " 
			UPDATE indicador_plugin.juridico_num_sol_par_gere
		       SET dt_exclusao         = CURRENT_TIMESTAMP, 
			       cd_usuario_exclusao = ".intval($args['cd_usuario'])."
		     WHERE cd_juridico_num_sol_par_gere = ".intval($args['cd_juridico_num_sol_par_gere']).";"; 
			 
		$result = $this->db->query($qr_sql);
	}
	
	function atualiza_fechar_periodo(&$result, $args=array())
	{
		$qr_sql = "
					INSERT INTO indicador_plugin.juridico_num_sol_par_gere 
						 (
							dt_referencia, 
							nr_sg, 
							nr_gri, 
							nr_gap, 
							nr_gb, 
							nr_ga, 
							nr_gc, 
							nr_gf, 
							nr_gin, 
							nr_rh, 
							nr_gad, 
							nr_gi, 
							nr_meta, 
							cd_indicador_tabela, 
							fl_media, 
							cd_usuario_inclusao,
							cd_usuario_alteracao
						  ) 
					 VALUES 
						  ( 
							".(trim($args['dt_referencia']) == "" ? "DEFAULT" : "TO_DATE('".trim($args["dt_referencia"])."', 'DD/MM/YYYY')").",
							".(trim($args['nr_sg']) == "" ? "DEFAULT" : floatval($args['nr_sg'])).",
							".(trim($args['nr_gri']) == "" ? "DEFAULT" : floatval($args['nr_gri'])).",
							".(trim($args['nr_gap']) == "" ? "DEFAULT" : floatval($args['nr_gap'])).",
							".(trim($args['nr_gb']) == "" ? "DEFAULT" : floatval($args['nr_gb'])).",
							".(trim($args['nr_ga']) == "" ? "DEFAULT" : floatval($args['nr_ga'])).",
							".(trim($args['nr_gc']) == "" ? "DEFAULT" : floatval($args['nr_gc'])).",
							".(trim($args['nr_gf']) == "" ? "DEFAULT" : floatval($args['nr_gf'])).",
							".(trim($args['nr_gin']) == "" ? "DEFAULT" : floatval($args['nr_gin'])).",
							".(trim($args['nr_rh']) == "" ? "DEFAULT" : floatval($args['nr_rh'])).",
							".(trim($args['nr_gad']) == "" ? "DEFAULT" : floatval($args['nr_gad'])).",
							".(trim($args['nr_gi']) == "" ? "DEFAULT" : floatval($args['nr_gi'])).",
							".(trim($args['nr_meta']) == "" ? "DEFAULT" : floatval($args['nr_meta'])).",
							".(intval($args['cd_indicador_tabela']) == 0 ? "DEFAULT" : intval($args['cd_indicador_tabela'])).",
							'S',
							".intval($args['cd_usuario']).",
							".intval($args['cd_usuario'])."
						  );
				  "; 

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