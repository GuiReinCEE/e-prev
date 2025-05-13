<?php
class Reclamatoria_indenizacao_demandante_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT i.cd_reclamatoria_indenizacao_demandante,
				   TO_CHAR(i.dt_referencia,'YYYY') AS ano_referencia,
				   TO_CHAR(i.dt_referencia,'MM/YYYY') AS mes_referencia,
				   i.dt_referencia,
				   i.cd_indicador_tabela,
				   i.fl_media,
				   i.observacao,
				   i.vl_indenizacao,
				   i.nr_liquidada,
				   i.nr_demandante,
				   i.vl_indenizacao_liquidada,
				   i.vl_indenizacao_demandante,
				   i.nr_meta,
				   i.nr_resultado
			  FROM indicador_plugin.reclamatoria_indenizacao_demandante i
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
			  FROM indicador_plugin.reclamatoria_indenizacao_demandante
			 WHERE dt_exclusao IS NULL 
			 ORDER BY dt_referencia DESC 
			 LIMIT 1;";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function carrega(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_reclamatoria_indenizacao_demandante,
				   TO_CHAR(dt_referencia,'DD/MM/YYYY') AS dt_referencia,
				   cd_indicador_tabela,
				   fl_media,
				   vl_indenizacao,
				   nr_liquidada,
				   nr_demandante,
				   nr_meta,
				   observacao,
				   nr_resultado
			  FROM indicador_plugin.reclamatoria_indenizacao_demandante 
			 WHERE cd_reclamatoria_indenizacao_demandante = ".intval($args['cd_reclamatoria_indenizacao_demandante']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function salvar(&$result, $args=array())
	{
		if(intval($args['cd_reclamatoria_indenizacao_demandante']) == 0)
		{
			$qr_sql = "
				INSERT INTO indicador_plugin.reclamatoria_indenizacao_demandante 
					 (
						dt_referencia, 
						vl_indenizacao, 
						nr_liquidada, 
						nr_demandante, 
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
						".(trim($args['vl_indenizacao']) == "" ? "DEFAULT" : floatval($args['vl_indenizacao'])).",
						".(trim($args['nr_liquidada']) == "" ? "DEFAULT" : floatval($args['nr_liquidada'])).",
						".(trim($args['nr_demandante']) == "" ? "DEFAULT" : floatval($args['nr_demandante'])).",
						".(trim($args['nr_meta']) == "" ? "DEFAULT" : floatval($args['nr_meta'])).",
						".(intval($args['cd_indicador_tabela']) == 0 ? "DEFAULT" : intval($args['cd_indicador_tabela'])).",
						".(trim($args['fl_media']) == "" ? "DEFAULT" : "'".trim($args["fl_media"])."'").",
						".(trim($args['observacao']) == "" ? "DEFAULT" : "'".trim($args["observacao"])."'").",
						".intval($args['cd_usuario']).",
						".intval($args['cd_usuario'])."
					  );";
		}
		else
		{
			$qr_sql = "
				UPDATE indicador_plugin.reclamatoria_indenizacao_demandante
				   SET dt_referencia        = ".(trim($args['dt_referencia']) == "" ? "DEFAULT" : "TO_DATE('".trim($args["dt_referencia"])."', 'DD/MM/YYYY')").",
					   vl_indenizacao       = ".(trim($args['vl_indenizacao']) == "" ? "DEFAULT" : floatval($args['vl_indenizacao'])).",
					   nr_liquidada         = ".(trim($args['nr_liquidada']) == "" ? "DEFAULT" : floatval($args['nr_liquidada'])).",
					   nr_demandante        = ".(trim($args['nr_demandante']) == "" ? "DEFAULT" : floatval($args['nr_demandante'])).",
					   nr_meta              = ".(trim($args['nr_meta']) == "" ? "DEFAULT" : floatval($args['nr_meta'])).",
					   cd_indicador_tabela  = ".(intval($args['cd_indicador_tabela']) == 0 ? "DEFAULT" : intval($args['cd_indicador_tabela'])).",
					   fl_media             = ".(trim($args['fl_media']) == "" ? "DEFAULT" : "'".trim($args["fl_media"])."'").",
					   observacao           = ".(trim($args['observacao']) == "" ? "DEFAULT" : "'".trim($args["observacao"])."'").",
					   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
					   dt_alteracao         = CURRENT_TIMESTAMP
				 WHERE cd_reclamatoria_indenizacao_demandante = ".intval($args['cd_reclamatoria_indenizacao_demandante']).";";
		}

		$result = $this->db->query($qr_sql);
	}	
	
	function excluir(&$result, $args=array())
	{
		$qr_sql = " 
			UPDATE indicador_plugin.reclamatoria_indenizacao_demandante
			   SET dt_exclusao         = CURRENT_TIMESTAMP, 
				   cd_usuario_exclusao = ".intval($args['cd_usuario'])."
			 WHERE cd_reclamatoria_indenizacao_demandante = ".intval($args['cd_reclamatoria_indenizacao_demandante']).";"; 
			 
		$result = $this->db->query($qr_sql);
	}
	
	function atualiza_fechar_periodo(&$result, $args=array())
	{
		$qr_sql = " 
			INSERT INTO indicador_plugin.reclamatoria_indenizacao_demandante 
				 (
					dt_referencia,
					vl_indenizacao,
					nr_liquidada,
					nr_demandante,
					nr_meta,
					cd_indicador_tabela,
					fl_media, 
					cd_usuario_inclusao,
					cd_usuario_alteracao
				 ) 
			VALUES 
				 ( 
					".(trim($args['dt_referencia']) == "" ? "DEFAULT" : "TO_DATE('".trim($args["dt_referencia"])."', 'DD/MM/YYYY')").",
					".(trim($args['vl_indenizacao']) == "" ? "DEFAULT" : floatval($args['vl_indenizacao'])).",
					".(trim($args['nr_liquidada']) == "" ? "DEFAULT" : floatval($args['nr_liquidada'])).",
					".(trim($args['nr_demandante']) == "" ? "DEFAULT" : floatval($args['nr_demandante'])).",
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