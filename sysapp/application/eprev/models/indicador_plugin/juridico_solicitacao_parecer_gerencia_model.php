<?php
class Juridico_solicitacao_parecer_gerencia_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	function listar( &$result, $args=array() )
	{


		$qr_sql = "
					SELECT i.cd_juridico_solicitacao_parecer_gerencia,
						   TO_CHAR(i.dt_referencia,'YYYY') AS ano_referencia,
						   TO_CHAR(i.dt_referencia,'MM/YYYY') AS mes_referencia,
						   i.dt_referencia,
						   i.cd_indicador_tabela,
						   i.fl_media,
						   i.observacao,
						   i.nr_meta,
						   i.nr_total,
						   i.nr_ac,
						   i.nr_ai,
						   i.nr_aj,
						   i.nr_gc,
						   i.nr_ge,
						   i.nr_gfc,
						   i.nr_ggs,
						   i.nr_gin,
						   i.nr_gp,
						   i.nr_sg,
						   i.nr_pre,
						   i.nr_prev,
						   i.nr_fin,
						   i.nr_infr
					  FROM indicador_plugin.juridico_solicitacao_parecer_gerencia i
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
			  FROM indicador_plugin.juridico_solicitacao_parecer_gerencia
			 WHERE dt_exclusao IS NULL 
			 ORDER BY dt_referencia DESC 
			 LIMIT 1;";
			 
		$result = $this->db->query($qr_sql);
	}	

	function carrega(&$result, $args=array())
	{
		$qr_sql = "
            SELECT cd_juridico_solicitacao_parecer_gerencia,
                   TO_CHAR(dt_referencia,'DD/MM/YYYY') AS dt_referencia,
                   cd_indicador_tabela,
                   fl_media,
                   nr_meta,
                   observacao,
				   nr_total,
				   nr_ac,
				   nr_ai,
				   nr_aj,
				   nr_gc,
				   nr_ge,
				   nr_gfc,
				   nr_ggs,
				   nr_gin,
				   nr_gp,
				   nr_sg,
				   nr_pre,
				   nr_prev,
				   nr_fin,
				   nr_infr			   
		      FROM indicador_plugin.juridico_solicitacao_parecer_gerencia 
			 WHERE cd_juridico_solicitacao_parecer_gerencia = ".intval($args['cd_juridico_solicitacao_parecer_gerencia']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function salvar(&$result, $args=array())
	{
		if(intval($args['cd_juridico_solicitacao_parecer_gerencia']) == 0)
		{
			$qr_sql = "
						INSERT INTO indicador_plugin.juridico_solicitacao_parecer_gerencia 
							 (
								dt_referencia, 
								nr_ac,
							    nr_ai,
							    nr_aj,
							    nr_gc,
							    nr_ge,
							    nr_gfc,
							    nr_ggs,
							    nr_gin,
							    nr_gp,
							    nr_sg,
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
								".(trim($args['nr_ac']) == "" ? "DEFAULT" : floatval($args['nr_ac'])).",
								".(trim($args['nr_ai']) == "" ? "DEFAULT" : floatval($args['nr_ai'])).",
								".(trim($args['nr_aj']) == "" ? "DEFAULT" : floatval($args['nr_aj'])).",
								".(trim($args['nr_gc']) == "" ? "DEFAULT" : floatval($args['nr_gc'])).",
								".(trim($args['nr_ge']) == "" ? "DEFAULT" : floatval($args['nr_ge'])).",
								".(trim($args['nr_gfc']) == "" ? "DEFAULT" : floatval($args['nr_gfc'])).",
								".(trim($args['nr_ggs']) == "" ? "DEFAULT" : floatval($args['nr_ggs'])).",
								".(trim($args['nr_gin']) == "" ? "DEFAULT" : floatval($args['nr_gin'])).",
								".(trim($args['nr_gp']) == "" ? "DEFAULT" : floatval($args['nr_gp'])).",
								".(trim($args['nr_sg']) == "" ? "DEFAULT" : floatval($args['nr_sg'])).",
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
				UPDATE indicador_plugin.juridico_solicitacao_parecer_gerencia
				   SET dt_referencia        = ".(trim($args['dt_referencia']) == "" ? "DEFAULT" : "TO_DATE('".trim($args["dt_referencia"])."', 'DD/MM/YYYY')").",
				       nr_ac                = ".(trim($args['nr_ac']) == "" ? "DEFAULT" : floatval($args['nr_ac'])).",
				       nr_ai               = ".(trim($args['nr_ai']) == "" ? "DEFAULT" : floatval($args['nr_ai'])).",
				       nr_aj               = ".(trim($args['nr_aj']) == "" ? "DEFAULT" : floatval($args['nr_aj'])).",
				       nr_gc                = ".(trim($args['nr_gc']) == "" ? "DEFAULT" : floatval($args['nr_gc'])).",
				       nr_ge                = ".(trim($args['nr_ge']) == "" ? "DEFAULT" : floatval($args['nr_ge'])).",
				       nr_gfc                = ".(trim($args['nr_gfc']) == "" ? "DEFAULT" : floatval($args['nr_gfc'])).",
				       nr_gin               = ".(trim($args['nr_gin']) == "" ? "DEFAULT" : floatval($args['nr_gin'])).",
				       nr_ggs                = ".(trim($args['nr_ggs']) == "" ? "DEFAULT" : floatval($args['nr_ggs'])).",
				       nr_gp               = ".(trim($args['nr_gp']) == "" ? "DEFAULT" : floatval($args['nr_gp'])).",
				       nr_sg				= ".(trim($args['nr_sg']) == "" ? "DEFAULT" : floatval($args['nr_sg'])).",       
					   nr_meta              = ".(trim($args['nr_meta']) == "" ? "DEFAULT" : floatval($args['nr_meta'])).",
					   cd_indicador_tabela  = ".(intval($args['cd_indicador_tabela']) == 0 ? "DEFAULT" : intval($args['cd_indicador_tabela'])).",
					   fl_media             = ".(trim($args['fl_media']) == "" ? "''" : "'".trim($args["fl_media"])."'").",
					   observacao           = ".(trim($args['observacao']) == "" ? "DEFAULT" : "'".trim($args["observacao"])."'").",
					   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
					   dt_alteracao         = CURRENT_TIMESTAMP
			     WHERE cd_juridico_solicitacao_parecer_gerencia = ".intval($args['cd_juridico_solicitacao_parecer_gerencia']).";";
		}

		$result = $this->db->query($qr_sql);
	}	

	function excluir(&$result, $args=array())
	{
		$qr_sql = " 
			UPDATE indicador_plugin.juridico_solicitacao_parecer_gerencia
		       SET dt_exclusao         = CURRENT_TIMESTAMP, 
			       cd_usuario_exclusao = ".intval($args['cd_usuario'])."
		     WHERE cd_juridico_solicitacao_parecer_gerencia = ".intval($args['cd_juridico_solicitacao_parecer_gerencia']).";"; 
			 
		$result = $this->db->query($qr_sql);
	}
	
	function atualiza_fechar_periodo(&$result, $args=array())
	{
		/*
		$qr_sql = "
					INSERT INTO indicador_plugin.juridico_solicitacao_parecer_gerencia 
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
		*/
		
		$qr_sql = "
					INSERT INTO indicador_plugin.juridico_solicitacao_parecer_gerencia 
						 (
							dt_referencia, 
							nr_ac,
							nr_ai,
							nr_aj,
							nr_gc,
							nr_ge,
							nr_gfc,
							nr_ggs,
							nr_gin,
							nr_gp,
							nr_sg,
							nr_meta, 
							cd_indicador_tabela, 
							fl_media, 
							cd_usuario_inclusao,
							cd_usuario_alteracao
						  ) 
					 VALUES 
						  ( 
							".(trim($args['dt_referencia']) == "" ? "DEFAULT" : "TO_DATE('".trim($args["dt_referencia"])."', 'DD/MM/YYYY')").",
							".(trim($args['nr_ac']) == "" ? "DEFAULT" : floatval($args['nr_ac'])).",
							".(trim($args['nr_ai']) == "" ? "DEFAULT" : floatval($args['nr_ai'])).",
							".(trim($args['nr_aj']) == "" ? "DEFAULT" : floatval($args['nr_aj'])).",
							".(trim($args['nr_gc']) == "" ? "DEFAULT" : floatval($args['nr_gc'])).",
							".(trim($args['nr_ge']) == "" ? "DEFAULT" : floatval($args['nr_ge'])).",
							".(trim($args['nr_gfc']) == "" ? "DEFAULT" : floatval($args['nr_gfc'])).",
							".(trim($args['nr_ggs']) == "" ? "DEFAULT" : floatval($args['nr_ggs'])).",
							".(trim($args['nr_gin']) == "" ? "DEFAULT" : floatval($args['nr_gin'])).",
							".(trim($args['nr_gp']) == "" ? "DEFAULT" : floatval($args['nr_gp'])).",
							".(trim($args['nr_sg']) == "" ? "DEFAULT" : floatval($args['nr_sg'])).",
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