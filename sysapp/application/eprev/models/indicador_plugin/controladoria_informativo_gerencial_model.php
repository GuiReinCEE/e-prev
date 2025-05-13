<?php
class Controladoria_informativo_gerencial_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	function listar( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT i.cd_controladoria_informativo_gerencial,
				   TO_CHAR(i.dt_referencia,'YYYY') AS ano_referencia,
				   TO_CHAR(i.dt_referencia,'MM/YYYY') AS mes_referencia,
				   i.dt_referencia,
				   i.cd_indicador_tabela,
				   i.fl_media,
				   i.observacao,
				   i.nr_repondente,
				   i.nr_clareza,
				   i.nr_clareza_meta,
				   i.nr_tempestividade,
				   i.nr_tempestividade_meta,
				   i.nr_relevancia,
				   i.nr_relevancia_meta,
				   i.nr_exatidao,
				   i.nr_exatidao_meta,
				   i.nr_satisfacao,
				   i.nr_meta
		      FROM indicador_plugin.controladoria_informativo_gerencial i
		     WHERE i.dt_exclusao IS NULL
		       AND(i.fl_media = 'S' OR i.cd_indicador_tabela = ".intval($args['cd_indicador_tabela']).")
		     ORDER BY i.dt_referencia ASC;";

		$result = $this->db->query($qr_sql);
	}

	function carrega_referencia(&$result, $args=array())
	{
		$qr_sql = "
			SELECT nr_meta, 
				   cd_indicador_tabela 
			  FROM indicador_plugin.controladoria_informativo_gerencial
			 WHERE dt_exclusao IS NULL 
			 ORDER BY dt_referencia DESC 
			 LIMIT 1;";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function carrega(&$result, $args=array())
	{
		$qr_sql = "
            SELECT cd_controladoria_informativo_gerencial,
                   TO_CHAR(dt_referencia,'DD/MM/YYYY') AS dt_referencia,
                   TO_CHAR(dt_referencia,'YYYY') AS ano_referencia,
				   TO_CHAR(dt_referencia,'MM') AS mes_referencia,
                   cd_indicador_tabela,
                   fl_media,
                   nr_repondente,
                   nr_clareza_meta,
				   nr_clareza_1,
				   nr_clareza_2,
				   nr_clareza_3,
				   nr_clareza_4,
				   nr_clareza_5,
				   nr_tempestividade_meta,
				   nr_tempestividade_1,
				   nr_tempestividade_2,
				   nr_tempestividade_3,
				   nr_tempestividade_4,
				   nr_tempestividade_5,
				   nr_relevancia_meta,
				   nr_relevancia_1,
				   nr_relevancia_2,
				   nr_relevancia_3,
				   nr_relevancia_4,
				   nr_relevancia_5,
				   nr_exatidao_meta,
				   nr_exatidao_1,
				   nr_exatidao_2,
				   nr_exatidao_3,
				   nr_exatidao_4,
				   nr_exatidao_5,
                   observacao
		      FROM indicador_plugin.controladoria_informativo_gerencial 
			 WHERE cd_controladoria_informativo_gerencial = ".intval($args['cd_controladoria_informativo_gerencial']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function salvar(&$result, $args=array())
	{
		if(intval($args['cd_controladoria_informativo_gerencial']) == 0)
		{
			$qr_sql = "
				INSERT INTO indicador_plugin.controladoria_informativo_gerencial 
				     (
						dt_referencia, 
					    nr_repondente, 
					    nr_clareza_meta,
                        nr_clareza_1,
				   		nr_clareza_2,
						nr_clareza_3,
						nr_clareza_4,
						nr_clareza_5,
						nr_tempestividade_meta,
						nr_tempestividade_1,
						nr_tempestividade_2,
						nr_tempestividade_3,
						nr_tempestividade_4,
						nr_tempestividade_5,
						nr_relevancia_meta,
						nr_relevancia_1,
						nr_relevancia_2,
						nr_relevancia_3,
						nr_relevancia_4,
						nr_relevancia_5,
						nr_exatidao_meta,
						nr_exatidao_1,
						nr_exatidao_2,
						nr_exatidao_3,
						nr_exatidao_4,
						nr_exatidao_5,
					    cd_indicador_tabela, 
					    fl_media, 
                        observacao,
					    cd_usuario_inclusao,
					    cd_usuario_alteracao
			          ) 
			     VALUES 
				      ( 
						".(trim($args['dt_referencia']) == "" ? "DEFAULT" : "TO_DATE('".trim($args["dt_referencia"])."', 'DD/MM/YYYY')").",
					    ".(trim($args['nr_repondente']) == "" ? "DEFAULT" : floatval($args['nr_repondente'])).",

					    ".(trim($args['nr_clareza_meta']) == "" ? "DEFAULT" : floatval($args['nr_clareza_meta'])).",
					    ".(trim($args['nr_clareza_1']) == "" ? "DEFAULT" : floatval($args['nr_clareza_1'])).",
					    ".(trim($args['nr_clareza_2']) == "" ? "DEFAULT" : floatval($args['nr_clareza_2'])).",
					    ".(trim($args['nr_clareza_3']) == "" ? "DEFAULT" : floatval($args['nr_clareza_3'])).",
					    ".(trim($args['nr_clareza_4']) == "" ? "DEFAULT" : floatval($args['nr_clareza_4'])).",
					    ".(trim($args['nr_clareza_5']) == "" ? "DEFAULT" : floatval($args['nr_clareza_5'])).",

					    ".(trim($args['nr_tempestividade_meta']) == "" ? "DEFAULT" : floatval($args['nr_tempestividade_meta'])).",
					    ".(trim($args['nr_tempestividade_1']) == "" ? "DEFAULT" : floatval($args['nr_tempestividade_1'])).",
					    ".(trim($args['nr_tempestividade_2']) == "" ? "DEFAULT" : floatval($args['nr_tempestividade_2'])).",
					    ".(trim($args['nr_tempestividade_3']) == "" ? "DEFAULT" : floatval($args['nr_tempestividade_3'])).",
					    ".(trim($args['nr_tempestividade_4']) == "" ? "DEFAULT" : floatval($args['nr_tempestividade_4'])).",
					    ".(trim($args['nr_tempestividade_5']) == "" ? "DEFAULT" : floatval($args['nr_tempestividade_5'])).",

					    ".(trim($args['nr_relevancia_meta']) == "" ? "DEFAULT" : floatval($args['nr_relevancia_meta'])).",
					    ".(trim($args['nr_relevancia_1']) == "" ? "DEFAULT" : floatval($args['nr_relevancia_1'])).",
					    ".(trim($args['nr_relevancia_2']) == "" ? "DEFAULT" : floatval($args['nr_relevancia_2'])).",
					    ".(trim($args['nr_relevancia_3']) == "" ? "DEFAULT" : floatval($args['nr_relevancia_3'])).",
					    ".(trim($args['nr_relevancia_4']) == "" ? "DEFAULT" : floatval($args['nr_relevancia_4'])).",
					    ".(trim($args['nr_relevancia_5']) == "" ? "DEFAULT" : floatval($args['nr_relevancia_5'])).",

					    ".(trim($args['nr_exatidao_meta']) == "" ? "DEFAULT" : floatval($args['nr_exatidao_meta'])).",
					    ".(trim($args['nr_exatidao_1']) == "" ? "DEFAULT" : floatval($args['nr_exatidao_1'])).",
					    ".(trim($args['nr_exatidao_2']) == "" ? "DEFAULT" : floatval($args['nr_exatidao_2'])).",
					    ".(trim($args['nr_exatidao_3']) == "" ? "DEFAULT" : floatval($args['nr_exatidao_3'])).",
					    ".(trim($args['nr_exatidao_4']) == "" ? "DEFAULT" : floatval($args['nr_exatidao_4'])).",
					    ".(trim($args['nr_exatidao_5']) == "" ? "DEFAULT" : floatval($args['nr_exatidao_5'])).",
					    
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
				UPDATE indicador_plugin.controladoria_informativo_gerencial
				   SET dt_referencia          = ".(trim($args['dt_referencia']) == "" ? "DEFAULT" : "TO_DATE('".trim($args["dt_referencia"])."', 'DD/MM/YYYY')").",
				       nr_repondente          = ".(trim($args['nr_repondente']) == "" ? "DEFAULT" : floatval($args['nr_repondente'])).",

				       nr_clareza_meta        = ".(trim($args['nr_clareza_meta']) == "" ? "DEFAULT" : trim($args['nr_clareza_meta'])).",
                       nr_clareza_1           = ".(trim($args['nr_clareza_1']) == "" ? "DEFAULT" : floatval($args['nr_clareza_1'])).",
                       nr_clareza_2           = ".(trim($args['nr_clareza_2']) == "" ? "DEFAULT" : floatval($args['nr_clareza_2'])).",
                       nr_clareza_3           = ".(trim($args['nr_clareza_3']) == "" ? "DEFAULT" : floatval($args['nr_clareza_3'])).",
                       nr_clareza_4           = ".(trim($args['nr_clareza_4']) == "" ? "DEFAULT" : floatval($args['nr_clareza_4'])).",
                       nr_clareza_5           = ".(trim($args['nr_clareza_5']) == "" ? "DEFAULT" : floatval($args['nr_clareza_5'])).",

                       nr_exatidao_meta       = ".(trim($args['nr_exatidao_meta']) == "" ? "DEFAULT" : trim($args['nr_exatidao_meta'])).",
					   nr_exatidao_1          = ".(trim($args['nr_exatidao_1']) == "" ? "DEFAULT" : floatval($args['nr_exatidao_1'])).",
					   nr_exatidao_2          = ".(trim($args['nr_exatidao_2']) == "" ? "DEFAULT" : floatval($args['nr_exatidao_2'])).",
					   nr_exatidao_3          = ".(trim($args['nr_exatidao_3']) == "" ? "DEFAULT" : floatval($args['nr_exatidao_3'])).",
					   nr_exatidao_4          = ".(trim($args['nr_exatidao_4']) == "" ? "DEFAULT" : floatval($args['nr_exatidao_4'])).",
					   nr_exatidao_5          = ".(trim($args['nr_exatidao_5']) == "" ? "DEFAULT" : floatval($args['nr_exatidao_5'])).",

					   nr_tempestividade_meta = ".(trim($args['nr_tempestividade_meta']) == "" ? "DEFAULT" : trim($args['nr_tempestividade_meta'])).",
					   nr_tempestividade_1    = ".(trim($args['nr_tempestividade_1']) == "" ? "DEFAULT" : floatval($args['nr_tempestividade_1'])).",
					   nr_tempestividade_2    = ".(trim($args['nr_tempestividade_2']) == "" ? "DEFAULT" : floatval($args['nr_tempestividade_2'])).",
					   nr_tempestividade_3    = ".(trim($args['nr_tempestividade_3']) == "" ? "DEFAULT" : floatval($args['nr_tempestividade_3'])).",
					   nr_tempestividade_4    = ".(trim($args['nr_tempestividade_4']) == "" ? "DEFAULT" : floatval($args['nr_tempestividade_4'])).",
					   nr_tempestividade_5    = ".(trim($args['nr_tempestividade_5']) == "" ? "DEFAULT" : floatval($args['nr_tempestividade_5'])).",

					   nr_relevancia_meta     = ".(trim($args['nr_relevancia_meta']) == "" ? "DEFAULT" : trim($args['nr_relevancia_meta'])).",
					   nr_relevancia_1        = ".(trim($args['nr_relevancia_1']) == "" ? "DEFAULT" : floatval($args['nr_relevancia_1'])).",
					   nr_relevancia_2        = ".(trim($args['nr_relevancia_2']) == "" ? "DEFAULT" : floatval($args['nr_relevancia_2'])).",
					   nr_relevancia_3        = ".(trim($args['nr_relevancia_3']) == "" ? "DEFAULT" : floatval($args['nr_relevancia_3'])).",
					   nr_relevancia_4        = ".(trim($args['nr_relevancia_4']) == "" ? "DEFAULT" : floatval($args['nr_relevancia_4'])).",
					   nr_relevancia_5        = ".(trim($args['nr_relevancia_5']) == "" ? "DEFAULT" : floatval($args['nr_relevancia_5'])).",

					   cd_indicador_tabela    = ".(intval($args['cd_indicador_tabela']) == 0 ? "DEFAULT" : intval($args['cd_indicador_tabela'])).",
					   fl_media               = ".(trim($args['fl_media']) == "" ? "''" : "'".trim($args["fl_media"])."'").",
					   observacao             = ".(trim($args['observacao']) == "" ? "DEFAULT" : "'".trim($args["observacao"])."'").",
					   cd_usuario_alteracao   = ".intval($args['cd_usuario']).",
					   dt_alteracao           = CURRENT_TIMESTAMP
			     WHERE cd_controladoria_informativo_gerencial = ".intval($args['cd_controladoria_informativo_gerencial']).";";
		}

		$result = $this->db->query($qr_sql);
	}

	function excluir(&$result, $args=array())
	{
		$qr_sql = " 
			UPDATE indicador_plugin.controladoria_informativo_gerencial
		       SET dt_exclusao         = CURRENT_TIMESTAMP, 
			       cd_usuario_exclusao = ".intval($args['cd_usuario'])."
		     WHERE cd_controladoria_informativo_gerencial = ".intval($args['cd_controladoria_informativo_gerencial']).";"; 
			 
		$result = $this->db->query($qr_sql);
	}
	
	function atualiza_fechar_periodo(&$result, $args=array())
	{
		$qr_sql = " 
			INSERT INTO indicador_plugin.controladoria_informativo_gerencial 
				 (
					dt_referencia,
					nr_satisfacao,
					nr_meta,
					cd_indicador_tabela,
					fl_media, 
					cd_usuario_inclusao,
					cd_usuario_alteracao
				 ) 
			VALUES 
				 ( 
					".(trim($args['dt_referencia']) == "" ? "DEFAULT" : "TO_DATE('".trim($args["dt_referencia"])."', 'DD/MM/YYYY')").",
					".(trim($args['nr_satisfacao']) == "" ? "DEFAULT" : floatval($args['nr_satisfacao'])).",
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