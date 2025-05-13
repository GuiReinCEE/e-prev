<?php
class juridico_honor_acao_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT i.cd_juridico_honor_acao,
						   TO_CHAR(i.dt_referencia,'YYYY') AS ano_referencia,
						   TO_CHAR(i.dt_referencia,'MM/YYYY') AS mes_referencia,
						   i.dt_referencia,
						   i.cd_indicador_tabela,
						   i.fl_media,
						   i.observacao,
						   i.vl_honorario,
						   i.qt_acoes,
						   i.vl_honorario_medio,
						   i.nr_meta
					  FROM indicador_plugin.juridico_honor_acao i
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
			  FROM indicador_plugin.juridico_honor_acao
			 WHERE dt_exclusao IS NULL 
			 ORDER BY dt_referencia DESC 
			 LIMIT 1;";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function carrega(&$result, $args=array())
	{
		$qr_sql = "
            SELECT cd_juridico_honor_acao,
                   TO_CHAR(dt_referencia,'DD/MM/YYYY') AS dt_referencia,
                   cd_indicador_tabela,
                   fl_media,
                   vl_honorario,
                   qt_acoes,
                   nr_meta,
                   observacao
		      FROM indicador_plugin.juridico_honor_acao 
			 WHERE cd_juridico_honor_acao = ".intval($args['cd_juridico_honor_acao']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function salvar(&$result, $args=array())
	{
		if(intval($args['cd_juridico_honor_acao']) == 0)
		{
			$qr_sql = "
				INSERT INTO indicador_plugin.juridico_honor_acao 
				     (
						dt_referencia, 
					    vl_honorario, 
                        qt_acoes, 
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
					    ".(trim($args['vl_honorario']) == "" ? "DEFAULT" : floatval($args['vl_honorario'])).",
					    ".(trim($args['qt_acoes']) == "" ? "DEFAULT" : floatval($args['qt_acoes'])).",
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
				UPDATE indicador_plugin.juridico_honor_acao
				   SET dt_referencia        = ".(trim($args['dt_referencia']) == "" ? "DEFAULT" : "TO_DATE('".trim($args["dt_referencia"])."', 'DD/MM/YYYY')").",
				       vl_honorario         = ".(trim($args['vl_honorario']) == "" ? "DEFAULT" : floatval($args['vl_honorario'])).",
					   qt_acoes             = ".(trim($args['qt_acoes']) == "" ? "DEFAULT" : floatval($args['qt_acoes'])).",
	                   nr_meta              = ".(trim($args['nr_meta']) == "" ? "DEFAULT" : floatval($args['nr_meta'])).",
					   cd_indicador_tabela  = ".(intval($args['cd_indicador_tabela']) == 0 ? "DEFAULT" : intval($args['cd_indicador_tabela'])).",
					   fl_media             = ".(trim($args['fl_media']) == "" ? "''" : "'".trim($args["fl_media"])."'").",
					   observacao           = ".(trim($args['observacao']) == "" ? "DEFAULT" : "'".trim($args["observacao"])."'").",
					   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
					   dt_alteracao         = CURRENT_TIMESTAMP
			     WHERE cd_juridico_honor_acao = ".intval($args['cd_juridico_honor_acao']).";";
		}

		$result = $this->db->query($qr_sql);
	}	
	
	function excluir(&$result, $args=array())
	{
		$qr_sql = " 
			UPDATE indicador_plugin.juridico_honor_acao
		       SET dt_exclusao         = CURRENT_TIMESTAMP, 
			       cd_usuario_exclusao = ".intval($args['cd_usuario'])."
		     WHERE cd_juridico_honor_acao = ".intval($args['cd_juridico_honor_acao']).";"; 
			 
		$result = $this->db->query($qr_sql);
	}
	
	function atualiza_fechar_periodo(&$result, $args=array())
	{
		$qr_sql = " 
			INSERT INTO indicador_plugin.juridico_honor_acao 
				 (
					dt_referencia,
					vl_honorario,
					vl_honorario_medio,
					nr_meta,
					cd_indicador_tabela,
					fl_media, 
					cd_usuario_inclusao,
					cd_usuario_alteracao
				 ) 
			VALUES 
				 ( 
					".(trim($args['dt_referencia']) == "" ? "DEFAULT" : "TO_DATE('".trim($args["dt_referencia"])."', 'DD/MM/YYYY')").",
					".(trim($args['vl_honorario']) == "" ? "DEFAULT" : floatval($args['vl_honorario'])).",
					".(trim($args['vl_honorario_medio']) == "" ? "DEFAULT" : floatval($args['vl_honorario_medio'])).",
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