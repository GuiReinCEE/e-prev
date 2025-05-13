<?php
class Juridico_sucesso_acoes_ribeiro_civel_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT i.cd_juridico_sucesso_acoes_ribeiro_civel,
						   TO_CHAR(i.dt_referencia,'YYYY') AS ano_referencia,
						   TO_CHAR(i.dt_referencia,'MM/YYYY') AS mes_referencia,
						   i.dt_referencia,
						   i.cd_indicador_tabela,
						   i.cd_etapa,
						   i.fl_media,
						   i.observacao,
						   i.nr_inicial,
						   i.nr_improcede,
						   i.pr_improcede,
						   i.nr_parcial,
						   i.pr_parcial,
						   i.nr_procede,
						   i.pr_procede,
						   i.nr_total,
						   i.nr_total_geral,
						   i.nr_meta
					  FROM indicador_plugin.juridico_sucesso_acoes_ribeiro_civel i
					 WHERE i.dt_exclusao IS NULL
					   AND(i.fl_media = 'S' OR i.cd_indicador_tabela = ".intval($args['cd_indicador_tabela']).")
					 ORDER BY i.dt_referencia ASC;
			     ";

		$result = $this->db->query($qr_sql);		
	}	

	function etapa(&$result, $args=array())
	{
		$qr_sql = "
					SELECT x.value, 
					       x.text
					  FROM (
							SELECT 0::INTEGER AS value, 'Fase Inicial'::TEXT AS text
							 UNION
							SELECT 1::INTEGER AS value, '1º Instância'::TEXT AS text
							 UNION
							SELECT 2::INTEGER AS value, '2º Instância'::TEXT AS text
							 UNION
							SELECT 3::INTEGER AS value, '3º Instância'::TEXT AS text
						   ) x
					 WHERE x.value NOT IN (SELECT i.cd_etapa
					                         FROM indicador_plugin.juridico_sucesso_acoes_ribeiro_civel i
								            WHERE i.dt_exclusao IS NULL
											  AND i.cd_juridico_sucesso_acoes_ribeiro_civel <> ".intval($args['cd_juridico_sucesso_acoes_ribeiro_civel'])."
											  AND i.cd_indicador_tabela = ".intval($args['cd_indicador_tabela']).")
					ORDER BY x.value
			      ";
			 
		$result = $this->db->query($qr_sql);
	}	
	
	function carrega(&$result, $args=array())
	{
		$qr_sql = "
					SELECT cd_juridico_sucesso_acoes_ribeiro_civel,
						   TO_CHAR(dt_referencia,'YYYY') AS ano_referencia,
		                   TO_CHAR(dt_referencia,'MM/YYYY') AS mes_referencia,
						   TO_CHAR(dt_referencia,'DD/MM/YYYY') AS dt_referencia,
						   cd_indicador_tabela,
						   cd_etapa,
						   fl_media,
						   observacao,
						   nr_inicial,
						   nr_improcede,
						   pr_improcede,
						   nr_parcial,
						   pr_parcial,
						   nr_procede,
						   pr_procede,
						   nr_total,
						   nr_total_geral,
						   nr_meta
					  FROM indicador_plugin.juridico_sucesso_acoes_ribeiro_civel 
					 WHERE cd_juridico_sucesso_acoes_ribeiro_civel = ".intval($args['cd_juridico_sucesso_acoes_ribeiro_civel']).";
			      ";
			 
		$result = $this->db->query($qr_sql);		
	}	
	
	function salvar(&$result, $args=array())
	{
		if(intval($args['cd_juridico_sucesso_acoes_ribeiro_civel']) == 0)
		{
			$qr_sql = "
						INSERT INTO indicador_plugin.juridico_sucesso_acoes_ribeiro_civel 
							 (
								dt_referencia, 
						        cd_etapa,
						        nr_inicial,
						        nr_improcede,
						        nr_parcial,
						        nr_procede,
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
								".(trim($args['cd_etapa'])   == "" ? "DEFAULT" : intval($args['cd_etapa'])).",
								".(trim($args['nr_inicial']) == "" ? "DEFAULT" : floatval($args['nr_inicial'])).",
								".(trim($args['nr_improcede']) == "" ? "DEFAULT" : floatval($args['nr_improcede'])).",
								".(trim($args['nr_parcial']) == "" ? "DEFAULT" : floatval($args['nr_parcial'])).",
								".(trim($args['nr_procede']) == "" ? "DEFAULT" : floatval($args['nr_procede'])).",
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
						UPDATE indicador_plugin.juridico_sucesso_acoes_ribeiro_civel
						   SET dt_referencia        = ".(trim($args['dt_referencia']) == "" ? "DEFAULT" : "TO_DATE('".trim($args["dt_referencia"])."', 'DD/MM/YYYY')").",
							   cd_etapa             = ".(trim($args['cd_etapa'])   == "" ? "DEFAULT" : intval($args['cd_etapa'])).",
							   nr_inicial           = ".(trim($args['nr_inicial']) == "" ? "DEFAULT" : floatval($args['nr_inicial'])).",
							   nr_improcede         = ".(trim($args['nr_improcede']) == "" ? "DEFAULT" : floatval($args['nr_improcede'])).",
							   nr_parcial           = ".(trim($args['nr_parcial']) == "" ? "DEFAULT" : floatval($args['nr_parcial'])).",
							   nr_procede           = ".(trim($args['nr_procede']) == "" ? "DEFAULT" : floatval($args['nr_procede'])).",
							   nr_meta              = ".(trim($args['nr_meta']) == "" ? "DEFAULT" : floatval($args['nr_meta'])).",
							   cd_indicador_tabela  = ".(intval($args['cd_indicador_tabela']) == 0 ? "DEFAULT" : intval($args['cd_indicador_tabela'])).",
							   observacao           = ".(trim($args['observacao']) == "" ? "DEFAULT" : "'".trim($args["observacao"])."'").",
							   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
							   dt_alteracao         = CURRENT_TIMESTAMP
						 WHERE cd_juridico_sucesso_acoes_ribeiro_civel = ".intval($args['cd_juridico_sucesso_acoes_ribeiro_civel']).";
				      ";
		}
		$result = $this->db->query($qr_sql);		
	}	

	function excluir(&$result, $args=array())
	{
		$qr_sql = " 
			UPDATE indicador_plugin.juridico_sucesso_acoes_ribeiro_civel
		       SET dt_exclusao         = CURRENT_TIMESTAMP, 
			       cd_usuario_exclusao = ".intval($args['cd_usuario'])."
		     WHERE cd_juridico_sucesso_acoes_ribeiro_civel = ".intval($args['cd_juridico_sucesso_acoes_ribeiro_civel']).";"; 
			 
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