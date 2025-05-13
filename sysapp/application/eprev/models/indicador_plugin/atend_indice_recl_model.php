<?php
class Atend_indice_recl_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	function listar($cd_indicador_tabela)
	{
		$qr_sql = "
			SELECT i.cd_atend_indice_recl,
				   TO_CHAR(i.dt_referencia,'YYYY') AS ano_referencia,
				   TO_CHAR(i.dt_referencia,'MM/YYYY') AS mes_referencia,
				   i.dt_referencia,
				   i.cd_indicador_tabela,
				   i.fl_media,
				   i.observacao,
				   i.nr_total_participantes,
				   i.nr_total_reclamacoes,
				   i.nr_nao_procede,
				   i.nr_procede,
                   i.nr_abertas,
				   i.nr_percentual_reclamacoes,
				   i.nr_percentual_nao_procede,
				   i.nr_percentual_procede,
                   i.nr_percentual_abertas,
                   i.nr_meta
		      FROM indicador_plugin.atend_indice_recl i
		     WHERE i.dt_exclusao IS NULL
		       AND(i.fl_media = 'S' OR i.cd_indicador_tabela = ".intval($cd_indicador_tabela).")
		     ORDER BY i.dt_referencia ASC;";
		
		return $this->db->query($qr_sql)->result_array();
	}

	function carrega_referencia()
	{
		$qr_sql = "
			SELECT TO_CHAR(dt_referencia + '1 month'::interval, 'DD/MM/YYYY') AS dt_referencia_n, 
				   nr_meta, 
				   cd_indicador_tabela 
			  FROM indicador_plugin.atend_indice_recl
			 WHERE dt_exclusao IS NULL 
			 ORDER BY dt_referencia DESC 
			 LIMIT 1;";
			 
		return $this->db->query($qr_sql)->row_array();
	}
	
	function carrega($cd_atend_indice_recl)
	{
		$qr_sql = "
            SELECT cd_atend_indice_recl,
                   TO_CHAR(dt_referencia,'DD/MM/YYYY') AS dt_referencia,
                   cd_indicador_tabela,
                   fl_media,
                   nr_total_participantes,
                   nr_total_reclamacoes,
                   nr_nao_procede,
                   nr_procede,
                   nr_abertas,
                   nr_meta,
                   observacao
		      FROM indicador_plugin.atend_indice_recl 
			 WHERE cd_atend_indice_recl = ".intval($cd_atend_indice_recl).";";
			 
		return $this->db->query($qr_sql)->row_array();
	}
	
	function salvar($args=array())
	{
		if(intval($args['cd_atend_indice_recl']) == 0)
		{
			$qr_sql = "
				INSERT INTO indicador_plugin.atend_indice_recl 
				     (
						dt_referencia, 
					    nr_total_participantes, 
                        nr_total_reclamacoes, 
                        nr_nao_procede, 
                        nr_procede, 
                        nr_abertas,
                        nr_percentual_reclamacoes,
						nr_percentual_procede,
						nr_percentual_nao_procede,
						nr_percentual_abertas,
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
					    ".(trim($args['nr_total_participantes']) == "" ? "DEFAULT" : floatval($args['nr_total_participantes'])).",
					    ".(trim($args['nr_total_reclamacoes']) == "" ? "DEFAULT" : floatval($args['nr_total_reclamacoes'])).",
					    ".(trim($args['nr_nao_procede']) == "" ? "DEFAULT" : floatval($args['nr_nao_procede'])).",
					    ".(trim($args['nr_procede']) == "" ? "DEFAULT" : floatval($args['nr_procede'])).",
                        ".(trim($args['nr_abertas']) == "" ? "DEFAULT" : floatval($args['nr_abertas'])).",
                        ".(trim($args['nr_percentual_reclamacoes']) != '' ? floatval($args['nr_percentual_reclamacoes']) : "DEFAULT").",
                        ".(trim($args['nr_percentual_procede']) != '' ? floatval($args['nr_percentual_procede']) : "DEFAULT").",
                        ".(trim($args['nr_percentual_nao_procede']) != '' ? floatval($args['nr_percentual_nao_procede']) : "DEFAULT").",
                        ".(trim($args['nr_percentual_abertas']) != '' ? floatval($args['nr_percentual_abertas']) : "DEFAULT").",
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
				UPDATE indicador_plugin.atend_indice_recl
				   SET dt_referencia             = ".(trim($args['dt_referencia']) == "" ? "DEFAULT" : "TO_DATE('".trim($args["dt_referencia"])."', 'DD/MM/YYYY')").",
				       nr_total_participantes    = ".(trim($args['nr_total_participantes']) == "" ? "DEFAULT" : floatval($args['nr_total_participantes'])).",
					   nr_total_reclamacoes      = ".(trim($args['nr_total_reclamacoes']) == "" ? "DEFAULT" : floatval($args['nr_total_reclamacoes'])).",
					   nr_nao_procede            = ".(trim($args['nr_nao_procede']) == "" ? "DEFAULT" : floatval($args['nr_nao_procede'])).",
					   nr_procede                = ".(trim($args['nr_procede']) == "" ? "DEFAULT" : floatval($args['nr_procede'])).",
					   nr_percentual_reclamacoes = ".(trim($args['nr_percentual_reclamacoes']) != '' ? floatval($args['nr_percentual_reclamacoes']) : "DEFAULT").",     
					   nr_percentual_procede     = ".(trim($args['nr_percentual_procede']) != '' ? floatval($args['nr_percentual_procede']) : "DEFAULT").",
					   nr_percentual_nao_procede = ".(trim($args['nr_percentual_nao_procede']) != '' ? floatval($args['nr_percentual_nao_procede']) : "DEFAULT").",    
					   nr_percentual_abertas     = ".(trim($args['nr_percentual_abertas']) != '' ? floatval($args['nr_percentual_abertas']) : "DEFAULT").",  
                       nr_abertas                = ".(trim($args['nr_abertas']) == "" ? "DEFAULT" : floatval($args['nr_abertas'])).",
	                   nr_meta                   = ".(trim($args['nr_meta']) == "" ? "DEFAULT" : floatval($args['nr_meta'])).",
					   cd_indicador_tabela       = ".(intval($args['cd_indicador_tabela']) == 0 ? "DEFAULT" : intval($args['cd_indicador_tabela'])).",
					   fl_media                  = ".(trim($args['fl_media']) == "" ? "''" : "'".trim($args["fl_media"])."'").",
					   observacao                = ".(trim($args['observacao']) == "" ? "DEFAULT" : "'".trim($args["observacao"])."'").",
					   cd_usuario_alteracao      = ".intval($args['cd_usuario']).",
					   dt_alteracao              = CURRENT_TIMESTAMP
			     WHERE cd_atend_indice_recl      = ".intval($args['cd_atend_indice_recl']).";";
		}

		$this->db->query($qr_sql);
	}

	function excluir($cd_atend_indice_recl,$cd_usuario)
	{
		$qr_sql = " 
			UPDATE indicador_plugin.atend_indice_recl
		       SET dt_exclusao         = CURRENT_TIMESTAMP, 
			       cd_usuario_exclusao = ".intval($cd_usuario)."
		     WHERE cd_atend_indice_recl = ".intval($cd_atend_indice_recl).";"; 
			 
		$this->db->query($qr_sql);
	}
	
	function fechar_periodo($cd_indicador_tabela,$cd_usuario)
	{
		$qr_sql = "
			UPDATE indicador.indicador_tabela 
			   SET dt_fechamento_periodo         = CURRENT_TIMESTAMP, 
			       cd_usuario_fechamento_periodo = ".$cd_usuario." 
		     WHERE cd_indicador_tabela = ".intval($cd_indicador_tabela).";";

		$this->db->query($qr_sql);
	}
	
	function get_valores($args=array())
	{
		$qr_sql = "
					SELECT TO_CHAR(r.dt_inclusao,'YYYY/MM') AS nr_mes,
						   rrc.cd_reclamacao_retorno_classificacao,
						   rrc.ds_reclamacao_retorno_classificacao,
						   COUNT((r.ano,r.numero,r.tipo)) AS qt_item
					  FROM projetos.reclamacao r
					  LEFT JOIN projetos.reclamacao_atendimento ra
						ON ra.numero = r.numero
					   AND ra.ano    = r.ano
					   AND ra.tipo   = r.tipo
					  LEFT JOIN projetos.reclamacao_andamento ran
						ON ran.numero                  = r.numero
					   AND ran.ano                     = r.ano
					   AND ran.tipo                    = r.tipo
					   AND ran.tp_reclamacao_andamento = 'R' --RETORNO
					  LEFT JOIN projetos.reclamacao_retorno_classificacao rrc
						ON rrc.cd_reclamacao_retorno_classificacao = COALESCE((CASE WHEN COALESCE(ran.dt_reclamacao_retorno, ran.dt_inclusao) >= ((TO_DATE('01/' || TO_CHAR(".intval($args["nr_mes"]).",'FM00') || '/".intval($args["nr_ano"])."','DD/MM/YYYY') + '1 month'::INTERVAL)) 
																					THEN (CASE WHEN ran.dt_inclusao < ((TO_DATE('01/' || TO_CHAR(".intval($args["nr_mes"]).",'FM00') || '/".intval($args["nr_ano"])."','DD/MM/YYYY') + '1 month'::INTERVAL)) 
																					           THEN 2 --EM ANALISE
																							   ELSE 0 --ABERTA
																					      END) 
																					ELSE ran.cd_reclamacao_retorno_classificacao 
																			   END),0)
					 WHERE r.dt_exclusao IS NULL
					   AND r.tipo                               = 'R'
					   AND TO_CHAR(r.dt_inclusao,'YYYY')        = '".intval($args["nr_ano"])."'
					   AND TO_CHAR(r.dt_inclusao,'MM')::INTEGER = ".intval($args["nr_mes"])."
					 GROUP BY nr_mes, 
						      rrc.cd_reclamacao_retorno_classificacao,
						      rrc.ds_reclamacao_retorno_classificacao
					 ORDER BY nr_mes DESC;
		
		          ";
		
		return $this->db->query($qr_sql)->result_array();
	}
	
	function get_observacao($args=array())
	{
		$qr_sql = "
            SELECT TO_CHAR(r.dt_inclusao,'YYYY/MM') AS nr_mes,
                   rp.ds_reclamacao_programa,
                   COUNT((r.ano,r.numero,r.tipo)) AS qt_item
              FROM projetos.reclamacao r
              JOIN projetos.reclamacao_programa rp
                ON rp.cd_reclamacao_programa = r.cd_reclamacao_programa
             WHERE r.dt_exclusao IS NULL
               AND r.tipo                               = 'R'
               AND TO_CHAR(r.dt_inclusao,'YYYY')        = '".intval($args["nr_ano"])."'
               AND TO_CHAR(r.dt_inclusao,'MM')::INTEGER = ".intval($args["nr_mes"])."
             GROUP BY nr_mes, 
                      rp.ds_reclamacao_programa
             ORDER BY nr_mes DESC;";
			 
		return $this->db->query($qr_sql)->result_array();
	}
}	
?>