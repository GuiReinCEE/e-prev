<?php
class Juridico_sucesso_acoes_autora_trabalhista_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT i.cd_juridico_sucesso_acoes_autora_trabalhista,
						   TO_CHAR(i.dt_referencia,'YYYY') AS ano_referencia,
						   TO_CHAR(i.dt_referencia,'MM/YYYY') AS mes_referencia,
						   i.dt_referencia,
						   i.cd_indicador_tabela,
						   i.cd_etapa,
						   i.fl_media,
						   i.observacao,
						   i.nr_valor_1,
						   i.nr_valor_2,
						   i.nr_valor_3,
						   i.nr_valor_4,
						   i.nr_percentual_f,
						   i.nr_meta
					  FROM indicador_plugin.juridico_sucesso_acoes_autora_trabalhista i
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
					                         FROM indicador_plugin.juridico_sucesso_acoes_autora_trabalhista i
								            WHERE i.dt_exclusao IS NULL
											  AND i.cd_juridico_sucesso_acoes_autora_trabalhista <> ".intval($args['cd_juridico_sucesso_acoes_autora_trabalhista'])."
											  AND i.cd_indicador_tabela = ".intval($args['cd_indicador_tabela']).")
					ORDER BY x.value
			      ";
			 
		$result = $this->db->query($qr_sql);
	}	
	
	function carrega(&$result, $args=array())
	{
		$qr_sql = "
					SELECT cd_juridico_sucesso_acoes_autora_trabalhista,
						   TO_CHAR(dt_referencia,'YYYY') AS ano_referencia,
		                   TO_CHAR(dt_referencia,'MM/YYYY') AS mes_referencia,
						   TO_CHAR(dt_referencia,'DD/MM/YYYY') AS dt_referencia,
						   cd_indicador_tabela,
						   cd_etapa,
						   fl_media,
						   observacao,
						   nr_valor_1,
						   nr_valor_2,
						   nr_valor_3,
						   nr_valor_4,
						   nr_percentual_f,
						   nr_meta
					  FROM indicador_plugin.juridico_sucesso_acoes_autora_trabalhista 
					 WHERE cd_juridico_sucesso_acoes_autora_trabalhista = ".intval($args['cd_juridico_sucesso_acoes_autora_trabalhista']).";
			      ";
			 
		$result = $this->db->query($qr_sql);		
	}	
	
	function salvar(&$result, $args=array())
	{
		if(intval($args['cd_juridico_sucesso_acoes_autora_trabalhista']) == 0)
		{
			$qr_sql = "
						INSERT INTO indicador_plugin.juridico_sucesso_acoes_autora_trabalhista 
							 (
								dt_referencia, 
						        cd_etapa,
						        nr_valor_1,
						        nr_valor_2,
						        nr_valor_3,
						        nr_valor_4,
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
								".(trim($args['nr_valor_1']) == "" ? "DEFAULT" : floatval($args['nr_valor_1'])).",
								".(trim($args['nr_valor_2']) == "" ? "DEFAULT" : floatval($args['nr_valor_2'])).",
								".(trim($args['nr_valor_3']) == "" ? "DEFAULT" : floatval($args['nr_valor_3'])).",
								".(trim($args['nr_valor_4']) == "" ? "DEFAULT" : floatval($args['nr_valor_4'])).",
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
						UPDATE indicador_plugin.juridico_sucesso_acoes_autora_trabalhista
						   SET dt_referencia        = ".(trim($args['dt_referencia']) == "" ? "DEFAULT" : "TO_DATE('".trim($args["dt_referencia"])."', 'DD/MM/YYYY')").",
							   cd_etapa             = ".(trim($args['cd_etapa'])   == "" ? "DEFAULT" : intval($args['cd_etapa'])).",
							   nr_valor_1           = ".(trim($args['nr_valor_1']) == "" ? "DEFAULT" : floatval($args['nr_valor_1'])).",
							   nr_valor_2           = ".(trim($args['nr_valor_2']) == "" ? "DEFAULT" : floatval($args['nr_valor_2'])).",
							   nr_valor_3           = ".(trim($args['nr_valor_3']) == "" ? "DEFAULT" : floatval($args['nr_valor_3'])).",
							   nr_valor_4           = ".(trim($args['nr_valor_4']) == "" ? "DEFAULT" : floatval($args['nr_valor_4'])).",
							   nr_meta              = ".(trim($args['nr_meta']) == "" ? "DEFAULT" : floatval($args['nr_meta'])).",
							   cd_indicador_tabela  = ".(intval($args['cd_indicador_tabela']) == 0 ? "DEFAULT" : intval($args['cd_indicador_tabela'])).",
							   observacao           = ".(trim($args['observacao']) == "" ? "DEFAULT" : "'".trim($args["observacao"])."'").",
							   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
							   dt_alteracao         = CURRENT_TIMESTAMP
						 WHERE cd_juridico_sucesso_acoes_autora_trabalhista = ".intval($args['cd_juridico_sucesso_acoes_autora_trabalhista']).";
				      ";
		}
		$result = $this->db->query($qr_sql);		
	}	

	function excluir(&$result, $args=array())
	{
		$qr_sql = " 
			UPDATE indicador_plugin.juridico_sucesso_acoes_autora_trabalhista
		       SET dt_exclusao         = CURRENT_TIMESTAMP, 
			       cd_usuario_exclusao = ".intval($args['cd_usuario'])."
		     WHERE cd_juridico_sucesso_acoes_autora_trabalhista = ".intval($args['cd_juridico_sucesso_acoes_autora_trabalhista']).";"; 
			 
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