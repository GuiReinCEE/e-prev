<?php
class Divulgacao_model extends Model
{
    var $AR_FILTRO_EMAIL;
	function __construct()
    {
        parent::Model();
		
		$this->AR_FILTRO_EMAIL = Array();
    }
	
    function listar(&$result, $args=array())
    {
	
		$qr_sql = "
                SELECT d.cd_divulgacao, 
                       d.assunto,  
                       d.divisao,
                       TO_CHAR(d.dt_divulgacao, 'DD/MM/YYYY') as data_div,
                       COALESCE((SELECT COUNT(*)
                                   FROM projetos.envia_emails ea
                                  WHERE ea.cd_divulgacao = d.cd_divulgacao
                                    AND ea.fl_retornou = 'N'
                                    AND ea.dt_email_enviado IS NULL),0) AS qt_email_aguarda_env,
                       COALESCE((SELECT COUNT(*)
                                   FROM projetos.envia_emails er
                                  WHERE er.cd_divulgacao = d.cd_divulgacao
                                    AND er.fl_retornou = 'S'
                                    AND er.dt_email_enviado IS NOT NULL),0) AS qt_email_nao_env,
                       COALESCE((SELECT COUNT(*)
                                   FROM projetos.envia_emails ee
                                  WHERE ee.cd_divulgacao = d.cd_divulgacao
                                    AND ee.fl_retornou = 'N'
                                    AND ee.dt_email_enviado IS NOT NULL),0) AS qt_email_env,
                       COALESCE((SELECT COUNT(*)
                                   FROM projetos.envia_emails ee
                                  WHERE ee.cd_divulgacao = d.cd_divulgacao),0) AS qt_email,
                       TO_CHAR((SELECT MAX(ee.dt_email_enviado)
                                  FROM projetos.envia_emails ee
                                 WHERE ee.cd_divulgacao = d.cd_divulgacao
                                 GROUP BY ee.cd_divulgacao),'DD/MM/YYYY HH24:MI:SS') AS dt_ultimo_email_enviado,
                       COALESCE((SELECT COUNT(DISTINCT funcoes.cripto_re(ee.cd_empresa,ee.cd_registro_empregado,ee.seq_dependencia))
	                               FROM projetos.envia_emails ee
	                              WHERE ee.cd_divulgacao = d.cd_divulgacao
	                                AND COALESCE(ee.fl_visualizado,'N') = 'S'
	                                AND COALESCE(ee.cd_registro_empregado,0) > 0),0) AS qt_participante,
                       COALESCE((SELECT SUM(COALESCE(ee.qt_visualizado,0))
                                   FROM projetos.envia_emails ee
                                  WHERE ee.cd_divulgacao = d.cd_divulgacao),0) AS qt_visualizacao,
                       COALESCE((SELECT COUNT(*)
                                   FROM projetos.envia_emails ee
                                  WHERE ee.cd_divulgacao = d.cd_divulgacao
                                    AND COALESCE(ee.fl_visualizado,'N') = 'S'),0) AS qt_visualizacao_unica
                  FROM projetos.divulgacao d
                 WHERE 1 = 1
                 ".(((trim($args["dt_divulgacao_inicio"]) != "") and (trim($args["dt_divulgacao_fim"]) != "")) ? "AND DATE_TRUNC('day',d.dt_divulgacao) BETWEEN TO_DATE('".$args["dt_divulgacao_inicio"]."','DD/MM/YYYY')  AND TO_DATE('".$args["dt_divulgacao_fim"]."','DD/MM/YYYY')" : "")."
                 ".(trim($args["cd_publico"]) != "" ? " AND 1 = (SELECT COUNT(*)
                                                                   FROM projetos.divulgacoes_publicos dp
                                                                  WHERE dp.cd_divulgacao = d.cd_divulgacao
                                                                    AND dp.cd_publico = '".$args["cd_publico"]."')": "")."
                 ".(trim($args["cd_divisao"]) != '' ? "AND d.divisao = '".$args["cd_divisao"]."'" : "")."
                 ".(trim($args["nome"]) != "" ? "AND UPPER(d.assunto) LIKE UPPER('%".$args["nome"]."%')": "")."              
                 GROUP BY d.cd_divulgacao, 
                          d.assunto,  
                          d.divisao,
                          data_div              
                ";
				
        #echo "<pre style='text-align:center;'>$qr_sql</pre>";exit;

        $result = $this->db->query($qr_sql);
    }	

    function listarx(&$result, $args=array())
    {
		
		$qr_sql = "
                SELECT d.cd_divulgacao, 
                       d.assunto,  
                       d.divisao,
                       TO_CHAR(d.dt_divulgacao, 'DD/MM/YYYY') as data_div,
                       NULL AS qt_email_aguarda_env,
                       NULL AS qt_email_nao_env,
                       NULL AS qt_email_env,
                       NULL AS qt_email,
                       (SELECT TO_CHAR(de.dt_ultimo_email_enviado,'DD/MM/YYYY HH24:MI:SS') FROM projetos.divulgacao_ultimo_envio de WHERE de.cd_divulgacao = d.cd_divulgacao) AS dt_ultimo_email_enviado,
                       NULL AS qt_participante,
                       NULL AS qt_visualizacao,
                       NULL AS qt_visualizacao_unica
                  FROM projetos.divulgacao d
                 WHERE 1 = 1
                 ".(((trim($args["dt_divulgacao_inicio"]) != "") and (trim($args["dt_divulgacao_fim"]) != "")) ? "AND DATE_TRUNC('day',d.dt_divulgacao) BETWEEN TO_DATE('".$args["dt_divulgacao_inicio"]."','DD/MM/YYYY')  AND TO_DATE('".$args["dt_divulgacao_fim"]."','DD/MM/YYYY')" : "")."
                 ".(trim($args["cd_publico"]) != "" ? " AND 1 = (SELECT COUNT(*)
                                                                   FROM projetos.divulgacoes_publicos dp
                                                                  WHERE dp.cd_divulgacao = d.cd_divulgacao
                                                                    AND dp.cd_publico = '".$args["cd_publico"]."')": "")."
                 ".(trim($args["cd_divisao"]) != '' ? "AND d.divisao = '".$args["cd_divisao"]."'" : "")."
                 ".(trim($args["nome"]) != "" ? "AND UPPER(d.assunto) LIKE UPPER('%".$args["nome"]."%')": "")."              
                 GROUP BY d.cd_divulgacao, 
                          d.assunto,  
                          d.divisao,
                          data_div 
			     ORDER BY d.cd_divulgacao DESC
                ";        

				
        #echo "<pre style='text-align:center;'>$qr_sql</pre>";exit;

        $result = $this->db->query($qr_sql);
    }
	

    function listar_estatistica(&$result, $args=array())
    {
		if($args['campo'] == 'dt_ultimo_email_enviado')
		{
			$qr_sql = "
						SELECT 'dt_ultimo_email_enviado' AS item,
						       TO_CHAR(de.dt_ultimo_email_enviado,'DD/MM/YYYY HH24:MI:SS')::TEXT AS valor 
					  	  FROM projetos.divulgacao_ultimo_envio de 
						 WHERE de.cd_divulgacao = ".intval($args['cd_divulgacao'])."
                       ";			
			/*
			$qr_sql = "
						SELECT 'dt_ultimo_email_enviado' AS item, 
							   TO_CHAR(MAX(ee.dt_email_enviado),'DD/MM/YYYY HH24:MI:SS')::TEXT AS valor
						  FROM projetos.envia_emails ee
						 WHERE ee.cd_divulgacao = ".intval($args['cd_divulgacao'])."
                       ";						 
			*/
		}
		elseif($args['campo'] == 'qt_email_aguarda_env')
		{
			$qr_sql = "
						SELECT 'qt_email_aguarda_env' AS item, 
							   COUNT(*)::TEXT AS valor
						  FROM projetos.envia_emails ea
						 WHERE ea.cd_divulgacao = ".intval($args['cd_divulgacao'])."
							AND ea.fl_retornou = 'N'
							AND ea.dt_email_enviado IS NULL
                       ";						 
		}
		elseif($args['campo'] == 'qt_email_nao_env')
		{
			$qr_sql = "
						SELECT 'qt_email_nao_env' AS item,  
								COUNT(*)::TEXT AS valor
						  FROM projetos.envia_emails er
						 WHERE er.cd_divulgacao = ".intval($args['cd_divulgacao'])."
						   AND er.fl_retornou = 'S'
						   AND er.dt_email_enviado IS NOT NULL
                       ";						 
		}	
		elseif($args['campo'] == 'qt_email_env')
		{
			$qr_sql = "
						SELECT 'qt_email_env' AS item, 
							   COUNT(*)::TEXT AS valor
						  FROM projetos.envia_emails ee
						 WHERE ee.cd_divulgacao = ".intval($args['cd_divulgacao'])."
						   AND ee.fl_retornou = 'N'
						   AND ee.dt_email_enviado IS NOT NULL
                       ";						 
		}
		elseif($args['campo'] == 'qt_email')
		{
			$qr_sql = "
						SELECT 'qt_email' AS item, 
							   COUNT(*)::TEXT AS valor
						  FROM projetos.envia_emails ee
						 WHERE ee.cd_divulgacao = ".intval($args['cd_divulgacao'])."
                       ";						 
		}	
		elseif($args['campo'] == 'qt_participante')
		{
			$qr_sql = "
						SELECT 'qt_participante' AS item, 
							   COUNT(DISTINCT funcoes.cripto_re(ee.cd_empresa,ee.cd_registro_empregado,ee.seq_dependencia))::TEXT AS valor
						  FROM projetos.envia_emails ee
						 WHERE ee.cd_divulgacao = ".intval($args['cd_divulgacao'])."
						   AND COALESCE(ee.fl_visualizado,'N') = 'S'
						   AND COALESCE(ee.cd_registro_empregado,0) > 0
                       ";						 
		}	
		elseif($args['campo'] == 'qt_visualizacao')
		{
			$qr_sql = "
						SELECT 'qt_visualizacao' AS item, 
							   SUM(COALESCE(ee.qt_visualizado,0))::TEXT AS valor
						  FROM projetos.envia_emails ee
						 WHERE ee.cd_divulgacao = ".intval($args['cd_divulgacao'])."
                       ";						 
		}	
		elseif($args['campo'] == 'qt_visualizacao_unica')
		{
			$qr_sql = "
						SELECT 'qt_visualizacao_unica' AS item, 
							   COUNT(*)::TEXT AS valor
						  FROM projetos.envia_emails ee
						 WHERE ee.cd_divulgacao = ".intval($args['cd_divulgacao'])."
						   AND COALESCE(ee.fl_visualizado,'N') = 'S' 
                       ";						 
		}		
		
		/*
		$qr_sql = "
					
					SELECT 'dt_ultimo_email_enviado' AS item, 
						   TO_CHAR(MAX(ee.dt_email_enviado),'DD/MM/YYYY HH24:MI:SS')::TEXT AS valor
                      FROM projetos.envia_emails ee
                     WHERE ee.cd_divulgacao = ".intval($args['cd_divulgacao'])."
					 
					 UNION
					
					SELECT 'qt_email_aguarda_env' AS item, 
						   COUNT(*)::TEXT AS valor
					  FROM projetos.envia_emails ea
					 WHERE ea.cd_divulgacao = ".intval($args['cd_divulgacao'])."
						AND ea.fl_retornou = 'N'
						AND ea.dt_email_enviado IS NULL

					 UNION
								
					SELECT 'qt_email_nao_env' AS item,  
							COUNT(*)::TEXT AS valor
					  FROM projetos.envia_emails er
					 WHERE er.cd_divulgacao = ".intval($args['cd_divulgacao'])."
					   AND er.fl_retornou = 'S'
					   AND er.dt_email_enviado IS NOT NULL

					 UNION                              
								
					SELECT 'qt_email_env' AS item, 
						   COUNT(*)::TEXT AS valor
					  FROM projetos.envia_emails ee
					 WHERE ee.cd_divulgacao = ".intval($args['cd_divulgacao'])."
					   AND ee.fl_retornou = 'N'
					   AND ee.dt_email_enviado IS NOT NULL

					 UNION                              
								
					SELECT 'qt_email' AS item, 
						   COUNT(*)::TEXT AS valor
					  FROM projetos.envia_emails ee
					 WHERE ee.cd_divulgacao = ".intval($args['cd_divulgacao'])."

					 UNION

					SELECT 'qt_participante' AS item, 
						   COUNT(DISTINCT funcoes.cripto_re(ee.cd_empresa,ee.cd_registro_empregado,ee.seq_dependencia))::TEXT AS valor
					  FROM projetos.envia_emails ee
					 WHERE ee.cd_divulgacao = ".intval($args['cd_divulgacao'])."
					   AND COALESCE(ee.fl_visualizado,'N') = 'S'
					   AND COALESCE(ee.cd_registro_empregado,0) > 0
								
					 UNION                              

					SELECT 'qt_visualizacao' AS item, 
						   SUM(COALESCE(ee.qt_visualizado,0))::TEXT AS valor
					  FROM projetos.envia_emails ee
					 WHERE ee.cd_divulgacao = ".intval($args['cd_divulgacao'])."
							  
					 UNION 

					SELECT 'qt_visualizacao_unica' AS item, 
						   COUNT(*)::TEXT AS valor
					  FROM projetos.envia_emails ee
					 WHERE ee.cd_divulgacao = ".intval($args['cd_divulgacao'])."
					   AND COALESCE(ee.fl_visualizado,'N') = 'S'             
                  ";     
		*/
        #echo "<pre style='text-align:center;'>$qr_sql</pre>";exit;

        $result = $this->db->query($qr_sql);
    }	
    
    function gerencia(&$result, $args=array())
    {
        $qr_sql = "
                SELECT DISTINCT d.divisao AS text,
                       d.divisao AS value
                  FROM projetos.divulgacao d
                 WHERE d.divisao IS NOT NULL
                 GROUP BY d.divisao         
                ";
                
        $result = $this->db->query($qr_sql);
    }
    
    function listarEmail(&$result, $args=array())
    {
        $nr_pagina = intval($args['nr_pagina']) - 1;
        
        if($nr_pagina > 0)
        {
            $nr_pagina = ($nr_pagina * intval($args['qt_pagina']));
        }
        else
        {
            $nr_pagina = 0;
        }
        
        $qr_sql = "
                    SELECT ee.cd_email, 
                           ee.cd_empresa,
                           ee.cd_registro_empregado,
                           ee.seq_dependencia,
                           p.nome,
                           ee.para,
                           ee.cc,
                           ee.cco,
                           TO_CHAR(ee.dt_envio, 'dd/mm/yyyy HH24:MI:SS') AS dt_email, 
                           TO_CHAR(ee.dt_email_enviado, 'dd/mm/yyyy HH24:MI:SS') AS dt_envio, 
                           TO_CHAR(ee.dt_schedule_email, 'dd/mm/yyyy HH24:MI:SS') AS dt_schedule_email, 
                           ee.assunto,
                           ee.fl_retornou AS fl_retorno,
                           ee.fl_visualizado
                      FROM projetos.envia_emails ee 
                      LEFT JOIN public.participantes p
                        ON p.cd_empresa            = ee.cd_empresa
                       AND p.cd_registro_empregado = ee.cd_registro_empregado
                       AND p.seq_dependencia       = ee.seq_dependencia
                     WHERE ee.cd_divulgacao = ".intval($args['cd_divulgacao'])."
                       AND ee.fl_retornou = '".$args['fl_retornou']."'
                        ".(((trim($args['dt_email_ini']) != "") AND (trim($args['dt_email_fim']) != "")) ? " AND DATE_TRUNC('day', ee.dt_envio) BETWEEN TO_DATE('".$args['dt_email_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_email_fim']."', 'DD/MM/YYYY')" : "")."
                        ".(((trim($args['dt_envio_ini']) != "") AND (trim($args['dt_envio_fim']) != "")) ? " AND DATE_TRUNC('day', ee.dt_email_enviado) BETWEEN TO_DATE('".$args['dt_envio_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_envio_fim']."', 'DD/MM/YYYY')" : "")."
                        ".(trim($args["email_enviado"]) != "" ? "AND (LOWER(ee.para) LIKE LOWER('%".trim($args["email_enviado"])."%') OR LOWER(ee.cc) LIKE LOWER('%".trim($args["email_enviado"])."%') OR LOWER(ee.cco) LIKE LOWER('%".trim($args["email_enviado"])."%'))" : "")."
                        ".(trim($args["nome"]) != "" ? "AND UPPER(p.nome) LIKE funcoes.remove_acento(UPPER('%".trim($args["nome"])."%'))" : "")."
                        ".(trim($args["fl_lido"]) != "" ? "AND ee.fl_visualizado = '".trim($args["fl_lido"])."'" : "")."
                     ORDER BY ee.cd_email 
                     LIMIT ".intval($args['qt_pagina'])."
                    OFFSET ".$nr_pagina."
               ";
        #echo "<pre>$qr_sql</pre>";exit;
        $result = $this->db->query($qr_sql);
    }   
    
    function grupo(&$result, $args=array())
    {
        $qr_sql = "
                    SELECT dg.cd_divulgacao_grupo,
                           dg.ds_divulgacao_grupo,
                           dg.cd_lista,
                           CASE WHEN dgs.cd_divulgacao_grupo IS NOT NULL 
                                THEN 'S' 
                                ELSE 'N' 
                           END AS fl_marcado,
                           COALESCE(dgt.qt_registro,0) AS qt_registro
                      FROM projetos.divulgacao_grupo dg
                      LEFT JOIN projetos.divulgacao_grupo_total dgt
                        ON dgt.cd_divulgacao_grupo = dg.cd_divulgacao_grupo
                      LEFT JOIN projetos.divulgacao_grupo_selecionado dgs
                        ON dgs.cd_divulgacao_grupo = dg.cd_divulgacao_grupo
                       AND dgs.cd_divulgacao       = ".intval($args['cd_divulgacao'])."
                       AND dgs.dt_exclusao IS NULL
                     WHERE dg.dt_exclusao IS NULL
                     ORDER BY ds_divulgacao_grupo
                  ";
        $result = $this->db->query($qr_sql);
    }   

    public function lista_negra($cd_divulgacao)
    {
        $qr_sql = "
            SELECT lnd.cd_lista_negra_divulgacao,
                   lnd.ds_lista_negra_divulgacao,
                   (CASE WHEN dlnd.cd_divulgacao_lista_negra_divulgacao IS NOT NULL OR COALESCE(dlnd.cd_divulgacao, 0) = 0
                         THEN 'S' 
                         ELSE 'N' 
                   END) AS fl_marcado,
                   (SELECT COUNT(*)
                      FROM projetos.lista_negra_divulgacao_email lnde
                     WHERE lnde.dt_exclusao IS NULL
                       AND lnde.cd_lista_negra_divulgacao = lnd.cd_lista_negra_divulgacao) AS qt_registro
              FROM projetos.lista_negra_divulgacao lnd
              LEFT JOIN projetos.divulgacao_lista_negra_divulgacao dlnd
                ON dlnd.cd_lista_negra_divulgacao = lnd.cd_lista_negra_divulgacao
               AND dlnd.cd_divulgacao       = ".intval($cd_divulgacao)."
             WHERE lnd.dt_exclusao IS NULL
               AND dlnd.dt_exclusao IS NULL
             ORDER BY ds_lista_negra_divulgacao;";

        return $this->db->query($qr_sql)->result_array();
    }   
    
    function carrega(&$result, $args=array())
    {
        $qr_sql = "
                    SELECT d.cd_divulgacao, 
					       d.id_rementente,
                           TO_CHAR(d.dt_divulgacao, 'DD/MM/YYYY HH24:MI:SS') AS dt_divulgacao,
						   (SELECT TO_CHAR(de.dt_ultimo_email_enviado,'DD/MM/YYYY HH24:MI:SS') FROM projetos.divulgacao_ultimo_envio de WHERE de.cd_divulgacao = d.cd_divulgacao) AS dt_ultimo_email_enviado,
                           TO_CHAR(d.dt_agenda_email, 'DD/MM/YYYY') AS dt_agenda_email,
                           TO_CHAR(d.dt_agenda_email, 'HH24:MI') AS hr_agenda_email,
                           CASE WHEN d.dt_agenda_email IS NOT NULL THEN 'S' ELSE 'N' END AS fl_agenda_email,
                           d.remetente AS ds_remetente, 
                           d.assunto AS ds_assunto, 
                           d.conteudo AS ds_texto,
                           d.email_avulsos,
                           d.arquivo_associado, 
                           d.cd_usuario, 
                           funcoes.get_usuario_nome(d.cd_usuario) AS usuario_cadastro,
                           d.email_avulsos, 
                           d.divisao, 
                           d.tipo_divulgacao,
						   d.fl_unico_destinatario,						   
                           d.url_link AS ds_url_link,
						   
                           0 AS qt_email_enviado,
                           0 AS qt_email_nao_enviado,
                           0 AS qt_participante,
                           0 AS qt_visualizacao,
                           0 AS qt_visualizacao_unica						   
						   
						   /*
                           (SELECT COUNT(*) AS qt_email
                              FROM projetos.envia_emails ee
                             WHERE ee.cd_divulgacao = d.cd_divulgacao
                               AND ee.fl_retornou <> 'S') AS qt_email_enviado,
                           (SELECT COUNT(*) AS qt_email
                              FROM projetos.envia_emails ee
                             WHERE ee.cd_divulgacao = d.cd_divulgacao
                               AND ee.fl_retornou = 'S') AS qt_email_nao_enviado,
                           COALESCE((SELECT COUNT(DISTINCT funcoes.cripto_re(ee.cd_empresa,ee.cd_registro_empregado,ee.seq_dependencia))
	                                   FROM projetos.envia_emails ee
	                                  WHERE ee.cd_divulgacao = d.cd_divulgacao
	                                    AND COALESCE(ee.fl_visualizado,'N') = 'S'
	                                    AND COALESCE(ee.cd_registro_empregado,0) > 0),0) AS qt_participante,
                           COALESCE((SELECT SUM(COALESCE(ee.qt_visualizado,0))
                                       FROM projetos.envia_emails ee
                                      WHERE ee.cd_divulgacao = d.cd_divulgacao),0) AS qt_visualizacao,
                           COALESCE((SELECT COUNT(*)
                                       FROM projetos.envia_emails ee
                                      WHERE ee.cd_divulgacao = d.cd_divulgacao
                                        AND COALESCE(ee.fl_visualizado,'N') = 'S'),0) AS qt_visualizacao_unica 
                           */										
                      FROM projetos.divulgacao d
                     WHERE d.cd_divulgacao = ".intval($args['cd_divulgacao'])."
                  ";
        $result = $this->db->query($qr_sql);
    }
    
    function salvar(&$result, $args=array())
    {
        if(intval($args['cd_divulgacao']) == 0)
        {
            $ob_resul = $this->db->query("SELECT nextval('projetos.divulgacao_cd_divulgacao_seq') AS cd_divulgacao");
            $ar_new = $ob_resul->row_array();
            $args['cd_divulgacao'] = intval($ar_new["cd_divulgacao"]);
        
            $qr_sql = "
                        INSERT INTO projetos.divulgacao 
                             ( 
                                cd_divulgacao, 
                                dt_agenda_email,
                                id_rementente,
								remetente,
                                assunto, 
                                conteudo, 
                                url_link,
                                email_avulsos,
                                divisao,
                                cd_usuario,
                                cd_usuario_alteracao,
                                tipo_divulgacao,
								fl_unico_destinatario
                             ) 
                        VALUES 
                             ( 
                                ".intval($args['cd_divulgacao']).", 
                                ".(trim($args['dt_agenda_email']) != "" ? "TO_TIMESTAMP('".trim($args['dt_agenda_email'])." ".(trim($args['hr_agenda_email']) != "" ? trim($args['hr_agenda_email']) : "00:00")."', 'DD/MM/YYYY HH24:MI')" : "DEFAULT").",
                                ".(trim($args['id_rementente'])   != "" ? str_escape($args['id_rementente']) : "DEFAULT").", 
                                ".(trim($args['ds_remetente'])    != "" ? str_escape($args['ds_remetente']) : "DEFAULT").", 
                                ".(trim($args['ds_assunto'])      != "" ? str_escape($args['ds_assunto']) : "DEFAULT").", 
                                ".(trim($args['ds_texto'])        != "" ? str_escape($args['ds_texto']) : "DEFAULT").",
                                ".(trim($args['ds_url_link'])     != "" ? str_escape($args['ds_url_link']) : "DEFAULT").",
                                ".(trim($args['email_avulsos'])   != "" ? str_escape($args['email_avulsos']) : "DEFAULT").",
                                ".(intval($args['cd_usuario'])    != "" ? "funcoes.get_usuario_area(".intval($args['cd_usuario']).")" : "DEFAULT").",
                                ".(intval($args['cd_usuario'])    != "" ? intval($args['cd_usuario']) : "DEFAULT").",
                                ".(intval($args['cd_usuario'])    != "" ? intval($args['cd_usuario']) : "DEFAULT").",
                                'E',
								".($args['fl_unico_destinatario'] ? "'S'" : "DEFAULT")."
                             );         
                      ";

            if(count($args['ar_divulgacao_lista']) > 0)
            {
                $qr_sql .= "
                    INSERT INTO projetos.divulgacao_lista_negra_divulgacao(cd_divulgacao, cd_lista_negra_divulgacao, cd_usuario_inclusao)
                    SELECT ".intval($args['cd_divulgacao']).", x.column1, ".intval($args['cd_usuario'])."
                      FROM (VALUES (".implode("),(", $args['ar_divulgacao_lista']).")) x;";
            }
        }
        else
        {
            $qr_sql = "
                        UPDATE projetos.divulgacao 
                           SET dt_agenda_email      = ".(trim($args['dt_agenda_email']) != "" ? "TO_TIMESTAMP('".trim($args['dt_agenda_email'])." ".(trim($args['hr_agenda_email']) != "" ? trim($args['hr_agenda_email']) : "00:00")."', 'DD/MM/YYYY HH24:MI')" : "DEFAULT").",
                               id_rementente        = ".(trim($args['id_rementente'])    != "" ? str_escape($args['id_rementente']) : "DEFAULT").", 
                               remetente            = ".(trim($args['ds_remetente'])    != "" ? str_escape($args['ds_remetente']) : "DEFAULT").", 
                               assunto              = ".(trim($args['ds_assunto'])       != "" ? str_escape($args['ds_assunto']) : "DEFAULT").", 
                               conteudo             = ".(trim($args['ds_texto'])        != "" ? str_escape($args['ds_texto']) : "DEFAULT").",
                               url_link             = ".(trim($args['ds_url_link'])     != "" ? str_escape($args['ds_url_link']) : "DEFAULT").",
                               email_avulsos        = ".(trim($args['email_avulsos'])     != "" ? str_escape($args['email_avulsos']) : "DEFAULT").",
							   fl_unico_destinatario = ".($args['fl_unico_destinatario'] ? "'S'" : "DEFAULT").",
                               dt_alteracao         = CURRENT_TIMESTAMP,
                               cd_usuario_alteracao = ".(intval($args['cd_usuario'])    != "" ? intval($args['cd_usuario']) : "DEFAULT")."
                         WHERE cd_divulgacao = ".intval($args['cd_divulgacao']).";
                      ";

            if(count($args['ar_divulgacao_lista']) > 0)
            {
                 $qr_sql .= "
                    UPDATE projetos.divulgacao_lista_negra_divulgacao
                       SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
                           dt_exclusao         = CURRENT_TIMESTAMP
                     WHERE cd_divulgacao = ".intval($args['cd_divulgacao'])."
                       AND dt_exclusao IS NULL
                       AND cd_lista_negra_divulgacao NOT IN ('".implode("','", $args['ar_divulgacao_lista'])."');
           
                    INSERT INTO projetos.divulgacao_lista_negra_divulgacao(cd_divulgacao, cd_lista_negra_divulgacao, cd_usuario_inclusao)
                    SELECT ".intval($args['cd_divulgacao']).", x.column1, ".intval($args['cd_usuario'])."
                      FROM (VALUES (".implode("),(", $args['ar_divulgacao_lista']).")) x
                     WHERE x.column1 NOT IN (SELECT a.cd_lista_negra_divulgacao
                                               FROM projetos.divulgacao_lista_negra_divulgacao a
                                              WHERE a.cd_divulgacao = ".intval($args['cd_divulgacao'])."
                                                AND a.dt_exclusao IS NULL);";
            }

            if(trim($args['fl_enviar_email']) == "S")
            {
                #echo "<PRE>".print_r($args,true)."</PRE>"; #exit;
                $qr_grupo = "";
                foreach($args['ar_divulgacao_grupo'] as $cd_grupo)
                {
                    $args['cd_grupo'] = intval($cd_grupo);
                    $qr_grupo.= $this->emails_grupo($args);
                }
                $qr_sql.= $qr_grupo;
                
                
                if(trim($args['email_avulsos']) != "")
                {
                    $qr_sql.= $this->emails_avulsos($args);
                }
            }
        }
        
        #echo "<PRE><textarea>".$qr_sql."</textarea></PRE>"; exit;
    
        $result = $this->db->query($qr_sql);
        

        $this->marca_grupo($args);
        
        
        return intval($args['cd_divulgacao']);
    }   

    #### MARCA GRUPOS SELECIONADOS ####
    private function marca_grupo($args)
    {
        #echo "<PRE>".print_r($args,true)."</PRE>"; exit;
        $qr_sql = "
                    UPDATE projetos.divulgacao_grupo_selecionado
                       SET dt_exclusao         = CURRENT_TIMESTAMP, 
                           cd_usuario_exclusao = ".intval($args['cd_usuario']) ."
                     WHERE cd_divulgacao = ".intval($args['cd_divulgacao'])."
                       AND cd_divulgacao_grupo NOT IN (".(is_array($args['ar_divulgacao_grupo']) ? implode(",", $args['ar_divulgacao_grupo']): 0).");
                  ";
        
        if(is_array($args['ar_divulgacao_grupo']))
        {
            $qr_grupo = "";
            foreach($args['ar_divulgacao_grupo'] as $cd_grupo)
            {
                $qr_grupo.= (trim($qr_grupo) != "" ? " UNION " : "")."SELECT ".intval($args['cd_divulgacao']).", ".intval($cd_grupo).", ".intval($args['cd_usuario']);
            }
            
            if(trim($qr_grupo) != "")
            {
                $qr_grupo.= "
                            EXCEPT
                            SELECT cd_divulgacao, cd_divulgacao_grupo, ".intval($args['cd_usuario'])."
                              FROM projetos.divulgacao_grupo_selecionado
                             WHERE cd_divulgacao = ".intval($args['cd_divulgacao'])."
                               AND dt_exclusao IS NULL;
                            ";
                $qr_sql.= "INSERT INTO projetos.divulgacao_grupo_selecionado(cd_divulgacao, cd_divulgacao_grupo, cd_usuario_inclusao) ".$qr_grupo;
            }
        }
        $this->db->query($qr_sql);  
        
    }
    
    #### ENVIA EMAILS ####
    private function emails_grupo($args)
    {
        #echo "<PRE>".print_r($args,true)."</PRE>"; exit;
        
        $FL_UNICO_DESTINATARIO = $args['fl_unico_destinatario'];
		
		$qr_email = "";
        
        if(intval($args['cd_divulgacao']) > 0) 
        {
            $qr_sql = "
                        SELECT cd_divulgacao_grupo,
                               qr_sql, 
                               cd_lista
                          FROM projetos.divulgacao_grupo
                         WHERE dt_exclusao IS NULL
                           AND cd_divulgacao_grupo = ".intval($args['cd_grupo'])."
                      ";
            #echo "<PRE>$qr_sql</PRE>"; exit;
            $ob_resul = $this->db->query($qr_sql);
            $ar_pub = $ob_resul->row_array();
            #echo "<PRE>".print_r($ar_pub,true)."</PRE>"; exit;
            $qr_sql_grupo = $ar_pub['qr_sql'];
            $cd_lista     = $ar_pub['cd_lista'];

            #echo "<PRE>$qr_sql_grupo</PRE>"; exit;

            if(trim($qr_sql_grupo) != "")
            {
                #### BUSCA REGISTRO DO GRUPO ####

                if(isset($args['ar_divulgacao_lista']) AND count($args['ar_divulgacao_lista']) > 0)
                {
                    $qr_sql_grupo = "
                        SELECT *
                          FROM (".trim($qr_sql_grupo).") x
                         WHERE COALESCE(TRIM(x.email), '') NOT IN (
                            SELECT TRIM(ds_lista_negra_divulgacao_email) 
                              FROM projetos.lista_negra_divulgacao_email
                             WHERE dt_exclusao IS NULL
                               AND cd_lista_negra_divulgacao IN (".implode(",", $args['ar_divulgacao_lista']).")
                         )
                          AND COALESCE(TRIM(x.email_profissional::text), '') NOT IN (
                            SELECT TRIM(ds_lista_negra_divulgacao_email) 
                              FROM projetos.lista_negra_divulgacao_email
                             WHERE dt_exclusao IS NULL
                               AND cd_lista_negra_divulgacao IN (".implode(",", $args['ar_divulgacao_lista']).")
                         )
                    ";
                }


                $ob_resul = $this->db->query($qr_sql_grupo);
                $ar_reg = $ob_resul->result_array();
                
				$id_user_conta = 1;
				$id_user       = 1;				
                foreach($ar_reg as $reg)
                {
                    #echo "<PRE>".print_r($reg,true)."</PRE>"; exit;
                    $v_texto = $args['ds_texto'];
                    $v_texto = str_replace("[EMP]",  $reg['cd_empresa'], $v_texto);
                    $v_texto = str_replace("[RE]",   $reg['cd_registro_empregado'], $v_texto);
                    $v_texto = str_replace("[SEQ]",  $reg['seq_dependencia'], $v_texto);
                    $v_texto = str_replace("[NOME]", $reg['nome'], $v_texto);
                    $v_texto = str_replace("[RE_CRIPTO]", $reg['re_cripto'], $v_texto);
					
					$assunto = $args['ds_assunto'];
					$assunto = str_replace("[NOME]", $reg['nome'], $assunto);					
					
                    #### RE ####
                    if(intval($reg['cd_registro_empregado']) == 0)
                    {
                        $emp = "DEFAULT";
                        $re  = "DEFAULT";
                        $seq = "DEFAULT";
                    }
                    else
                    {
                        $emp = (trim($reg['cd_empresa']) == ""            ? "DEFAULT" : intval($reg['cd_empresa']));
                        $re  = (trim($reg['cd_registro_empregado']) == "" ? "DEFAULT" : intval($reg['cd_registro_empregado']));
                        $seq = (trim($reg['seq_dependencia']) == ""       ? "DEFAULT" : intval($reg['seq_dependencia']));
                    }
                    
                    #### LINK ####
                    $link_email = "";
                    if(trim($args['ds_url_link']) != "")
                    {
                        $link = str_replace("[RE_CRIPTO]", $reg['re_cripto'], trim($args['ds_url_link']));
                        $link_emp = (trim($emp) == "DEFAULT" ? "NULL" : $emp);
                        $link_re  = (trim($re)  == "DEFAULT" ? "NULL" : $re);
                        $link_seq = (trim($seq) == "DEFAULT" ? "NULL" : $seq);
                        
                        $link_email = "' || (funcoes.gera_link('".$link."',".$link_emp."::NUMERIC,".$link_re."::NUMERIC,".$link_seq."::NUMERIC)) || '";
                    }
                    $v_texto = str_replace("[LINK_1]", $link_email, $v_texto);

                    #### AJUSTE NO EMAIL ####
                    $reg['email'] = trim(strtolower(str_replace("'","",$reg['email'])));
                    $reg['email_profissional'] = trim(strtolower(str_replace("'","",$reg['email_profissional'])));
                    
                    if(!preg_match("/.*@.*/", $reg['email'])) 
                    {
                        $reg['email'] = $reg['email_profissional'];
                        $reg['email_profissional'] = "";
                    }
                    $email = $reg['email'];
                    
                    $v_cc = "";
                    if(preg_match("/.*@.*/", $reg['email_profissional'])) 
                    {
                        if(trim($reg['email']) != trim($reg['email_profissional']))
                        {
                            $v_cc = $reg['email_profissional'];
                        }
                    }           

                    $ds_remetente = "";
                    #### REMETENTE (DE) ####
                    if(trim($args['ds_remetente']) != "")
                    {
                        $ds_remetente = trim($args['ds_remetente']);
                    }
                    elseif($emp == 7)
                    {
                        $ds_remetente = "Senge Previdencia";
                    }               
                    elseif($emp == 8)
                    {
                        $ds_remetente = "SINPRORS Previdencia";
                    }
                    elseif($emp == 10)
                    {
                        $ds_remetente = "SINPRORS Previdencia";
                    }
                    elseif($emp == 19)
                    {
                        $ds_remetente = "Familia Previdencia";
                    }
                    else
                    {
                        $ds_remetente = "Fundacao Familia";
                    }
                    
					if ($FL_UNICO_DESTINATARIO)
					{	
						#### NAO REPETE ENVIO PARA O MESMO ENDERECO DE EMAIL - DESTINO ####
						$email = (trim($email) == "" ? "" : ((in_array(trim($email), $this->AR_FILTRO_EMAIL)) ? "" : trim($email)));
						$v_cc  = (trim($v_cc) == "" ? "" : ((in_array(trim($v_cc), $this->AR_FILTRO_EMAIL)) ? "" : trim($v_cc)));
					}
					
					#### DIVIDE NOS 10 USUARIOS (e1@..., e2@..., etc) DE ENVIO DE EMAILS ####
					if($id_user_conta > 100)
					{
						$id_user++;
						$id_user = (($id_user > 10) ? 1 : $id_user);
						
						$id_user_conta = 1;
					}
					$id_user_conta++;
					
                    $qr_email.= "
                                 INSERT INTO projetos.envia_emails 
                                      ( 
                                        formato,
                                        tp_email,
                                        cd_divulgacao,
                                        id_user_email_envio,
                                        dt_schedule_email, 
                                        de, 
                                        para,
                                        cc, 
                                        cco,
                                        assunto,
                                        texto,
                                        cd_empresa,
                                        cd_registro_empregado,
                                        seq_dependencia,
                                        div_solicitante,
                                        cd_usuario,
										fl_unico_destinatario             
                                      )     
                                 VALUES
                                      (
                                        'HTML',
                                        ".(trim($args['id_rementente']) == "" ? "DEFAULT" : "'".trim($args['id_rementente'])."'" ).",
                                        ".intval($args['cd_divulgacao']).",
                                        ".intval($id_user).",
                                        ".(trim($args['dt_agenda_email']) != "" ? "TO_TIMESTAMP('".trim($args['dt_agenda_email'])." ".(trim($args['hr_agenda_email']) != "" ? trim($args['hr_agenda_email']) : "00:00")."', 'DD/MM/YYYY HH24:MI')" : "DEFAULT").", --AGENDAMENTO DO ENVIO
                                        ".(trim($ds_remetente) == "" ? "DEFAULT" : "'".trim($ds_remetente)."'" ).",
                                        '".trim($email)."',
                                        '".trim($v_cc)."',
                                        '',
                                        ".(trim($assunto) == "" ? "''" : "'".trim($assunto)."'" ).",
                                        '".$v_texto."',
                                        ".$emp.",
                                        ".$re.", 
                                        ".$seq.",
                                        funcoes.get_usuario_area(".intval($args['cd_usuario'])."),
                                        ".intval($args['cd_usuario']).",
										".($FL_UNICO_DESTINATARIO ? "'S'" : "DEFAULT")."
                                      );
                                ";
                    if ($FL_UNICO_DESTINATARIO)
					{	
						#### INCLUI PARA FILTRAR DESTINATARIOS ####
						
						if(trim($email) != "")
						{
							$this->AR_FILTRO_EMAIL[] = trim($email);
						}
						
						if(trim($v_cc) != "")
						{
							$this->AR_FILTRO_EMAIL[] = trim($v_cc);
						}										
					}            
                    #echo "<PRE>$qr_email</PRE>"; exit;
                }
            }
        }
		
        return $qr_email;
    }   
    
    #### ENVIA EMAIL AVULSO ####
    private function emails_avulsos($args) 
    {
        $ar_email = explode(";",$args['email_avulsos']);
        $qr_sql = "";
        foreach($ar_email as $email)
        {
            if(preg_match("/.*@.*/", $email)) 
            {
                $v_texto = $args['ds_texto'];
                $v_texto = str_replace("[EMP]",       "", $v_texto);
                $v_texto = str_replace("[RE]",        "", $v_texto);
                $v_texto = str_replace("[SEQ]",       "", $v_texto);
                $v_texto = str_replace("[NOME]",      "", $v_texto);
                $v_texto = str_replace("[RE_CRIPTO]", "", $v_texto);
				
				$assunto = $args['ds_assunto'];
				$assunto = str_replace("[NOME]", "", $assunto);
                
                #### LINK ####
                $link_email = "";
                if(trim($args['ds_url_link']) != "")
                {
                    $link = str_replace("[RE_CRIPTO]", md5(uniqid(rand(), true)), trim($args['ds_url_link']));
                    $link_email = "' || (funcoes.gera_link('".$link."',NULL::NUMERIC,NULL::NUMERIC,NULL::NUMERIC)) || '";
                }
                $v_texto = str_replace("[LINK_1]", $link_email, $v_texto);              
                
                $qr_sql.= "
                            INSERT INTO projetos.envia_emails
                                 (
                                    formato,
                                    tp_email,                                
                                    cd_divulgacao,
                                    dt_schedule_email,
                                    de,
                                    para,
                                    cc,
                                    cco,
                                    assunto,
                                    texto,
                                    div_solicitante,
                                    cd_usuario                       
                                 )
                            VALUES
                                 (
                                    'HTML',
                                    ".(trim($args['id_rementente']) == "" ? "DEFAULT" : "'".trim($args['id_rementente'])."'" ).",                                 
                                    ".intval(intval($args['cd_divulgacao'])).",
                                    ".(trim($args['dt_agenda_email']) != "" ? "TO_TIMESTAMP('".trim($args['dt_agenda_email'])." ".(trim($args['hr_agenda_email']) != "" ? trim($args['hr_agenda_email']) : "00:00")."', 'DD/MM/YYYY HH24:MI')" : "DEFAULT").", --AGENDAMENTO DO ENVIO
                                    ".(trim($args['ds_remetente']) == "" ? "DEFAULT" : "'".trim($args['ds_remetente'])."'" ).",
                                    LOWER(funcoes.remove_acento('".trim($email)."')),
                                    '',
                                    '',
                                    ".(trim($assunto) == "" ? "''" : "'".trim($assunto)."'" ).",
                                    '".$v_texto."',
                                    funcoes.get_usuario_area(".intval($args['cd_usuario'])."),
                                    ".intval($args['cd_usuario'])."
                                 );
                          ";
            }
        }
        
        #echo "<PRE>$qr_sql</PRE>"; exit;
        
        return $qr_sql;
    }   
    
    #### DADOS GRAFICOS ####
    function tecnologiaDeviceType(&$result, $args=array())
    {
        $qr_sql = "
                SELECT COALESCE(INITCAP(eet.device_type),'Não identificado') AS ds_item, 
                       COUNT(*) AS qt_item
                  FROM projetos.envia_emails ee
                  JOIN projetos.envia_emails_tracker eet
                    ON eet.cd_email = ee.cd_email
                 WHERE ee.cd_divulgacao = ".intval($args['cd_divulgacao'])."
                     ".(((trim($args['dt_envio_ini']) != "") AND (trim($args['dt_envio_fim']) != "")) ? " AND DATE_TRUNC('day', ee.dt_email_enviado) BETWEEN TO_DATE('".$args['dt_envio_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_envio_fim']."', 'DD/MM/YYYY')" : "")."
                 GROUP BY ds_item
                 ORDER BY qt_item DESC
                ";
        #echo "<pre style='text-align:center;'>$qr_sql</pre>";

        $result = $this->db->query($qr_sql);
    }

    function tecnologiaDeviceName(&$result, $args=array())
    {
        $qr_sql = "
                SELECT COALESCE(eet.device_mobile,'Não identificado') AS ds_item, 
                       COUNT(*) AS qt_item
                  FROM projetos.envia_emails ee
                  JOIN projetos.envia_emails_tracker eet
                    ON eet.cd_email = ee.cd_email
                 WHERE ee.cd_divulgacao = ".intval($args['cd_divulgacao'])."
                   AND COALESCE(eet.device_type,'') IN ('phone','tablet')
                     ".(((trim($args['dt_envio_ini']) != "") AND (trim($args['dt_envio_fim']) != "")) ? " AND DATE_TRUNC('day', ee.dt_email_enviado) BETWEEN TO_DATE('".$args['dt_envio_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_envio_fim']."', 'DD/MM/YYYY')" : "")."
                 GROUP BY ds_item
                 ORDER BY qt_item DESC
                ";
        #echo "<pre style='text-align:center;'>$qr_sql</pre>";

        $result = $this->db->query($qr_sql);
    }   
    
    function tecnologiaOSFamily(&$result, $args=array())
    {
        $qr_sql = "
                SELECT COALESCE(eet.ug_os_family,'Não identificado') AS ds_item, 
                       COUNT(*) AS qt_item
                  FROM projetos.envia_emails ee
                  JOIN projetos.envia_emails_tracker eet
                    ON eet.cd_email = ee.cd_email
                 WHERE ee.cd_divulgacao = ".intval($args['cd_divulgacao'])."
                     ".(((trim($args['dt_envio_ini']) != "") AND (trim($args['dt_envio_fim']) != "")) ? " AND DATE_TRUNC('day', ee.dt_email_enviado) BETWEEN TO_DATE('".$args['dt_envio_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_envio_fim']."', 'DD/MM/YYYY')" : "")."
                 GROUP BY ds_item
                 ORDER BY qt_item DESC
                ";
        #echo "<pre style='text-align:center;'>$qr_sql</pre>";

        $result = $this->db->query($qr_sql);
    }

    function tecnologiaOSName(&$result, $args=array())
    {
        $qr_sql = "
                SELECT COALESCE(eet.ug_os_name,'Não identificado') AS ds_item, 
                       COUNT(*) AS qt_item
                  FROM projetos.envia_emails ee
                  JOIN projetos.envia_emails_tracker eet
                    ON eet.cd_email = ee.cd_email
                 WHERE ee.cd_divulgacao = ".intval($args['cd_divulgacao'])."
                     ".(((trim($args['dt_envio_ini']) != "") AND (trim($args['dt_envio_fim']) != "")) ? " AND DATE_TRUNC('day', ee.dt_email_enviado) BETWEEN TO_DATE('".$args['dt_envio_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_envio_fim']."', 'DD/MM/YYYY')" : "")."
                 GROUP BY ds_item
                 ORDER BY qt_item DESC
                ";
        #echo "<pre style='text-align:center;'>$qr_sql</pre>";

        $result = $this->db->query($qr_sql);
    }

    function tecnologiaUATipo(&$result, $args=array())
    {
        $qr_sql = "
                SELECT COALESCE(eet.ug_typ,'Não identificado') AS ds_item, 
                       COUNT(*) AS qt_item
                  FROM projetos.envia_emails ee
                  JOIN projetos.envia_emails_tracker eet
                    ON eet.cd_email = ee.cd_email
                 WHERE ee.cd_divulgacao = ".intval($args['cd_divulgacao'])."
                     ".(((trim($args['dt_envio_ini']) != "") AND (trim($args['dt_envio_fim']) != "")) ? " AND DATE_TRUNC('day', ee.dt_email_enviado) BETWEEN TO_DATE('".$args['dt_envio_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_envio_fim']."', 'DD/MM/YYYY')" : "")."
                 GROUP BY ds_item
                 ORDER BY qt_item DESC
                ";
        #echo "<pre style='text-align:center;'>$qr_sql</pre>";

        $result = $this->db->query($qr_sql);
    }   
    
    function tecnologiaUAFamily(&$result, $args=array())
    {
        $qr_sql = "
                SELECT COALESCE(eet.ug_ua_family,'Não identificado') AS ds_item, 
                       COUNT(*) AS qt_item
                  FROM projetos.envia_emails ee
                  JOIN projetos.envia_emails_tracker eet
                    ON eet.cd_email = ee.cd_email
                 WHERE ee.cd_divulgacao = ".intval($args['cd_divulgacao'])."
                     ".(((trim($args['dt_envio_ini']) != "") AND (trim($args['dt_envio_fim']) != "")) ? " AND DATE_TRUNC('day', ee.dt_email_enviado) BETWEEN TO_DATE('".$args['dt_envio_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_envio_fim']."', 'DD/MM/YYYY')" : "")."
                 GROUP BY ds_item
                 ORDER BY qt_item DESC
                ";
        #echo "<pre style='text-align:center;'>$qr_sql</pre>";

        $result = $this->db->query($qr_sql);
    }   
    
    function participanteEmpresa(&$result, $args=array())
    {
        $qr_sql = "
                SELECT COALESCE(p.sigla,'Não identificado') AS ds_item, 
                       COUNT(*) AS qt_item
                  FROM projetos.envia_emails ee
                  JOIN projetos.envia_emails_tracker eet
                    ON eet.cd_email = ee.cd_email
                  LEFT JOIN public.patrocinadoras p
                    ON p.cd_empresa = eet.part_cd_empresa
                 WHERE ee.cd_divulgacao = ".intval($args['cd_divulgacao'])."
                    
                    ".(((trim($args['dt_envio_ini']) != "") AND (trim($args['dt_envio_fim']) != "")) ? " AND DATE_TRUNC('day', ee.dt_email_enviado) BETWEEN TO_DATE('".$args['dt_envio_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_envio_fim']."', 'DD/MM/YYYY')" : "")."
                    ".(is_array($args["ar_empresa"]) ? $this->whereEmpresa($args["ar_empresa"]) : "")." 
                    ".(is_array($args["ar_plano"]) ? $this->wherePlano($args["ar_plano"]) : "")." 
                    ".(is_array($args["ar_tipo"]) ? $this->whereTipo($args["ar_tipo"]) : "")." 
                    ".(is_array($args["ar_tempo_plano"]) ? $this->whereTempoPlano($args["ar_tempo_plano"]) : "")." 
                    ".(is_array($args["ar_idade"]) ? $this->whereIdade($args["ar_idade"]) : "")." 
                    ".(is_array($args["ar_renda"]) ? $this->whereRenda($args["ar_renda"]) : "")." 
                    ".(is_array($args["ar_uf"]) ? $this->whereUF($args["ar_uf"]) : "")." 
                    ".(trim($args["cd_sexo"]) != "" ? "AND eet.part_sexo = '".trim($args["cd_sexo"])."'" : "")." 
                    ".(trim($args["cd_senha"]) != "" ? "AND COALESCE(eet.part_tipo_senha, 'SEM') = '".trim($args["cd_senha"])."'" : "")." 
                    
                   AND COALESCE(eet.part_cd_registro_empregado,0) > 0
                   AND eet.cd_envia_emails_tracker = (SELECT MIN(eet1.cd_envia_emails_tracker)
                                                        FROM projetos.envia_emails_tracker eet1
                                                        JOIN projetos.envia_emails ee1
                                                          ON ee1.cd_email = eet1.cd_email
                                                       WHERE ee1.cd_divulgacao               = ee.cd_divulgacao
                                                         AND eet1.part_cd_empresa            = eet.part_cd_empresa
                                                         AND eet1.part_cd_registro_empregado = eet.part_cd_registro_empregado
                                                         AND eet1.part_seq_dependencia       = eet.part_seq_dependencia)                     
                     
                 GROUP BY ds_item
                 ORDER BY qt_item DESC
                ";
        #echo "<pre style='text-align:center;'>$qr_sql</pre>";

        $result = $this->db->query($qr_sql);
    }   
    
    function participantePlano(&$result, $args=array())
    {
        $qr_sql = "
                SELECT COALESCE(p.descricao,'Não identificado') AS ds_item, 
                       COUNT(*) AS qt_item
                  FROM projetos.envia_emails ee
                  JOIN projetos.envia_emails_tracker eet
                    ON eet.cd_email = ee.cd_email
                  LEFT JOIN public.planos p
                    ON p.cd_plano = eet.part_cd_plano
                 WHERE ee.cd_divulgacao = ".intval($args['cd_divulgacao'])."
                    ".(((trim($args['dt_envio_ini']) != "") AND (trim($args['dt_envio_fim']) != "")) ? " AND DATE_TRUNC('day', ee.dt_email_enviado) BETWEEN TO_DATE('".$args['dt_envio_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_envio_fim']."', 'DD/MM/YYYY')" : "")."
                    ".(is_array($args["ar_empresa"]) ? $this->whereEmpresa($args["ar_empresa"]) : "")." 
                    ".(is_array($args["ar_plano"]) ? $this->wherePlano($args["ar_plano"]) : "")." 
                    ".(is_array($args["ar_tipo"]) ? $this->whereTipo($args["ar_tipo"]) : "")." 
                    ".(is_array($args["ar_tempo_plano"]) ? $this->whereTempoPlano($args["ar_tempo_plano"]) : "")." 
                    ".(is_array($args["ar_idade"]) ? $this->whereIdade($args["ar_idade"]) : "")." 
                    ".(is_array($args["ar_renda"]) ? $this->whereRenda($args["ar_renda"]) : "")." 
                    ".(is_array($args["ar_uf"]) ? $this->whereUF($args["ar_uf"]) : "")." 
                    ".(trim($args["cd_sexo"]) != "" ? "AND eet.part_sexo = '".trim($args["cd_sexo"])."'" : "")." 
                    ".(trim($args["cd_senha"]) != "" ? "AND COALESCE(eet.part_tipo_senha, 'SEM') = '".trim($args["cd_senha"])."'" : "")." 
                   AND COALESCE(eet.part_cd_registro_empregado,0) > 0
                   AND eet.cd_envia_emails_tracker = (SELECT MIN(eet1.cd_envia_emails_tracker)
                                                        FROM projetos.envia_emails_tracker eet1
                                                        JOIN projetos.envia_emails ee1
                                                          ON ee1.cd_email = eet1.cd_email
                                                       WHERE ee1.cd_divulgacao               = ee.cd_divulgacao
                                                         AND eet1.part_cd_empresa            = eet.part_cd_empresa
                                                         AND eet1.part_cd_registro_empregado = eet.part_cd_registro_empregado
                                                         AND eet1.part_seq_dependencia       = eet.part_seq_dependencia)                     
                     
                 GROUP BY ds_item
                 ORDER BY qt_item DESC
                ";
        #echo "<pre style='text-align:center;'>$qr_sql</pre>";

        $result = $this->db->query($qr_sql);
    }   

    function participanteTempoPlano(&$result, $args=array())
    {
        $qr_sql = "
                SELECT CASE WHEN COALESCE(eet.part_qt_ano_plano,-1) BETWEEN 00 AND 10 THEN '00 à 10'
                            WHEN COALESCE(eet.part_qt_ano_plano,-1) BETWEEN 11 AND 20 THEN '11 à 20'
                            WHEN COALESCE(eet.part_qt_ano_plano,-1) BETWEEN 21 AND 30 THEN '21 à 30'
                            WHEN COALESCE(eet.part_qt_ano_plano,-1) BETWEEN 31 AND 40 THEN '31 à 40'
                            WHEN COALESCE(eet.part_qt_ano_plano,-1) BETWEEN 41 AND 50 THEN '41 à 50'
                            WHEN COALESCE(eet.part_qt_ano_plano,-1) BETWEEN 51 AND 60 THEN '51 à 60'
                            WHEN COALESCE(eet.part_qt_ano_plano,-1) BETWEEN 61 AND 70 THEN '61 à 70'
                            WHEN COALESCE(eet.part_qt_ano_plano,-1) BETWEEN 71 AND 80 THEN '71 à 80'
                            WHEN COALESCE(eet.part_qt_ano_plano,-1) BETWEEN 81 AND 90 THEN '81 à 90'
                            WHEN COALESCE(eet.part_qt_ano_plano,-1) BETWEEN 91 AND 100 THEN '91 à 100'
                            WHEN COALESCE(eet.part_qt_ano_plano,-1) > 100 THEN '+ de 100'
                            ELSE 'Não identificado'
                       END AS ds_item, 
                       COUNT(*) AS qt_item
                  FROM projetos.envia_emails ee
                  JOIN projetos.envia_emails_tracker eet
                    ON eet.cd_email = ee.cd_email
                 WHERE ee.cd_divulgacao = ".intval($args['cd_divulgacao'])."
                    ".(((trim($args['dt_envio_ini']) != "") AND (trim($args['dt_envio_fim']) != "")) ? " AND DATE_TRUNC('day', ee.dt_email_enviado) BETWEEN TO_DATE('".$args['dt_envio_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_envio_fim']."', 'DD/MM/YYYY')" : "")."
                    ".(is_array($args["ar_empresa"]) ? $this->whereEmpresa($args["ar_empresa"]) : "")." 
                    ".(is_array($args["ar_plano"]) ? $this->wherePlano($args["ar_plano"]) : "")." 
                    ".(is_array($args["ar_tipo"]) ? $this->whereTipo($args["ar_tipo"]) : "")." 
                    ".(is_array($args["ar_tempo_plano"]) ? $this->whereTempoPlano($args["ar_tempo_plano"]) : "")." 
                    ".(is_array($args["ar_idade"]) ? $this->whereIdade($args["ar_idade"]) : "")." 
                    ".(is_array($args["ar_renda"]) ? $this->whereRenda($args["ar_renda"]) : "")." 
                    ".(is_array($args["ar_uf"]) ? $this->whereUF($args["ar_uf"]) : "")." 
                    ".(trim($args["cd_sexo"]) != "" ? "AND eet.part_sexo = '".trim($args["cd_sexo"])."'" : "")." 
                    ".(trim($args["cd_senha"]) != "" ? "AND COALESCE(eet.part_tipo_senha, 'SEM') = '".trim($args["cd_senha"])."'" : "")." 
                   AND COALESCE(eet.part_cd_registro_empregado,0) > 0
                   AND eet.cd_envia_emails_tracker = (SELECT MIN(eet1.cd_envia_emails_tracker)
                                                        FROM projetos.envia_emails_tracker eet1
                                                        JOIN projetos.envia_emails ee1
                                                          ON ee1.cd_email = eet1.cd_email
                                                       WHERE ee1.cd_divulgacao               = ee.cd_divulgacao
                                                         AND eet1.part_cd_empresa            = eet.part_cd_empresa
                                                         AND eet1.part_cd_registro_empregado = eet.part_cd_registro_empregado
                                                         AND eet1.part_seq_dependencia       = eet.part_seq_dependencia)                     
                     
                 GROUP BY ds_item
                 ORDER BY qt_item DESC
                ";
        #echo "<pre style='text-align:center;'>$qr_sql</pre>";

        $result = $this->db->query($qr_sql);
    }   
    
    function participanteTipo(&$result, $args=array())
    {
        $qr_sql = "
                SELECT COALESCE(eet.part_tipo,'Não identificado') AS ds_item, 
                       COUNT(*) AS qt_item
                  FROM projetos.envia_emails ee
                  JOIN projetos.envia_emails_tracker eet
                    ON eet.cd_email = ee.cd_email
                 WHERE ee.cd_divulgacao = ".intval($args['cd_divulgacao'])."
                    ".(((trim($args['dt_envio_ini']) != "") AND (trim($args['dt_envio_fim']) != "")) ? " AND DATE_TRUNC('day', ee.dt_email_enviado) BETWEEN TO_DATE('".$args['dt_envio_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_envio_fim']."', 'DD/MM/YYYY')" : "")."
                    ".(is_array($args["ar_empresa"]) ? $this->whereEmpresa($args["ar_empresa"]) : "")." 
                    ".(is_array($args["ar_plano"]) ? $this->wherePlano($args["ar_plano"]) : "")." 
                    ".(is_array($args["ar_tipo"]) ? $this->whereTipo($args["ar_tipo"]) : "")." 
                    ".(is_array($args["ar_tempo_plano"]) ? $this->whereTempoPlano($args["ar_tempo_plano"]) : "")." 
                    ".(is_array($args["ar_idade"]) ? $this->whereIdade($args["ar_idade"]) : "")." 
                    ".(is_array($args["ar_renda"]) ? $this->whereRenda($args["ar_renda"]) : "")." 
                    ".(is_array($args["ar_uf"]) ? $this->whereUF($args["ar_uf"]) : "")." 
                    ".(trim($args["cd_sexo"]) != "" ? "AND eet.part_sexo = '".trim($args["cd_sexo"])."'" : "")." 
                    ".(trim($args["cd_senha"]) != "" ? "AND COALESCE(eet.part_tipo_senha, 'SEM') = '".trim($args["cd_senha"])."'" : "")." 
                   AND COALESCE(eet.part_cd_registro_empregado,0) > 0
                   AND eet.cd_envia_emails_tracker = (SELECT MIN(eet1.cd_envia_emails_tracker)
                                                        FROM projetos.envia_emails_tracker eet1
                                                        JOIN projetos.envia_emails ee1
                                                          ON ee1.cd_email = eet1.cd_email
                                                       WHERE ee1.cd_divulgacao               = ee.cd_divulgacao
                                                         AND eet1.part_cd_empresa            = eet.part_cd_empresa
                                                         AND eet1.part_cd_registro_empregado = eet.part_cd_registro_empregado
                                                         AND eet1.part_seq_dependencia       = eet.part_seq_dependencia)                     
                     
                 GROUP BY ds_item
                 ORDER BY qt_item DESC
                ";
        #echo "<pre style='text-align:center;'>$qr_sql</pre>";

        $result = $this->db->query($qr_sql);
    }

    function participanteSenha(&$result, $args=array())
    {
        $qr_sql = "
                SELECT COALESCE(eet.part_tipo_senha,'Não identificado') AS ds_item, 
                       COUNT(*) AS qt_item
                  FROM projetos.envia_emails ee
                  JOIN projetos.envia_emails_tracker eet
                    ON eet.cd_email = ee.cd_email
                 WHERE ee.cd_divulgacao = ".intval($args['cd_divulgacao'])."
                    ".(((trim($args['dt_envio_ini']) != "") AND (trim($args['dt_envio_fim']) != "")) ? " AND DATE_TRUNC('day', ee.dt_email_enviado) BETWEEN TO_DATE('".$args['dt_envio_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_envio_fim']."', 'DD/MM/YYYY')" : "")."
                    ".(is_array($args["ar_empresa"]) ? $this->whereEmpresa($args["ar_empresa"]) : "")." 
                    ".(is_array($args["ar_plano"]) ? $this->wherePlano($args["ar_plano"]) : "")." 
                    ".(is_array($args["ar_tipo"]) ? $this->whereTipo($args["ar_tipo"]) : "")." 
                    ".(is_array($args["ar_tempo_plano"]) ? $this->whereTempoPlano($args["ar_tempo_plano"]) : "")." 
                    ".(is_array($args["ar_idade"]) ? $this->whereIdade($args["ar_idade"]) : "")." 
                    ".(is_array($args["ar_renda"]) ? $this->whereRenda($args["ar_renda"]) : "")." 
                    ".(is_array($args["ar_uf"]) ? $this->whereUF($args["ar_uf"]) : "")." 
                    ".(trim($args["cd_sexo"]) != "" ? "AND eet.part_sexo = '".trim($args["cd_sexo"])."'" : "")." 
                    ".(trim($args["cd_senha"]) != "" ? "AND COALESCE(eet.part_tipo_senha, 'SEM') = '".trim($args["cd_senha"])."'" : "")." 
                   AND COALESCE(eet.part_cd_registro_empregado,0) > 0
                   AND eet.cd_envia_emails_tracker = (SELECT MIN(eet1.cd_envia_emails_tracker)
                                                        FROM projetos.envia_emails_tracker eet1
                                                        JOIN projetos.envia_emails ee1
                                                          ON ee1.cd_email = eet1.cd_email
                                                       WHERE ee1.cd_divulgacao               = ee.cd_divulgacao
                                                         AND eet1.part_cd_empresa            = eet.part_cd_empresa
                                                         AND eet1.part_cd_registro_empregado = eet.part_cd_registro_empregado
                                                         AND eet1.part_seq_dependencia       = eet.part_seq_dependencia)                     
                     
                 GROUP BY ds_item
                 ORDER BY qt_item DESC
                ";
        #echo "<pre style='text-align:center;'>$qr_sql</pre>";

        $result = $this->db->query($qr_sql);
    }   
    
    function participanteSexo(&$result, $args=array())
    {
        $qr_sql = "
                SELECT CASE WHEN eet.part_sexo = 'M' THEN 'Masculino'
                            WHEN eet.part_sexo = 'F' THEN 'Feminino'
                            ELSE 'Não identificado'
                       END AS ds_item, 
                       COUNT(*) AS qt_item
                  FROM projetos.envia_emails ee
                  JOIN projetos.envia_emails_tracker eet
                    ON eet.cd_email = ee.cd_email
                 WHERE ee.cd_divulgacao = ".intval($args['cd_divulgacao'])."
                    ".(((trim($args['dt_envio_ini']) != "") AND (trim($args['dt_envio_fim']) != "")) ? " AND DATE_TRUNC('day', ee.dt_email_enviado) BETWEEN TO_DATE('".$args['dt_envio_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_envio_fim']."', 'DD/MM/YYYY')" : "")."
                    ".(is_array($args["ar_empresa"]) ? $this->whereEmpresa($args["ar_empresa"]) : "")." 
                    ".(is_array($args["ar_plano"]) ? $this->wherePlano($args["ar_plano"]) : "")." 
                    ".(is_array($args["ar_tipo"]) ? $this->whereTipo($args["ar_tipo"]) : "")." 
                    ".(is_array($args["ar_tempo_plano"]) ? $this->whereTempoPlano($args["ar_tempo_plano"]) : "")." 
                    ".(is_array($args["ar_idade"]) ? $this->whereIdade($args["ar_idade"]) : "")." 
                    ".(is_array($args["ar_renda"]) ? $this->whereRenda($args["ar_renda"]) : "")." 
                    ".(is_array($args["ar_uf"]) ? $this->whereUF($args["ar_uf"]) : "")." 
                    ".(trim($args["cd_sexo"]) != "" ? "AND eet.part_sexo = '".trim($args["cd_sexo"])."'" : "")." 
                    ".(trim($args["cd_senha"]) != "" ? "AND COALESCE(eet.part_tipo_senha, 'SEM') = '".trim($args["cd_senha"])."'" : "")." 
                   AND COALESCE(eet.part_cd_registro_empregado,0) > 0
                   AND eet.cd_envia_emails_tracker = (SELECT MIN(eet1.cd_envia_emails_tracker)
                                                        FROM projetos.envia_emails_tracker eet1
                                                        JOIN projetos.envia_emails ee1
                                                          ON ee1.cd_email = eet1.cd_email
                                                       WHERE ee1.cd_divulgacao               = ee.cd_divulgacao
                                                         AND eet1.part_cd_empresa            = eet.part_cd_empresa
                                                         AND eet1.part_cd_registro_empregado = eet.part_cd_registro_empregado
                                                         AND eet1.part_seq_dependencia       = eet.part_seq_dependencia)                     
                     
                 GROUP BY ds_item
                 ORDER BY qt_item DESC
                ";
        #echo "<pre style='text-align:center;'>$qr_sql</pre>";

        $result = $this->db->query($qr_sql);
    }   
    
    function participanteIdade(&$result, $args=array())
    {
        $qr_sql = "
                SELECT CASE WHEN COALESCE(eet.part_nr_idade,-1) BETWEEN 00 AND 10 THEN '00 à 10'
                            WHEN COALESCE(eet.part_nr_idade,-1) BETWEEN 11 AND 20 THEN '11 à 20'
                            WHEN COALESCE(eet.part_nr_idade,-1) BETWEEN 21 AND 30 THEN '21 à 30'
                            WHEN COALESCE(eet.part_nr_idade,-1) BETWEEN 31 AND 40 THEN '31 à 40'
                            WHEN COALESCE(eet.part_nr_idade,-1) BETWEEN 41 AND 50 THEN '41 à 50'
                            WHEN COALESCE(eet.part_nr_idade,-1) BETWEEN 51 AND 60 THEN '51 à 60'
                            WHEN COALESCE(eet.part_nr_idade,-1) BETWEEN 61 AND 70 THEN '61 à 70'
                            WHEN COALESCE(eet.part_nr_idade,-1) BETWEEN 71 AND 80 THEN '71 à 80'
                            WHEN COALESCE(eet.part_nr_idade,-1) BETWEEN 81 AND 90 THEN '81 à 90'
                            WHEN COALESCE(eet.part_nr_idade,-1) BETWEEN 91 AND 100 THEN '91 à 100'
                            WHEN COALESCE(eet.part_nr_idade,-1) > 100 THEN '+ de 100'
                            ELSE 'Não identificado'
                       END AS ds_item, 
                       COUNT(*) AS qt_item
                  FROM projetos.envia_emails ee
                  JOIN projetos.envia_emails_tracker eet
                    ON eet.cd_email = ee.cd_email
                 WHERE ee.cd_divulgacao = ".intval($args['cd_divulgacao'])."
                    ".(((trim($args['dt_envio_ini']) != "") AND (trim($args['dt_envio_fim']) != "")) ? " AND DATE_TRUNC('day', ee.dt_email_enviado) BETWEEN TO_DATE('".$args['dt_envio_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_envio_fim']."', 'DD/MM/YYYY')" : "")."
                    ".(is_array($args["ar_empresa"]) ? $this->whereEmpresa($args["ar_empresa"]) : "")." 
                    ".(is_array($args["ar_plano"]) ? $this->wherePlano($args["ar_plano"]) : "")." 
                    ".(is_array($args["ar_tipo"]) ? $this->whereTipo($args["ar_tipo"]) : "")." 
                    ".(is_array($args["ar_tempo_plano"]) ? $this->whereTempoPlano($args["ar_tempo_plano"]) : "")." 
                    ".(is_array($args["ar_idade"]) ? $this->whereIdade($args["ar_idade"]) : "")." 
                    ".(is_array($args["ar_renda"]) ? $this->whereRenda($args["ar_renda"]) : "")." 
                    ".(is_array($args["ar_uf"]) ? $this->whereUF($args["ar_uf"]) : "")." 
                    ".(trim($args["cd_sexo"]) != "" ? "AND eet.part_sexo = '".trim($args["cd_sexo"])."'" : "")." 
                    ".(trim($args["cd_senha"]) != "" ? "AND COALESCE(eet.part_tipo_senha, 'SEM') = '".trim($args["cd_senha"])."'" : "")." 
                   AND COALESCE(eet.part_cd_registro_empregado,0) > 0
                   AND eet.cd_envia_emails_tracker = (SELECT MIN(eet1.cd_envia_emails_tracker)
                                                        FROM projetos.envia_emails_tracker eet1
                                                        JOIN projetos.envia_emails ee1
                                                          ON ee1.cd_email = eet1.cd_email
                                                       WHERE ee1.cd_divulgacao               = ee.cd_divulgacao
                                                         AND eet1.part_cd_empresa            = eet.part_cd_empresa
                                                         AND eet1.part_cd_registro_empregado = eet.part_cd_registro_empregado
                                                         AND eet1.part_seq_dependencia       = eet.part_seq_dependencia)                     
                     
                 GROUP BY ds_item
                 ORDER BY qt_item DESC
                ";
        #echo "<pre style='text-align:center;'>$qr_sql</pre>";

        $result = $this->db->query($qr_sql);
    }   

    function participanteRenda(&$result, $args=array())
    {
        $qr_sql = "
                SELECT CASE WHEN COALESCE(eet.part_vl_renda,-1) = 0 THEN 'Sem'
                            WHEN COALESCE(eet.part_vl_renda,-1) BETWEEN 0000.01 AND 1000 THEN '0,01 à 1.000,00'
                            WHEN COALESCE(eet.part_vl_renda,-1) BETWEEN 1000.01 AND 2500 THEN '1.000,01 à 2.500,00'
                            WHEN COALESCE(eet.part_vl_renda,-1) BETWEEN 2500.01 AND 5000 THEN '2.500,01 à 5.000,00'
                            WHEN COALESCE(eet.part_vl_renda,-1) BETWEEN 5000.01 AND 7500 THEN '6.000,01 à 7.500,00'
                            WHEN COALESCE(eet.part_vl_renda,-1) BETWEEN 7500.01 AND 10000 THEN '7.500,01 à 10.000,00'
                            WHEN COALESCE(eet.part_vl_renda,-1) BETWEEN 10000.01 AND 15000 THEN '10.000,01 à 15.000,00'
                            WHEN COALESCE(eet.part_vl_renda,-1) BETWEEN 15000.01 AND 20000 THEN '15.000,01 à 20.000,00'
                            WHEN COALESCE(eet.part_vl_renda,-1) BETWEEN 20000.01 AND 30000 THEN '20.000,01 à 30.000,00'
                            WHEN COALESCE(eet.part_vl_renda,-1) > 30000.01 THEN '+ de 30.000,00'
                            ELSE 'Não identificado'
                       END AS ds_item, 
                       COUNT(*) AS qt_item
                  FROM projetos.envia_emails ee
                  JOIN projetos.envia_emails_tracker eet
                    ON eet.cd_email = ee.cd_email
                 WHERE ee.cd_divulgacao = ".intval($args['cd_divulgacao'])."
                    ".(((trim($args['dt_envio_ini']) != "") AND (trim($args['dt_envio_fim']) != "")) ? " AND DATE_TRUNC('day', ee.dt_email_enviado) BETWEEN TO_DATE('".$args['dt_envio_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_envio_fim']."', 'DD/MM/YYYY')" : "")."
                    ".(is_array($args["ar_empresa"]) ? $this->whereEmpresa($args["ar_empresa"]) : "")." 
                    ".(is_array($args["ar_plano"]) ? $this->wherePlano($args["ar_plano"]) : "")." 
                    ".(is_array($args["ar_tipo"]) ? $this->whereTipo($args["ar_tipo"]) : "")." 
                    ".(is_array($args["ar_tempo_plano"]) ? $this->whereTempoPlano($args["ar_tempo_plano"]) : "")." 
                    ".(is_array($args["ar_idade"]) ? $this->whereIdade($args["ar_idade"]) : "")." 
                    ".(is_array($args["ar_renda"]) ? $this->whereRenda($args["ar_renda"]) : "")." 
                    ".(is_array($args["ar_uf"]) ? $this->whereUF($args["ar_uf"]) : "")." 
                    ".(trim($args["cd_sexo"]) != "" ? "AND eet.part_sexo = '".trim($args["cd_sexo"])."'" : "")." 
                    ".(trim($args["cd_senha"]) != "" ? "AND COALESCE(eet.part_tipo_senha, 'SEM') = '".trim($args["cd_senha"])."'" : "")." 
                   AND COALESCE(eet.part_cd_registro_empregado,0) > 0
                   AND eet.cd_envia_emails_tracker = (SELECT MIN(eet1.cd_envia_emails_tracker)
                                                        FROM projetos.envia_emails_tracker eet1
                                                        JOIN projetos.envia_emails ee1
                                                          ON ee1.cd_email = eet1.cd_email
                                                       WHERE ee1.cd_divulgacao               = ee.cd_divulgacao
                                                         AND eet1.part_cd_empresa            = eet.part_cd_empresa
                                                         AND eet1.part_cd_registro_empregado = eet.part_cd_registro_empregado
                                                         AND eet1.part_seq_dependencia       = eet.part_seq_dependencia)                     
                 
                 GROUP BY ds_item
                 ORDER BY qt_item DESC
                ";
        #echo "<pre style='text-align:center;'>$qr_sql</pre>";

        $result = $this->db->query($qr_sql);
    }

    function participanteUF(&$result, $args=array())
    {
        $qr_sql = "
                SELECT COALESCE(eet.part_uf,'Não identificado') AS ds_item, 
                       COUNT(*) AS qt_item
                  FROM projetos.envia_emails ee
                  JOIN projetos.envia_emails_tracker eet
                    ON eet.cd_email = ee.cd_email
                 WHERE ee.cd_divulgacao = ".intval($args['cd_divulgacao'])."
                    ".(((trim($args['dt_envio_ini']) != "") AND (trim($args['dt_envio_fim']) != "")) ? " AND DATE_TRUNC('day', ee.dt_email_enviado) BETWEEN TO_DATE('".$args['dt_envio_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_envio_fim']."', 'DD/MM/YYYY')" : "")."
                    ".(is_array($args["ar_empresa"]) ? $this->whereEmpresa($args["ar_empresa"]) : "")." 
                    ".(is_array($args["ar_plano"]) ? $this->wherePlano($args["ar_plano"]) : "")." 
                    ".(is_array($args["ar_tipo"]) ? $this->whereTipo($args["ar_tipo"]) : "")." 
                    ".(is_array($args["ar_tempo_plano"]) ? $this->whereTempoPlano($args["ar_tempo_plano"]) : "")." 
                    ".(is_array($args["ar_idade"]) ? $this->whereIdade($args["ar_idade"]) : "")." 
                    ".(is_array($args["ar_renda"]) ? $this->whereRenda($args["ar_renda"]) : "")." 
                    ".(is_array($args["ar_uf"]) ? $this->whereUF($args["ar_uf"]) : "")." 
                    ".(trim($args["cd_sexo"]) != "" ? "AND eet.part_sexo = '".trim($args["cd_sexo"])."'" : "")." 
                    ".(trim($args["cd_senha"]) != "" ? "AND COALESCE(eet.part_tipo_senha, 'SEM') = '".trim($args["cd_senha"])."'" : "")." 
                   AND COALESCE(eet.part_cd_registro_empregado,0) > 0
                   AND eet.cd_envia_emails_tracker = (SELECT MIN(eet1.cd_envia_emails_tracker)
                                                        FROM projetos.envia_emails_tracker eet1
                                                        JOIN projetos.envia_emails ee1
                                                          ON ee1.cd_email = eet1.cd_email
                                                       WHERE ee1.cd_divulgacao               = ee.cd_divulgacao
                                                         AND eet1.part_cd_empresa            = eet.part_cd_empresa
                                                         AND eet1.part_cd_registro_empregado = eet.part_cd_registro_empregado
                                                         AND eet1.part_seq_dependencia       = eet.part_seq_dependencia)                     
                 GROUP BY ds_item
                 ORDER BY qt_item DESC
                ";
        #echo "<pre style='text-align:center;'>$qr_sql</pre>";

        $result = $this->db->query($qr_sql);
    }
    
    function participanteMapaCidade(&$result, $args=array())
    {
        $qr_sql = "
                SELECT COALESCE(eet.part_cidade || ' - ' || eet.part_uf,'Não identificado') AS ds_cidade, 
                       b.longitude,
                       b.latitude
                  FROM projetos.envia_emails ee
                  JOIN projetos.envia_emails_tracker eet
                    ON eet.cd_email = ee.cd_email
                  JOIN geografico.br_localidades_2010_v1 b
                    ON b.uf         = eet.part_uf
                   AND b.nm_localid = eet.part_cidade
                   AND b.tipo       = 'URBANO' 
                   AND b.cd_nivel   = '1' 
                   AND b.nm_categor = 'CIDADE'
                 WHERE ee.cd_divulgacao = ".intval($args['cd_divulgacao'])."
                    ".(((trim($args['dt_envio_ini']) != "") AND (trim($args['dt_envio_fim']) != "")) ? " AND DATE_TRUNC('day', ee.dt_email_enviado) BETWEEN TO_DATE('".$args['dt_envio_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_envio_fim']."', 'DD/MM/YYYY')" : "")."
                    ".(is_array($args["ar_empresa"]) ? $this->whereEmpresa($args["ar_empresa"]) : "")." 
                    ".(is_array($args["ar_plano"]) ? $this->wherePlano($args["ar_plano"]) : "")." 
                    ".(is_array($args["ar_tipo"]) ? $this->whereTipo($args["ar_tipo"]) : "")." 
                    ".(is_array($args["ar_tempo_plano"]) ? $this->whereTempoPlano($args["ar_tempo_plano"]) : "")." 
                    ".(is_array($args["ar_idade"]) ? $this->whereIdade($args["ar_idade"]) : "")." 
                    ".(is_array($args["ar_renda"]) ? $this->whereRenda($args["ar_renda"]) : "")." 
                    ".(is_array($args["ar_uf"]) ? $this->whereUF($args["ar_uf"]) : "")." 
                    ".(trim($args["cd_sexo"]) != "" ? "AND eet.part_sexo = '".trim($args["cd_sexo"])."'" : "")." 
                    ".(trim($args["cd_senha"]) != "" ? "AND COALESCE(eet.part_tipo_senha, 'SEM') = '".trim($args["cd_senha"])."'" : "")." 
                   AND COALESCE(eet.part_cd_registro_empregado,0) > 0
                   AND eet.cd_envia_emails_tracker = (SELECT MIN(eet1.cd_envia_emails_tracker)
                                                        FROM projetos.envia_emails_tracker eet1
                                                        JOIN projetos.envia_emails ee1
                                                          ON ee1.cd_email = eet1.cd_email
                                                       WHERE ee1.cd_divulgacao               = ee.cd_divulgacao
                                                         AND eet1.part_cd_empresa            = eet.part_cd_empresa
                                                         AND eet1.part_cd_registro_empregado = eet.part_cd_registro_empregado
                                                         AND eet1.part_seq_dependencia       = eet.part_seq_dependencia)                     
                 ORDER BY ds_cidade,
                          b.longitude,
                          b.latitude
                ";
        #echo "<pre style='text-align:center;'>$qr_sql</pre>";

        $result = $this->db->query($qr_sql);
    }   
    
    function tecnologiaMapaCidade(&$result, $args=array())
    {
        $qr_sql = "
                SELECT COALESCE(eet.ipgeo_country_code || ' - ' || eet.ipgeo_city || ' - ' || eet.ipgeo_region_name,'Não identificado') AS ds_cidade, 
                       eet.ipgeo_longitude AS longitude,
                       eet.ipgeo_latitude AS latitude
                  FROM projetos.envia_emails ee
                  JOIN projetos.envia_emails_tracker eet
                    ON eet.cd_email = ee.cd_email
                 WHERE ee.cd_divulgacao = ".intval($args['cd_divulgacao'])."
                    ".(((trim($args['dt_envio_ini']) != "") AND (trim($args['dt_envio_fim']) != "")) ? " AND DATE_TRUNC('day', ee.dt_email_enviado) BETWEEN TO_DATE('".$args['dt_envio_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_envio_fim']."', 'DD/MM/YYYY')" : "")."
                 ORDER BY ds_cidade,
                          longitude,
                          latitude
                ";
        #echo "<pre style='text-align:center;'>$qr_sql</pre>";

        $result = $this->db->query($qr_sql);
    }
    
    #### COMBOS ####
    function comboEmpresa(&$result, $args=array())
    {
        $qr_sql = "
                    SELECT DISTINCT COALESCE(p.cd_empresa, -1) AS value,
                           COALESCE(p.sigla,'Não identificado') AS text
                      FROM projetos.envia_emails ee
                      JOIN projetos.envia_emails_tracker eet
                        ON eet.cd_email = ee.cd_email
                      JOIN public.patrocinadoras p
                        ON p.cd_empresa = eet.part_cd_empresa
                     WHERE ee.cd_divulgacao = ".intval($args['cd_divulgacao'])."                        
                     ORDER BY text
                  ";
             
        $result = $this->db->query($qr_sql);    
    }
    
    function comboPlano(&$result, $args=array())
    {
        $qr_sql = "
                    SELECT DISTINCT COALESCE(p.cd_plano, -1) AS value,
                           COALESCE(p.descricao,'Não identificado') AS text
                      FROM projetos.envia_emails ee
                      JOIN projetos.envia_emails_tracker eet
                        ON eet.cd_email = ee.cd_email
                      JOIN public.planos p
                        ON p.cd_plano = eet.part_cd_plano
                     WHERE ee.cd_divulgacao = ".intval($args['cd_divulgacao'])."                            
                     ORDER BY text
                  ";
             
        $result = $this->db->query($qr_sql);    
    }   
    
    function comboUF(&$result, $args=array())
    {
        $qr_sql = "
                    SELECT DISTINCT COALESCE(eet.part_uf, '') AS value,
                           COALESCE(eet.part_uf,'Não identificado') AS text
                      FROM projetos.envia_emails ee
                      JOIN projetos.envia_emails_tracker eet
                        ON eet.cd_email = ee.cd_email
                     WHERE ee.cd_divulgacao = ".intval($args['cd_divulgacao'])."                        
                     ORDER BY text
                  ";
             
        $result = $this->db->query($qr_sql);    
    }   
    
    function comboSenha(&$result, $args=array())
    {
        $qr_sql = "
                    SELECT DISTINCT COALESCE(eet.part_tipo_senha, 'SEM') AS value,
                           COALESCE(eet.part_tipo_senha,'SEM') AS text
                      FROM projetos.envia_emails ee
                      JOIN projetos.envia_emails_tracker eet
                        ON eet.cd_email = ee.cd_email
                     WHERE ee.cd_divulgacao = ".intval($args['cd_divulgacao'])."
                     ORDER BY text
                  ";
             
        $result = $this->db->query($qr_sql);    
    }

    function comboTipo(&$result, $args=array())
    {
        $qr_sql = "
                    SELECT DISTINCT COALESCE(eet.part_tipo, '') AS value,
                           COALESCE(eet.part_tipo,'Não identificado') AS text
                      FROM projetos.envia_emails ee
                      JOIN projetos.envia_emails_tracker eet
                        ON eet.cd_email = ee.cd_email
                     WHERE ee.cd_divulgacao = ".intval($args['cd_divulgacao'])."
                     ORDER BY text
                  ";
             
        $result = $this->db->query($qr_sql);    
    }

    function comboTempoPlano(&$result, $args=array())
    {
        $qr_sql = "
                    SELECT DISTINCT CASE WHEN COALESCE(eet.part_qt_ano_plano,-1) BETWEEN 00 AND 10 THEN '00 à 10'
                                WHEN COALESCE(eet.part_qt_ano_plano,-1) BETWEEN 11 AND 20 THEN '11 à 20'
                                WHEN COALESCE(eet.part_qt_ano_plano,-1) BETWEEN 21 AND 30 THEN '21 à 30'
                                WHEN COALESCE(eet.part_qt_ano_plano,-1) BETWEEN 31 AND 40 THEN '31 à 40'
                                WHEN COALESCE(eet.part_qt_ano_plano,-1) BETWEEN 41 AND 50 THEN '41 à 50'
                                WHEN COALESCE(eet.part_qt_ano_plano,-1) BETWEEN 51 AND 60 THEN '51 à 60'
                                WHEN COALESCE(eet.part_qt_ano_plano,-1) BETWEEN 61 AND 70 THEN '61 à 70'
                                WHEN COALESCE(eet.part_qt_ano_plano,-1) BETWEEN 71 AND 80 THEN '71 à 80'
                                WHEN COALESCE(eet.part_qt_ano_plano,-1) BETWEEN 81 AND 90 THEN '81 à 90'
                                WHEN COALESCE(eet.part_qt_ano_plano,-1) BETWEEN 91 AND 100 THEN '91 à 100'
                                WHEN COALESCE(eet.part_qt_ano_plano,-1) > 100 THEN '+ de 100'
                                ELSE 'Não identificado'
                           END AS value, 
                           CASE WHEN COALESCE(eet.part_qt_ano_plano,-1) BETWEEN 00 AND 10 THEN '00 à 10'
                                WHEN COALESCE(eet.part_qt_ano_plano,-1) BETWEEN 11 AND 20 THEN '11 à 20'
                                WHEN COALESCE(eet.part_qt_ano_plano,-1) BETWEEN 21 AND 30 THEN '21 à 30'
                                WHEN COALESCE(eet.part_qt_ano_plano,-1) BETWEEN 31 AND 40 THEN '31 à 40'
                                WHEN COALESCE(eet.part_qt_ano_plano,-1) BETWEEN 41 AND 50 THEN '41 à 50'
                                WHEN COALESCE(eet.part_qt_ano_plano,-1) BETWEEN 51 AND 60 THEN '51 à 60'
                                WHEN COALESCE(eet.part_qt_ano_plano,-1) BETWEEN 61 AND 70 THEN '61 à 70'
                                WHEN COALESCE(eet.part_qt_ano_plano,-1) BETWEEN 71 AND 80 THEN '71 à 80'
                                WHEN COALESCE(eet.part_qt_ano_plano,-1) BETWEEN 81 AND 90 THEN '81 à 90'
                                WHEN COALESCE(eet.part_qt_ano_plano,-1) BETWEEN 91 AND 100 THEN '91 à 100'
                                WHEN COALESCE(eet.part_qt_ano_plano,-1) > 100 THEN '+ de 100'
                                ELSE 'Não identificado'
                           END AS text
                      FROM projetos.envia_emails ee
                      JOIN projetos.envia_emails_tracker eet
                        ON eet.cd_email = ee.cd_email
                     WHERE ee.cd_divulgacao = ".intval($args['cd_divulgacao'])."
                     ORDER BY text
                  ";
             
        $result = $this->db->query($qr_sql);    
    }   
    
    function comboIdade(&$result, $args=array())
    {
        $qr_sql = "
                    SELECT DISTINCT CASE WHEN COALESCE(eet.part_nr_idade,-1) BETWEEN 00 AND 10 THEN '00 à 10'
                            WHEN COALESCE(eet.part_nr_idade,-1) BETWEEN 11 AND 20 THEN '11 à 20'
                            WHEN COALESCE(eet.part_nr_idade,-1) BETWEEN 21 AND 30 THEN '21 à 30'
                            WHEN COALESCE(eet.part_nr_idade,-1) BETWEEN 31 AND 40 THEN '31 à 40'
                            WHEN COALESCE(eet.part_nr_idade,-1) BETWEEN 41 AND 50 THEN '41 à 50'
                            WHEN COALESCE(eet.part_nr_idade,-1) BETWEEN 51 AND 60 THEN '51 à 60'
                            WHEN COALESCE(eet.part_nr_idade,-1) BETWEEN 61 AND 70 THEN '61 à 70'
                            WHEN COALESCE(eet.part_nr_idade,-1) BETWEEN 71 AND 80 THEN '71 à 80'
                            WHEN COALESCE(eet.part_nr_idade,-1) BETWEEN 81 AND 90 THEN '81 à 90'
                            WHEN COALESCE(eet.part_nr_idade,-1) BETWEEN 91 AND 100 THEN '91 à 100'
                            WHEN COALESCE(eet.part_nr_idade,-1) > 100 THEN '+ de 100'
                            ELSE 'Não identificado'
                           END AS value,
                           CASE WHEN COALESCE(eet.part_nr_idade,-1) BETWEEN 00 AND 10 THEN '00 à 10'
                            WHEN COALESCE(eet.part_nr_idade,-1) BETWEEN 11 AND 20 THEN '11 à 20'
                            WHEN COALESCE(eet.part_nr_idade,-1) BETWEEN 21 AND 30 THEN '21 à 30'
                            WHEN COALESCE(eet.part_nr_idade,-1) BETWEEN 31 AND 40 THEN '31 à 40'
                            WHEN COALESCE(eet.part_nr_idade,-1) BETWEEN 41 AND 50 THEN '41 à 50'
                            WHEN COALESCE(eet.part_nr_idade,-1) BETWEEN 51 AND 60 THEN '51 à 60'
                            WHEN COALESCE(eet.part_nr_idade,-1) BETWEEN 61 AND 70 THEN '61 à 70'
                            WHEN COALESCE(eet.part_nr_idade,-1) BETWEEN 71 AND 80 THEN '71 à 80'
                            WHEN COALESCE(eet.part_nr_idade,-1) BETWEEN 81 AND 90 THEN '81 à 90'
                            WHEN COALESCE(eet.part_nr_idade,-1) BETWEEN 91 AND 100 THEN '91 à 100'
                            WHEN COALESCE(eet.part_nr_idade,-1) > 100 THEN '+ de 100'
                            ELSE 'Não identificado'
                           END AS text
                      FROM projetos.envia_emails ee
                      JOIN projetos.envia_emails_tracker eet
                        ON eet.cd_email = ee.cd_email
                     WHERE ee.cd_divulgacao = ".intval($args['cd_divulgacao'])."
                     ORDER BY text
                  ";
             
        $result = $this->db->query($qr_sql);    
    }   
    
    function comboRenda(&$result, $args=array())
    {
        $qr_sql = "
                    SELECT DISTINCT CASE WHEN COALESCE(eet.part_vl_renda,-1) = 0 THEN 'Sem'
                            WHEN COALESCE(eet.part_vl_renda,-1) BETWEEN 0000.01 AND 1000 THEN '0,01 à 1.000,00'
                            WHEN COALESCE(eet.part_vl_renda,-1) BETWEEN 1000.01 AND 2500 THEN '1.000,01 à 2.500,00'
                            WHEN COALESCE(eet.part_vl_renda,-1) BETWEEN 2500.01 AND 5000 THEN '2.500,01 à 5.000,00'
                            WHEN COALESCE(eet.part_vl_renda,-1) BETWEEN 5000.01 AND 7500 THEN '6.000,01 à 7.500,00'
                            WHEN COALESCE(eet.part_vl_renda,-1) BETWEEN 7500.01 AND 10000 THEN '7.500,01 à 10.000,00'
                            WHEN COALESCE(eet.part_vl_renda,-1) BETWEEN 10000.01 AND 15000 THEN '10.000,01 à 15.000,00'
                            WHEN COALESCE(eet.part_vl_renda,-1) BETWEEN 15000.01 AND 20000 THEN '15.000,01 à 20.000,00'
                            WHEN COALESCE(eet.part_vl_renda,-1) BETWEEN 20000.01 AND 30000 THEN '20.000,01 à 30.000,00'
                            WHEN COALESCE(eet.part_vl_renda,-1) > 30000.01 THEN '+ de 30.000,00'
                            ELSE 'Não identificado'
                           END AS value,
                           CASE WHEN COALESCE(eet.part_vl_renda,-1) = 0 THEN 'Sem'
                            WHEN COALESCE(eet.part_vl_renda,-1) BETWEEN 0000.01 AND 1000 THEN '0,01 à 1.000,00'
                            WHEN COALESCE(eet.part_vl_renda,-1) BETWEEN 1000.01 AND 2500 THEN '1.000,01 à 2.500,00'
                            WHEN COALESCE(eet.part_vl_renda,-1) BETWEEN 2500.01 AND 5000 THEN '2.500,01 à 5.000,00'
                            WHEN COALESCE(eet.part_vl_renda,-1) BETWEEN 5000.01 AND 7500 THEN '6.000,01 à 7.500,00'
                            WHEN COALESCE(eet.part_vl_renda,-1) BETWEEN 7500.01 AND 10000 THEN '7.500,01 à 10.000,00'
                            WHEN COALESCE(eet.part_vl_renda,-1) BETWEEN 10000.01 AND 15000 THEN '10.000,01 à 15.000,00'
                            WHEN COALESCE(eet.part_vl_renda,-1) BETWEEN 15000.01 AND 20000 THEN '15.000,01 à 20.000,00'
                            WHEN COALESCE(eet.part_vl_renda,-1) BETWEEN 20000.01 AND 30000 THEN '20.000,01 à 30.000,00'
                            WHEN COALESCE(eet.part_vl_renda,-1) > 30000.01 THEN '+ de 30.000,00'
                            ELSE 'Não identificado'
                           END AS text,
                           CASE WHEN COALESCE(eet.part_vl_renda,-1) = 0 THEN 0
                            WHEN COALESCE(eet.part_vl_renda,-1) BETWEEN 0000.01 AND 1000 THEN 1
                            WHEN COALESCE(eet.part_vl_renda,-1) BETWEEN 1000.01 AND 2500 THEN 2
                            WHEN COALESCE(eet.part_vl_renda,-1) BETWEEN 2500.01 AND 5000 THEN 3
                            WHEN COALESCE(eet.part_vl_renda,-1) BETWEEN 5000.01 AND 7500 THEN 4
                            WHEN COALESCE(eet.part_vl_renda,-1) BETWEEN 7500.01 AND 10000 THEN 5
                            WHEN COALESCE(eet.part_vl_renda,-1) BETWEEN 10000.01 AND 15000 THEN 6
                            WHEN COALESCE(eet.part_vl_renda,-1) BETWEEN 15000.01 AND 20000 THEN 7
                            WHEN COALESCE(eet.part_vl_renda,-1) BETWEEN 20000.01 AND 30000 THEN 8
                            WHEN COALESCE(eet.part_vl_renda,-1) > 30000.01 THEN 9
                            ELSE 10
                           END AS ordem                        
                      FROM projetos.envia_emails ee
                      JOIN projetos.envia_emails_tracker eet
                        ON eet.cd_email = ee.cd_email
                     WHERE ee.cd_divulgacao = ".intval($args['cd_divulgacao'])."
                     ORDER BY ordem
                  ";
             
        $result = $this->db->query($qr_sql);    
    }   

    #### WHERE ####
    private function whereEmpresa($ar_where)
    {
        return "
                    AND eet.part_cd_empresa IN (".implode(",",$ar_where).")
               ";
    }
    
    private function wherePlano($ar_where)
    {
        return "
                    AND eet.part_cd_plano IN (".implode(",",$ar_where).")
               ";
    }   
    
    private function whereTipo($ar_where)
    {
        return "
                    AND eet.part_tipo IN ('".implode("','",$ar_where)."')
               ";
    }   
    
    private function whereUF($ar_where)
    {
        return "
                    AND eet.part_uf IN ('".implode("','",$ar_where)."')
               ";
    }       
    
    private function whereTempoPlano($ar_where)
    {
        return "
                    AND (CASE WHEN COALESCE(eet.part_qt_ano_plano,-1) BETWEEN 00 AND 10 THEN '00 à 10'
                                WHEN COALESCE(eet.part_qt_ano_plano,-1) BETWEEN 11 AND 20 THEN '11 à 20'
                                WHEN COALESCE(eet.part_qt_ano_plano,-1) BETWEEN 21 AND 30 THEN '21 à 30'
                                WHEN COALESCE(eet.part_qt_ano_plano,-1) BETWEEN 31 AND 40 THEN '31 à 40'
                                WHEN COALESCE(eet.part_qt_ano_plano,-1) BETWEEN 41 AND 50 THEN '41 à 50'
                                WHEN COALESCE(eet.part_qt_ano_plano,-1) BETWEEN 51 AND 60 THEN '51 à 60'
                                WHEN COALESCE(eet.part_qt_ano_plano,-1) BETWEEN 61 AND 70 THEN '61 à 70'
                                WHEN COALESCE(eet.part_qt_ano_plano,-1) BETWEEN 71 AND 80 THEN '71 à 80'
                                WHEN COALESCE(eet.part_qt_ano_plano,-1) BETWEEN 81 AND 90 THEN '81 à 90'
                                WHEN COALESCE(eet.part_qt_ano_plano,-1) BETWEEN 91 AND 100 THEN '91 à 100'
                                WHEN COALESCE(eet.part_qt_ano_plano,-1) > 100 THEN '+ de 100'
                                ELSE 'Não identificado'
                           END) IN ('".implode("','",$ar_where)."')
               ";
    }   
    
    private function whereIdade($ar_where)
    {
        return "
                    AND (CASE WHEN COALESCE(eet.part_nr_idade,-1) BETWEEN 00 AND 10 THEN '00 à 10'
                            WHEN COALESCE(eet.part_nr_idade,-1) BETWEEN 11 AND 20 THEN '11 à 20'
                            WHEN COALESCE(eet.part_nr_idade,-1) BETWEEN 21 AND 30 THEN '21 à 30'
                            WHEN COALESCE(eet.part_nr_idade,-1) BETWEEN 31 AND 40 THEN '31 à 40'
                            WHEN COALESCE(eet.part_nr_idade,-1) BETWEEN 41 AND 50 THEN '41 à 50'
                            WHEN COALESCE(eet.part_nr_idade,-1) BETWEEN 51 AND 60 THEN '51 à 60'
                            WHEN COALESCE(eet.part_nr_idade,-1) BETWEEN 61 AND 70 THEN '61 à 70'
                            WHEN COALESCE(eet.part_nr_idade,-1) BETWEEN 71 AND 80 THEN '71 à 80'
                            WHEN COALESCE(eet.part_nr_idade,-1) BETWEEN 81 AND 90 THEN '81 à 90'
                            WHEN COALESCE(eet.part_nr_idade,-1) BETWEEN 91 AND 100 THEN '91 à 100'
                            WHEN COALESCE(eet.part_nr_idade,-1) > 100 THEN '+ de 100'
                            ELSE 'Não identificado'
                           END) IN ('".implode("','",$ar_where)."')
               ";
    }   

    private function whereRenda($ar_where)
    {
        return "
                    AND (CASE WHEN COALESCE(eet.part_vl_renda,-1) = 0 THEN 'Sem'
                            WHEN COALESCE(eet.part_vl_renda,-1) BETWEEN 0000.01 AND 1000 THEN '0,01 à 1.000,00'
                            WHEN COALESCE(eet.part_vl_renda,-1) BETWEEN 1000.01 AND 2500 THEN '1.000,01 à 2.500,00'
                            WHEN COALESCE(eet.part_vl_renda,-1) BETWEEN 2500.01 AND 5000 THEN '2.500,01 à 5.000,00'
                            WHEN COALESCE(eet.part_vl_renda,-1) BETWEEN 5000.01 AND 7500 THEN '6.000,01 à 7.500,00'
                            WHEN COALESCE(eet.part_vl_renda,-1) BETWEEN 7500.01 AND 10000 THEN '7.500,01 à 10.000,00'
                            WHEN COALESCE(eet.part_vl_renda,-1) BETWEEN 10000.01 AND 15000 THEN '10.000,01 à 15.000,00'
                            WHEN COALESCE(eet.part_vl_renda,-1) BETWEEN 15000.01 AND 20000 THEN '15.000,01 à 20.000,00'
                            WHEN COALESCE(eet.part_vl_renda,-1) BETWEEN 20000.01 AND 30000 THEN '20.000,01 à 30.000,00'
                            WHEN COALESCE(eet.part_vl_renda,-1) > 30000.01 THEN '+ de 30.000,00'
                            ELSE 'Não identificado'
                           END) IN ('".implode("','",$ar_where)."')
               ";
    }   
}
