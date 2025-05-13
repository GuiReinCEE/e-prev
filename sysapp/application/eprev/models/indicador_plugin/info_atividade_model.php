<?php
class Info_atividade_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	function listar( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT i.cd_info_atividade,
				   TO_CHAR(i.dt_referencia,'YYYY') AS ano_referencia,
				   TO_CHAR(i.dt_referencia,'MM/YYYY') AS mes_referencia,
				   i.dt_referencia,
				   i.cd_indicador_tabela,
				   i.fl_media,
				   i.observacao,
				   i.nr_abertas_mes,
				   i.nr_atendidas_mes,
				   i.nr_percentual_mes_f,
				   i.nr_meta,
				   i.nr_abertas_acu_f,
				   i.nr_atendidas_acu_f,
				   i.nr_percentual_acu_f,
				   i.nr_meta,
				   i.fl_meta,
				   i.fl_direcao,
				   (SELECT i1.tp_analise
                      FROM indicador.indicador_tabela it
                      JOIN indicador.indicador i1
                        ON i1.cd_indicador = it.cd_indicador
                     WHERE it.cd_indicador_tabela = i.cd_indicador_tabela) AS tp_analise
		      FROM indicador_plugin.info_atividade i
		     WHERE i.dt_exclusao IS NULL
		       AND(i.fl_media = 'S' OR i.cd_indicador_tabela = ".intval($args['cd_indicador_tabela']).")
		     ORDER BY i.dt_referencia ASC;";

		$result = $this->db->query($qr_sql);
	}

	function carrega_referencia(&$result, $args=array())
	{
		$qr_sql = "
			SELECT TO_CHAR(dt_referencia + '1 month'::interval, 'DD/MM/YYYY') AS dt_referencia_n, 
				   nr_meta, 
				   cd_indicador_tabela 
			  FROM indicador_plugin.info_atividade
			 WHERE dt_exclusao IS NULL 
			 ORDER BY dt_referencia DESC 
			 LIMIT 1;";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function carrega(&$result, $args=array())
	{
		$qr_sql = "
            SELECT cd_info_atividade,
                   TO_CHAR(dt_referencia,'DD/MM/YYYY') AS dt_referencia,
                   cd_indicador_tabela,
                   fl_media,
                   nr_abertas_mes,
                   nr_atendidas_mes,
                   nr_meta,
                   observacao
		      FROM indicador_plugin.info_atividade 
			 WHERE cd_info_atividade = ".intval($args['cd_info_atividade']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function salvar(&$result, $args=array())
	{
		if(intval($args['cd_info_atividade']) == 0)
		{
			$qr_sql = "
				INSERT INTO indicador_plugin.info_atividade 
				     (
						dt_referencia, 
					    nr_abertas_mes, 
                        nr_atendidas_mes, 
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
					    ".(trim($args['nr_abertas_mes']) == "" ? "DEFAULT" : floatval($args['nr_abertas_mes'])).",
					    ".(trim($args['nr_atendidas_mes']) == "" ? "DEFAULT" : floatval($args['nr_atendidas_mes'])).",
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
				UPDATE indicador_plugin.info_atividade
				   SET dt_referencia        = ".(trim($args['dt_referencia']) == "" ? "DEFAULT" : "TO_DATE('".trim($args["dt_referencia"])."', 'DD/MM/YYYY')").",
				       nr_abertas_mes       = ".(trim($args['nr_abertas_mes']) == "" ? "DEFAULT" : floatval($args['nr_abertas_mes'])).",
					   nr_atendidas_mes     = ".(trim($args['nr_atendidas_mes']) == "" ? "DEFAULT" : floatval($args['nr_atendidas_mes'])).",
	                   nr_meta              = ".(trim($args['nr_meta']) == "" ? "DEFAULT" : floatval($args['nr_meta'])).",
					   cd_indicador_tabela  = ".(intval($args['cd_indicador_tabela']) == 0 ? "DEFAULT" : intval($args['cd_indicador_tabela'])).",
					   fl_media             = ".(trim($args['fl_media']) == "" ? "''" : "'".trim($args["fl_media"])."'").",
					   observacao           = ".(trim($args['observacao']) == "" ? "DEFAULT" : "'".trim($args["observacao"])."'").",
					   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
					   dt_alteracao         = CURRENT_TIMESTAMP
			     WHERE cd_info_atividade = ".intval($args['cd_info_atividade']).";";
		}

		$result = $this->db->query($qr_sql);
	}

	function excluir(&$result, $args=array())
	{
		$qr_sql = " 
			UPDATE indicador_plugin.info_atividade
		       SET dt_exclusao         = CURRENT_TIMESTAMP, 
			       cd_usuario_exclusao = ".intval($args['cd_usuario'])."
		     WHERE cd_info_atividade = ".intval($args['cd_info_atividade']).";"; 
			 
		$result = $this->db->query($qr_sql);
	}
	
	function atualiza_fechar_periodo(&$result, $args=array())
	{
		$qr_sql = " 
			INSERT INTO indicador_plugin.info_atividade 
				 (
					dt_referencia,
					nr_abertas_acu_f,
					nr_atendidas_acu_f,
					nr_meta,
					cd_indicador_tabela,
					fl_media, 
					cd_usuario_inclusao,
					cd_usuario_alteracao
				 ) 
			VALUES 
				 ( 
					".(trim($args['dt_referencia']) == "" ? "DEFAULT" : "TO_DATE('".trim($args["dt_referencia"])."', 'DD/MM/YYYY')").",
					".(trim($args['nr_abertas_acu_f']) == "" ? "DEFAULT" : floatval($args['nr_abertas_acu_f'])).",
					".(trim($args['nr_atendidas_acu_f']) == "" ? "DEFAULT" : floatval($args['nr_atendidas_acu_f'])).",
					".(trim($args['nr_meta']) == "" ? "DEFAULT" : floatval($args['nr_meta'])).",
					".(intval($args['cd_indicador_tabela']) == 0 ? "DEFAULT" : intval($args['cd_indicador_tabela'])).",
					'S',
					".intval($args['cd_usuario']).",
					".intval($args['cd_usuario'])."
				 );"; 

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