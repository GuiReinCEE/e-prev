<?php
class Originais_retificacoes_dirf_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	function listar( &$result, $args=array() )
	{
		$qr_sql = "
            SELECT i.cd_originais_retificacoes_dirf, 
                   TO_CHAR(i.dt_referencia,'YYYY') AS ano_referencia,
				   TO_CHAR(i.dt_referencia,'YYYY') AS mes_referencia,
                   i.dt_referencia,
				   i.cd_indicador_tabela,
				   i.fl_media,
				   i.observacao,
                   i.nr_original, 
                   i.nr_retificacao_1, 
                   i.nr_retificacao_2, 
                   i.nr_retificacao_3, 
                   i.nr_retificacao_4, 
                   i.nr_retificacao_5, 
                   i.nr_declaracoes_entregue, 
                   i.nr_meta
              FROM indicador_controladoria.originais_retificacoes_dirf i
             WHERE i.dt_exclusao IS NULL
		       AND (i.fl_media = 'S' OR i.cd_indicador_tabela = ".intval($args['cd_indicador_tabela']).")
		     ORDER BY i.dt_referencia ASC;";
		
		$result = $this->db->query($qr_sql);
	}

	function carrega_referencia(&$result, $args=array())
	{
		$qr_sql = "
			SELECT TO_CHAR(dt_referencia + '1 year'::interval, 'YYYY') AS dt_referencia_n, 
				   nr_meta, 
				   cd_indicador_tabela 
			  FROM indicador_controladoria.originais_retificacoes_dirf
			 WHERE dt_exclusao IS NULL 
			 ORDER BY dt_referencia DESC 
			 LIMIT 1;";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function carrega(&$result, $args=array())
	{
		$qr_sql = "
            SELECT cd_originais_retificacoes_dirf,
                   TO_CHAR(dt_referencia,'YYYY') AS dt_referencia,
                   cd_indicador_tabela,
                   fl_media,
                   nr_original,
                   nr_retificacao_1,
                   nr_retificacao_2,
                   nr_retificacao_3,
                   nr_retificacao_4,
                   nr_retificacao_5,
                   nr_meta,
                   observacao
		      FROM indicador_controladoria.originais_retificacoes_dirf 
			 WHERE cd_originais_retificacoes_dirf = ".intval($args['cd_originais_retificacoes_dirf']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function salvar(&$result, $args=array())
	{
		if(intval($args['cd_originais_retificacoes_dirf']) == 0)
		{
			$qr_sql = "
                INSERT INTO indicador_controladoria.originais_retificacoes_dirf
                     (
                       dt_referencia, 
                       cd_indicador_tabela, 
                       nr_original, 
                       nr_retificacao_1, 
                       nr_retificacao_2, 
                       nr_retificacao_3, 
                       nr_retificacao_4, 
                       nr_retificacao_5,  
                       nr_meta, 
                       observacao, 
                       fl_media,  
                       cd_usuario_inclusao, 
                       cd_usuario_alteracao
                     )
                VALUES 
                     (
                       ".(trim($args['dt_referencia']) == "" ? "DEFAULT" : "TO_DATE('".trim($args["dt_referencia"])."', 'DD/MM/YYYY')").",
                       ".(intval($args['cd_indicador_tabela']) == 0 ? "DEFAULT" : intval($args['cd_indicador_tabela'])).",
                       ".(trim($args['nr_original']) == "" ? "DEFAULT" : intval($args['nr_original'])).",
                       ".(trim($args['nr_retificacao_1']) == "" ? "DEFAULT" : intval($args['nr_retificacao_1'])).",
                       ".(trim($args['nr_retificacao_2']) == "" ? "DEFAULT" : intval($args['nr_retificacao_2'])).",
                       ".(trim($args['nr_retificacao_3']) == "" ? "DEFAULT" : intval($args['nr_retificacao_3'])).",
                       ".(trim($args['nr_retificacao_4']) == "" ? "DEFAULT" : intval($args['nr_retificacao_4'])).",
                       ".(trim($args['nr_retificacao_5']) == "" ? "DEFAULT" : intval($args['nr_retificacao_5'])).",
                       ".(trim($args['nr_meta']) == "" ? "DEFAULT" : intval($args['nr_meta'])).",
                       ".(trim($args['observacao']) == "" ? "DEFAULT" : "'".trim($args["observacao"])."'").",
                       ".(trim($args['fl_media']) == "" ? "DEFAULT" : "'".trim($args["fl_media"])."'").",
                       ".intval($args['cd_usuario']).",
					   ".intval($args['cd_usuario'])."
                     );";
		}
		else
		{
			$qr_sql = "
                UPDATE indicador_controladoria.originais_retificacoes_dirf
                   SET dt_referencia        = ".(trim($args['dt_referencia']) == "" ? "DEFAULT" : "TO_DATE('".trim($args["dt_referencia"])."', 'DD/MM/YYYY')").",  
                       cd_indicador_tabela  = ".(intval($args['cd_indicador_tabela']) == 0 ? "DEFAULT" : intval($args['cd_indicador_tabela'])).",
                       nr_original          = ".(trim($args['nr_original']) == "" ? "DEFAULT" : intval($args['nr_original'])).",
                       nr_retificacao_1     = ".(trim($args['nr_retificacao_1']) == "" ? "DEFAULT" : intval($args['nr_retificacao_1'])).",
                       nr_retificacao_2     = ".(trim($args['nr_retificacao_2']) == "" ? "DEFAULT" : intval($args['nr_retificacao_2'])).",
                       nr_retificacao_3     = ".(trim($args['nr_retificacao_3']) == "" ? "DEFAULT" : intval($args['nr_retificacao_3'])).",
                       nr_retificacao_4     = ".(trim($args['nr_retificacao_4']) == "" ? "DEFAULT" : intval($args['nr_retificacao_4'])).",
                       nr_retificacao_5     = ".(trim($args['nr_retificacao_5']) == "" ? "DEFAULT" : intval($args['nr_retificacao_5'])).",
                       nr_meta              = ".(trim($args['nr_meta']) == "" ? "DEFAULT" : intval($args['nr_meta'])).",
                       observacao           = ".(trim($args['observacao']) == "" ? "DEFAULT" : "'".trim($args["observacao"])."'").",
                       fl_media             = ".(trim($args['fl_media']) == "" ? "DEFAULT" : "'".trim($args["fl_media"])."'").",
                       dt_alteracao         = CURRENT_TIMESTAMP,
                       cd_usuario_alteracao = ".intval($args['cd_usuario'])."
                 WHERE cd_originais_retificacoes_dirf = ".intval($args['cd_originais_retificacoes_dirf']).";";
		}

		$result = $this->db->query($qr_sql);
	}

	function excluir(&$result, $args=array())
	{
		$qr_sql = " 
			UPDATE indicador_controladoria.originais_retificacoes_dirf
		       SET dt_exclusao         = CURRENT_TIMESTAMP, 
			       cd_usuario_exclusao = ".intval($args['cd_usuario'])."
		     WHERE cd_originais_retificacoes_dirf = ".intval($args['cd_originais_retificacoes_dirf']).";"; 
			 
		$result = $this->db->query($qr_sql);
	}
}
?>