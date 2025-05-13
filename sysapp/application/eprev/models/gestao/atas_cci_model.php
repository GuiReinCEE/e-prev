<?php
class atas_cci_model extends Model
{
	function __construct()
    {
        parent::Model();
    }
	
	function listar(&$result, $args=array())
    {
        $qr_sql = "
			SELECT cci.cd_atas_cci,
			       TO_CHAR(cci.dt_reuniao, 'YYYY/MM') AS ano_mes,
				   cci.nr_reuniao,
				   TO_CHAR(cci.dt_reuniao, 'DD/MM/YYYY') AS dt_reuniao,
				   TO_CHAR(cci.dt_ata_cci, 'DD/MM/YYYY') AS dt_ata_cci,
				   TO_CHAR(cci.dt_sumula_cci, 'DD/MM/YYYY') AS dt_sumula_cci,
				   TO_CHAR(cci.dt_anexo_cci, 'DD/MM/YYYY') AS dt_anexo_cci,
				   TO_CHAR(cci.dt_homologado_diretoria, 'DD/MM/YYYY') AS dt_homologado_diretoria,
				   TO_CHAR(cci.dt_homologado_conselho_fiscal, 'DD/MM/YYYY') AS dt_homologado_conselho_fiscal,
				   cci.fl_ata_cci,
				   cci.fl_sumula_cci,
				   cci.fl_anexo_cci,
				   CASE WHEN cci.fl_homologado_diretoria = 'N' AND cci.dt_homologado_diretoria IS NOT NULL  THEN  'S'
						ELSE cci.fl_homologado_diretoria
				   END AS fl_homologado_diretoria,
				   cci.nr_ata_diretoria,
				   CASE WHEN cci.fl_homologado_conselho_fiscal = 'N' AND cci.dt_homologado_conselho_fiscal IS NOT NULL  THEN  'S'
						ELSE cci.fl_homologado_conselho_fiscal
				   END AS fl_homologado_conselho_fiscal,
				   cci.nr_ata_conselho_fiscal,
				   cci.fl_publicado_alchemy,
				   cci.fl_publicado_eprev,
				   TO_CHAR(cci.dt_alteracao, 'DD/MM/YYYY HH24:MI:SS') AS dt_alteracao,
				   uc.nome AS usuario_alteracao,
				   (SELECT TO_CHAR(aca.dt_inclusao,'DD/MM/YYYY HH24:MI:SS') || ': ' || aca.descricao
				      FROM gestao.atas_cci_acompanhamento aca
					 WHERE aca.cd_atas_cci = cci.cd_atas_cci
					   AND aca.dt_exclusao IS NULL
					   AND aca.cd_gerencia = 'GC'
					 ORDER BY aca.dt_inclusao DESC
					 LIMIT 1) AS acompanhamento_gc,
				   (SELECT TO_CHAR(aca.dt_inclusao,'DD/MM/YYYY HH24:MI:SS') || ': ' || aca.descricao 
				      FROM gestao.atas_cci_acompanhamento aca
					 WHERE aca.cd_atas_cci = cci.cd_atas_cci
					   AND aca.dt_exclusao IS NULL
					   AND aca.cd_gerencia = 'GIN'
					 ORDER BY aca.dt_inclusao DESC
					 LIMIT 1) AS acompanhamento_gin,
                   (SELECT TO_CHAR(CASE WHEN e.fl_dia_util = 'S'
							  		    THEN funcoes.dia_util('DEPOIS', (SELECT ac2.dt_reuniao FROM gestao.atas_cci ac2 WHERE ac2.cd_atas_cci = cci.cd_atas_cci), qt_dias)
										ELSE (SELECT ac2.dt_reuniao FROM gestao.atas_cci ac2 WHERE ac2.cd_atas_cci = cci.cd_atas_cci) + qt_dias
								   END,'DD/MM/YYYY') || ' : ' || e.ds_atas_cci_etapas_investimento
					  FROM gestao.atas_cci_etapas_investimento e
					  LEFT JOIN gestao.atas_cci_etapas_investimento_checked aec
						ON aec.cd_atas_cci_etapas_investimento = e.cd_atas_cci_etapas_investimento
					   AND aec.cd_atas_cci = cci.cd_atas_cci
					   AND aec.dt_exclusao IS NULL
					 WHERE e.dt_exclusao IS NULL
					   AND 'N' = (CASE WHEN aec.dt_inclusao IS NULL THEN 'N'
									   ELSE 'S'
								  END)
					 ORDER BY (CASE WHEN e.fl_dia_util = 'S'
									THEN funcoes.dia_util('DEPOIS', (SELECT ac2.dt_reuniao FROM gestao.atas_cci ac2 WHERE ac2.cd_atas_cci = cci.cd_atas_cci), qt_dias)
									ELSE (SELECT ac2.dt_reuniao FROM gestao.atas_cci ac2 WHERE ac2.cd_atas_cci = cci.cd_atas_cci) + qt_dias
								END)
					 LIMIT 1) AS etapa			 
			  FROM gestao.atas_cci cci
			  LEFT JOIN projetos.usuarios_controledi uc
				ON uc.codigo = cd_usuario_alteracao
			 WHERE dt_exclusao IS NULL
			   ".(trim($args['nr_ano']) != '' ? "AND TO_CHAR(cci.dt_reuniao, 'YYYY') = '".intval($args['nr_ano'])."'" : '')."
			   ".(trim($args['nr_reuniao']) != '' ? "AND cci.nr_reuniao = '".trim($args['nr_reuniao'])."'" : '')."
			   ".(trim($args['fl_ata_cci']) != '' ? "AND cci.fl_ata_cci = '".trim($args['fl_ata_cci'])."'" : '')."
			   ".(trim($args['fl_sumula_cci']) != '' ? "AND cci.fl_sumula_cci = '".trim($args['fl_sumula_cci'])."'" : '')."
			   ".(trim($args['fl_anexo_cci']) != '' ? "AND cci.fl_anexo_cci = '".trim($args['fl_anexo_cci'])."'" : '')."
			   ".(trim($args['fl_homologado_diretoria']) == 'S' ? "AND cci.dt_homologado_diretoria IS NOT NULL" : '')."
			   ".(trim($args['fl_homologado_diretoria']) == 'N' ? "AND cci.dt_homologado_diretoria IS NULL" : '')."
			   ".(trim($args['fl_homologado_conselho_fiscal']) == 'S' ? "AND cci.dt_homologado_conselho_fiscal IS NOT NULL" : '')."
			   ".(trim($args['fl_homologado_conselho_fiscal']) == 'N' ? "AND cci.dt_homologado_conselho_fiscal IS NULL" : '')."
			   ".(trim($args['fl_publicado_alchemy']) != '' ? "AND cci.fl_publicado_alchemy = '".trim($args['fl_publicado_alchemy'])."'" : '')."
			   ".(trim($args['fl_publicada_eprev']) != '' ? "AND cci.fl_ata_cci = '".trim($args['fl_publicada_eprev'])."'" : '')."
			   ".(trim($args['fl_etapa']) != '' ? "AND ".(trim($args['fl_etapa']) == 'S' ? "0 = "  : "0 < " )." 
						    (SELECT COUNT(*)
							   FROM gestao.atas_cci_etapas_investimento e
							   LEFT JOIN gestao.atas_cci_etapas_investimento_checked aec
								 ON aec.cd_atas_cci_etapas_investimento = e.cd_atas_cci_etapas_investimento
							    AND aec.cd_atas_cci = cci.cd_atas_cci
							    AND aec.dt_exclusao IS NULL
							  WHERE e.dt_exclusao IS NULL
							    AND 'N' = (CASE WHEN aec.dt_inclusao IS NULL THEN 'N'
											   ELSE 'S'
										  END)) " : '')."
				
			ORDER BY ano_mes DESC, cci.dt_reuniao ASC;";
						
		$result = $this->db->query($qr_sql);
	}
	
	function carrega(&$result, $args=array())
    {
        $qr_sql = "
			SELECT cd_atas_cci,
				   nr_reuniao,
				   TO_CHAR(dt_reuniao, 'DD/MM/YYYY') AS dt_reuniao,
				   TO_CHAR(dt_ata_cci, 'DD/MM/YYYY') AS dt_ata_cci,
				   TO_CHAR(dt_sumula_cci, 'DD/MM/YYYY') AS dt_sumula_cci,
				   TO_CHAR(dt_anexo_cci, 'DD/MM/YYYY') AS dt_anexo_cci,
				   TO_CHAR(dt_homologado_diretoria, 'DD/MM/YYYY') AS dt_homologado_diretoria,
				   TO_CHAR(dt_homologado_conselho_fiscal, 'DD/MM/YYYY') AS dt_homologado_conselho_fiscal,
				   fl_ata_cci,
				   fl_sumula_cci,
				   fl_anexo_cci,
				   fl_homologado_conselho_fiscal,
				   nr_ata_diretoria,
				   nr_ata_conselho_fiscal,
				   fl_publicado_alchemy,
				   fl_publicado_eprev,
				   cd_responsavel_investimento
			  FROM gestao.atas_cci
			 WHERE cd_atas_cci = ".intval($args['cd_atas_cci']);
		
		$result = $this->db->query($qr_sql);
	}
	
	function salvar(&$result, $args=array())
    {
        if(intval($args['cd_atas_cci']) > 0)
        {
			$cd_atas_cci = intval($args['cd_atas_cci']);
			$qr_sql = "
						UPDATE gestao.atas_cci
						   SET nr_reuniao                    = '".trim($args['nr_reuniao'])."',
							   dt_reuniao                    = TO_DATE('".$args['dt_reuniao']."', 'DD/MM/YYYY'),
							   fl_ata_cci                    = ".(trim($args['fl_ata_cci']) != '' ? "'".trim($args['fl_ata_cci'])."'" : "DEFAULT").",
							   fl_sumula_cci                 = ".(trim($args['fl_sumula_cci']) != '' ? "'".trim($args['fl_sumula_cci'])."'" : "DEFAULT").",
							   fl_anexo_cci                  = ".(trim($args['fl_anexo_cci']) != '' ? "'".trim($args['fl_anexo_cci'])."'" : "DEFAULT").",
							   nr_ata_diretoria              = ".(trim($args['nr_ata_diretoria']) != '' ? "'".trim($args['nr_ata_diretoria'])."'" : "DEFAULT").",
							   nr_ata_conselho_fiscal        = ".(trim($args['nr_ata_conselho_fiscal']) != '' ? "'".trim($args['nr_ata_conselho_fiscal'])."'" : "DEFAULT").",
							   fl_publicado_alchemy          = ".(trim($args['fl_publicado_alchemy']) != '' ? "'".trim($args['fl_publicado_alchemy'])."'" : "DEFAULT").",
							   fl_publicado_eprev            = ".(trim($args['fl_publicado_eprev']) != '' ? "'".trim($args['fl_publicado_eprev'])."'" : "DEFAULT").",
							   fl_homologado_conselho_fiscal = ".(trim($args['fl_homologado_conselho_fiscal']) != '' ? "'".trim($args['fl_homologado_conselho_fiscal'])."'" : "DEFAULT").",
							   dt_ata_cci                    = ".(trim($args['dt_ata_cci']) != '' ? "TO_DATE('".$args['dt_ata_cci']."', 'DD/MM/YYYY')" : "DEFAULT").",
							   dt_sumula_cci                 = ".(trim($args['dt_sumula_cci']) != '' ? "TO_DATE('".$args['dt_sumula_cci']."', 'DD/MM/YYYY')" : "DEFAULT").",
							   dt_anexo_cci                  = ".(trim($args['dt_anexo_cci']) != '' ? "TO_DATE('".$args['dt_anexo_cci']."', 'DD/MM/YYYY')" : "DEFAULT").",
							   dt_homologado_conselho_fiscal = ".(trim($args['dt_homologado_conselho_fiscal']) != '' ? "TO_DATE('".$args['dt_homologado_conselho_fiscal']."', 'DD/MM/YYYY')" : "DEFAULT").",
							   dt_homologado_diretoria       = ".(trim($args['dt_homologado_diretoria']) != '' ? "TO_DATE('".$args['dt_homologado_diretoria']."', 'DD/MM/YYYY')" : "DEFAULT").",
							   cd_responsavel_investimento   = ".(intval($args['cd_responsavel_investimento']) > 0 ? intval($args['cd_responsavel_investimento']) : "DEFAULT").",
							   cd_usuario_alteracao          = ".intval($args['cd_usuario']).",
							   dt_alteracao                  = CURRENT_TIMESTAMP
						 WHERE cd_atas_cci = ".intval($args['cd_atas_cci'])."
				      ";
		}
		else
		{
			$new_id = intval($this->db->get_new_id("gestao.atas_cci", "cd_atas_cci"));
			$cd_atas_cci = intval($new_id);
			$qr_sql = "
				INSERT INTO gestao.atas_cci
				     (
					   cd_atas_cci,
					   nr_reuniao,
					   dt_reuniao,
					   fl_ata_cci,
					   fl_sumula_cci,
					   fl_anexo_cci,
					   nr_ata_diretoria,
					   nr_ata_conselho_fiscal,
					   fl_publicado_alchemy,
					   fl_homologado_conselho_fiscal,
					   fl_publicado_eprev,
					   dt_ata_cci,
					   dt_sumula_cci,
					   dt_homologado_conselho_fiscal,
					   dt_homologado_diretoria,
					   dt_anexo_cci,
					   cd_usuario_inclusao,
					   cd_usuario_alteracao
					 )
			    VALUES
				     (
						".intval($cd_atas_cci).",
						'".trim($args['nr_reuniao'])."',
						TO_DATE('".$args['dt_reuniao']."', 'DD/MM/YYYY'),
						".(trim($args['fl_ata_cci']) != '' ? "'".trim($args['fl_ata_cci'])."'" : "DEFAULT").",
						".(trim($args['fl_sumula_cci']) != '' ? "'".trim($args['fl_sumula_cci'])."'" : "DEFAULT").",
						".(trim($args['fl_anexo_cci']) != '' ? "'".trim($args['fl_anexo_cci'])."'" : "DEFAULT").",
						".(trim($args['nr_ata_diretoria']) != '' ? "'".trim($args['nr_ata_diretoria'])."'" : "DEFAULT").",
						".(trim($args['nr_ata_conselho_fiscal']) != '' ? "'".trim($args['nr_ata_conselho_fiscal'])."'" : "DEFAULT").",
						".(trim($args['fl_publicado_alchemy']) != '' ? "'".trim($args['fl_publicado_alchemy'])."'" : "DEFAULT").",
						".(trim($args['fl_homologado_conselho_fiscal']) != '' ? "'".trim($args['fl_homologado_conselho_fiscal'])."'" : "DEFAULT").",
						".(trim($args['fl_publicado_eprev']) != '' ? "'".trim($args['fl_publicado_eprev'])."'" : "DEFAULT").",
						".(trim($args['dt_ata_cci']) != '' ? "TO_DATE('".$args['dt_ata_cci']."', 'DD/MM/YYYY')" : "TO_DATE('".$args['dt_reuniao']."', 'DD/MM/YYYY')").",
						".(trim($args['dt_sumula_cci']) != '' ? "TO_DATE('".$args['dt_sumula_cci']."', 'DD/MM/YYYY')" : "DEFAULT").",
						".(trim($args['dt_homologado_diretoria']) != '' ? "TO_DATE('".$args['dt_homologado_diretoria']."', 'DD/MM/YYYY')" : "DEFAULT").",
						".(trim($args['dt_homologado_conselho_fiscal']) != '' ? "TO_DATE('".$args['dt_homologado_conselho_fiscal']."', 'DD/MM/YYYY')" : "DEFAULT").",
						".(trim($args['dt_anexo_cci']) != '' ? "TO_DATE('".$args['dt_anexo_cci']."', 'DD/MM/YYYY')" : (trim($args['dt_sumula_cci']) != '' ? "TO_DATE('".$args['dt_sumula_cci']."', 'DD/MM/YYYY')" : "DEFAULT") ).",
						".intval($args['cd_usuario']).",
						".intval($args['cd_usuario'])."
					 )
				";
		}

		$this->db->query($qr_sql);
		
		return $cd_atas_cci;
	}
	
	function excluir(&$result, $args=array())
    {
		$qr_sql = "
			UPDATE gestao.atas_cci
			   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
			       dt_exclusao         = CURRENT_TIMESTAMP
			 WHERE cd_atas_cci = ".intval($args['cd_atas_cci'])."";
			 
		$this->db->query($qr_sql);
	}
	
	function lista_acompanhamento(&$result, $args=array())
	{
		$qr_sql = "
			SELECT ac.cd_atas_cci_acompanhamento,
			       ac.descricao,
				   TO_CHAR(ac.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   uc.nome
			  FROM gestao.atas_cci_acompanhamento ac
			  JOIN projetos.usuarios_controledi uc
			    ON uc.codigo = ac.cd_usuario_inclusao
			 WHERE ac.dt_exclusao IS NULL
			   AND ac.cd_atas_cci = ".intval($args['cd_atas_cci'])."
			   AND ac.cd_gerencia = '".trim($args['cd_gerencia'])."'
			 ORDER BY ac.dt_inclusao DESC";
			   
		$result = $this->db->query($qr_sql);
	}
	
	function salvar_acompanhamento(&$result, $args=array())
	{
		if(intval($args['cd_atas_cci_acompanhamento']) == 0)
		{
			$qr_sql = "
				INSERT INTO gestao.atas_cci_acompanhamento
					 (
					   cd_atas_cci,
					   descricao,
					   cd_gerencia,
					   cd_usuario_inclusao,
					   cd_usuario_alteracao
					 )
				VALUES
					 (
					   ".intval($args['cd_atas_cci']).",
					   '".trim($args['descricao'])."',
					   '".trim($args['cd_gerencia'])."',
					   ".intval($args['cd_usuario']).",
					   ".intval($args['cd_usuario'])."
					 )";
			}
		else
		{
			$qr_sql = "
				UPDATE gestao.atas_cci_acompanhamento
				   SET descricao = '".trim($args['descricao'])."',
				       dt_alteracao = CURRENT_TIMESTAMP,
			           cd_usuario_alteracao = ".intval($args['cd_usuario'])."
				 WHERE cd_atas_cci_acompanhamento = ".intval($args['cd_atas_cci_acompanhamento']);
		}
		
		$result = $this->db->query($qr_sql);
	}
	
	function carrega_acompanhamento(&$result, $args=array())
	{
		$qr_sql = "
			SELECT descricao
			  FROM gestao.atas_cci_acompanhamento
			 WHERE cd_atas_cci_acompanhamento = ".intval($args['cd_atas_cci_acompanhamento']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function excluir_acompanhamento(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE gestao.atas_cci_acompanhamento
			   SET dt_exclusao = CURRENT_TIMESTAMP,
			       cd_usuario_exclusao = ".intval($args['cd_usuario'])."
			 WHERE cd_atas_cci_acompanhamento = ".intval($args['cd_atas_cci_acompanhamento']);
			 
		$result = $this->db->query($qr_sql);
	}
	
	function usuarios_investimento(&$result, $args=array())
	{
		$qr_sql = "
					SELECT uc.codigo AS value,
					       uc.nome AS text
					  FROM projetos.usuarios_controledi uc
					 WHERE (uc.tipo NOT IN('X') AND uc.divisao = 'GIN')
						OR (0 < (SELECT COUNT(*) 
								   FROM gestao.atas_cci ac 
								  WHERE ac.dt_exclusao IS NULL 
								    AND ac.cd_atas_cci = ".intval($args["cd_atas_cci"])."
									AND ac.cd_responsavel_investimento = uc.codigo))		
					 ORDER BY text
                  ";			 
		$result = $this->db->query($qr_sql);
	}	
	
	function lista_etapas_investimento(&$result, $args=array())
	{
		$qr_sql = "
			SELECT TO_CHAR(CASE WHEN e.fl_dia_util = 'S'
								THEN funcoes.dia_util('DEPOIS', (SELECT ac.dt_reuniao FROM gestao.atas_cci ac WHERE ac.cd_atas_cci = ".intval($args['cd_atas_cci'])."), qt_dias)
								ELSE (SELECT ac.dt_reuniao FROM gestao.atas_cci ac WHERE ac.cd_atas_cci = ".intval($args['cd_atas_cci']).") + qt_dias
						   END,'DD/MM/YYYY') AS dt_limite, 
				   e.ds_atas_cci_etapas_investimento AS ds_etapa,
				   CASE WHEN aec.dt_inclusao IS NULL THEN 'N'
						ELSE 'S'
				   END AS checked ,
				   e.cd_atas_cci_etapas_investimento
			  FROM gestao.atas_cci_etapas_investimento e
			  LEFT JOIN gestao.atas_cci_etapas_investimento_checked aec
				ON aec.cd_atas_cci_etapas_investimento = e.cd_atas_cci_etapas_investimento
			   AND aec.cd_atas_cci = ".intval($args['cd_atas_cci'])."
			   AND aec.dt_exclusao IS NULL
			 WHERE e.dt_exclusao IS NULL
			 ORDER BY (CASE WHEN e.fl_dia_util = 'S'
								THEN funcoes.dia_util('DEPOIS', (SELECT ac.dt_reuniao FROM gestao.atas_cci ac WHERE ac.cd_atas_cci = ".intval($args['cd_atas_cci'])."), qt_dias)
								ELSE (SELECT ac.dt_reuniao FROM gestao.atas_cci ac WHERE ac.cd_atas_cci = ".intval($args['cd_atas_cci']).") + qt_dias
						   END);";			
		$result = $this->db->query($qr_sql);
	}	
	
	function checked_etapa(&$result, $args=array())
	{
		if(is_array($args['etapas']) > 0)
		{
			$qr_sql = "
				UPDATE gestao.atas_cci_etapas_investimento_checked
				   SET dt_exclusao         = CURRENT_TIMESTAMP,
					   cd_usuario_exclusao = ".intval($args['cd_usuario'])."
				 WHERE cd_atas_cci = ".intval($args['cd_atas_cci'])."
				   AND cd_atas_cci_etapas_investimento NOT IN (".implode(", ", $args['etapas']).");";
				   
			foreach($args['etapas'] as $item)
			{
				$qr_sql .= "
					INSERT INTO gestao.atas_cci_etapas_investimento_checked
						 (
							cd_atas_cci,
							cd_atas_cci_etapas_investimento,
							cd_usuario_inclusao
						 )
					SELECT ".intval($args['cd_atas_cci']).",
						   ".intval($item).",
						   ".intval($args['cd_usuario'])."
					 WHERE 0 =
						 (
						   SELECT COUNT(*)
							 FROM gestao.atas_cci_etapas_investimento_checked
							WHERE dt_exclusao IS NULL
							  AND cd_atas_cci                     = ".intval($args['cd_atas_cci'])."
							  AND cd_atas_cci_etapas_investimento = ".intval($item)."
						 );";
			}
		}
		else
		{
			$qr_sql = "
				UPDATE gestao.atas_cci_etapas_investimento_checked
				   SET dt_exclusao         = CURRENT_TIMESTAMP,
					   cd_usuario_exclusao = ".intval($args['cd_usuario'])."
				 WHERE cd_atas_cci = ".intval($args['cd_atas_cci']).";";
		}
		
		$result = $this->db->query($qr_sql);
	}
	
	public function get_assinatura($cd_atas_cci)
    {
        $qr_sql = "
			SELECT a.cd_atas_cci,
				   TO_CHAR(d.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   d.id_doc,
				   d.fl_status,
				   CASE WHEN d.fl_status = 'RUNNING' THEN 'EM PROCESSO DE ASSINATURA'
						WHEN d.fl_status = 'CLOSED' THEN 'FINALIZADO'
						WHEN d.fl_status = 'CANCELED' THEN 'CANCELADO'
						ELSE d.fl_status
				   END ds_status,
				   CASE WHEN d.fl_status = 'RUNNING' THEN 'label label-warning'
						WHEN d.fl_status = 'CLOSED' THEN 'label label-success'
						WHEN d.fl_status = 'CANCELED' THEN 'label label-important'
						ELSE 'label'
				   END cor_status
			  FROM gestao.atas_cci a
			  JOIN clicksign.documento d
				ON d.nr_reuniao_atas_cci = a.nr_reuniao
			 WHERE a.cd_atas_cci = ".intval($cd_atas_cci)."	
			 ORDER BY d.dt_inclusao DESC;";

        return $this->db->query($qr_sql)->result_array();
    }	
}
?>