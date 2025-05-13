<?php
class juridico_ass_acoes_jud_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT i.cd_juridico_ass_acoes_jud,
						   TO_CHAR(i.dt_referencia,'YYYY') AS ano_referencia,
						   TO_CHAR(i.dt_referencia,'MM/YYYY') AS mes_referencia,
						   i.dt_referencia,
						   i.cd_indicador_tabela,
						   i.fl_media,
						   i.observacao,
						   i.qt_assistidos,
						   i.qt_acoes,
						   i.qt_novos,
						   i.qt_reincidentes,
						   i.qt_sem,
						   i.nr_percentual_reincidentes,
						   i.nr_percentual_assistidos_com,
						   i.nr_meta
					  FROM indicador_plugin.juridico_ass_acoes_jud i
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
					  FROM indicador_plugin.juridico_ass_acoes_jud
					 WHERE dt_exclusao IS NULL 
					 ORDER BY dt_referencia DESC 
					 LIMIT 1;
			      ";
			 
		$result = $this->db->query($qr_sql);
	}	
	
	function carrega(&$result, $args=array())
	{
		$qr_sql = "
					SELECT cd_juridico_ass_acoes_jud,
						   TO_CHAR(dt_referencia,'DD/MM/YYYY') AS dt_referencia,
						   cd_indicador_tabela,
						   fl_media,
						   qt_assistidos,
						   qt_acoes,
						   qt_novos,
						   qt_reincidentes,
						   qt_sem,
						   nr_percentual_reincidentes,
						   nr_percentual_assistidos_com,
						   nr_meta,
						   observacao
					  FROM indicador_plugin.juridico_ass_acoes_jud 
					 WHERE cd_juridico_ass_acoes_jud = ".intval($args['cd_juridico_ass_acoes_jud']).";
			      ";
			 
		$result = $this->db->query($qr_sql);		
	}

	function salvar(&$result, $args=array())
	{
		if(intval($args['cd_juridico_ass_acoes_jud']) == 0)
		{
			$qr_sql = "
						INSERT INTO indicador_plugin.juridico_ass_acoes_jud 
							 (
								dt_referencia, 
								qt_assistidos,
								qt_acoes,
								qt_reincidentes,
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
								".(trim($args['qt_assistidos']) == "" ? "DEFAULT" : floatval($args['qt_assistidos'])).",
								".(trim($args['qt_acoes']) == "" ? "DEFAULT" : floatval($args['qt_acoes'])).",
								".(trim($args['qt_reincidentes']) == "" ? "DEFAULT" : floatval($args['qt_reincidentes'])).",
								".(trim($args['nr_meta']) == "" ? "DEFAULT" : floatval($args['nr_meta'])).",
								".(intval($args['cd_indicador_tabela']) == 0 ? "DEFAULT" : intval($args['cd_indicador_tabela'])).",
								'',
								".(trim($args['observacao']) == "" ? "DEFAULT" : "'".trim($args["observacao"])."'").",
								".intval($args['cd_usuario']).",
								".intval($args['cd_usuario'])."
							  );
					  ";
		}
		else
		{
			$qr_sql = "
						UPDATE indicador_plugin.juridico_ass_acoes_jud
						   SET dt_referencia        = ".(trim($args['dt_referencia']) == "" ? "DEFAULT" : "TO_DATE('".trim($args["dt_referencia"])."', 'DD/MM/YYYY')").",
							   qt_assistidos        = ".(trim($args['qt_assistidos']) == "" ? "DEFAULT" : floatval($args['qt_assistidos'])).",
							   qt_acoes             = ".(trim($args['qt_acoes']) == "" ? "DEFAULT" : floatval($args['qt_acoes'])).",
							   qt_reincidentes      = ".(trim($args['qt_reincidentes']) == "" ? "DEFAULT" : floatval($args['qt_reincidentes'])).",
							   nr_meta              = ".(trim($args['nr_meta']) == "" ? "DEFAULT" : floatval($args['nr_meta'])).",
							   cd_indicador_tabela  = ".(intval($args['cd_indicador_tabela']) == 0 ? "DEFAULT" : intval($args['cd_indicador_tabela'])).",
							   observacao           = ".(trim($args['observacao']) == "" ? "DEFAULT" : "'".trim($args["observacao"])."'").",
							   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
							   dt_alteracao         = CURRENT_TIMESTAMP
						 WHERE cd_juridico_ass_acoes_jud = ".intval($args['cd_juridico_ass_acoes_jud']).";
				      ";
		}
		$result = $this->db->query($qr_sql);		
	}
	
	function excluir(&$result, $args=array())
	{
		$qr_sql = " 
			UPDATE indicador_plugin.juridico_ass_acoes_jud
		       SET dt_exclusao         = CURRENT_TIMESTAMP, 
			       cd_usuario_exclusao = ".intval($args['cd_usuario'])."
		     WHERE cd_juridico_ass_acoes_jud = ".intval($args['cd_juridico_ass_acoes_jud']).";"; 
			 
		$result = $this->db->query($qr_sql);
	}	

	function atualiza_fechar_periodo(&$result, $args=array())
	{
		$qr_sql = "
					INSERT INTO indicador_plugin.juridico_ass_acoes_jud 
						 (
							dt_referencia, 
							qt_assistidos,
							qt_acoes,
							qt_reincidentes,
							nr_meta, 
							cd_indicador_tabela, 
							fl_media, 
							cd_usuario_inclusao,
							cd_usuario_alteracao
						  ) 
					 VALUES 
						  ( 
							".(trim($args['dt_referencia']) == "" ? "DEFAULT" : "TO_DATE('".trim($args["dt_referencia"])."', 'DD/MM/YYYY')").",
							".(trim($args['qt_assistidos']) == "" ? "DEFAULT" : floatval($args['qt_assistidos'])).",
							".(trim($args['qt_acoes']) == "" ? "DEFAULT" : floatval($args['qt_acoes'])).",
							".(trim($args['qt_reincidentes']) == "" ? "DEFAULT" : floatval($args['qt_reincidentes'])).",
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
					 WHERE cd_indicador_tabela = ".intval($args['cd_indicador_tabela']).";
			      ";
		$result = $this->db->query($qr_sql);
	}	
	
}
?>