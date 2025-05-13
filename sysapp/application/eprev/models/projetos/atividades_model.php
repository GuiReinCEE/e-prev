<?php
class Atividades_model extends Model
{
    function __construct()
    {
        parent::Model();
    }

    public function get_divisao_solicitante()
    {
        $qr_sql = "
            SELECT DISTINCT codigo AS value, 
                   nome AS text 
              FROM projetos.divisoes a 
              JOIN projetos.atividades b 
                ON a.codigo = b.divisao 
             ORDER BY nome ASC;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function get_projeto()
    {
        $qr_sql = "
            SELECT codigo AS value, 
                   nome AS text 
              FROM projetos.projetos 
             WHERE codigo IN
                           (
                            SELECT DISTINCT(a.sistema) 
                              FROM projetos.atividades a
                              JOIN listas l1
                                ON l1.codigo = a.status_atual 
                              JOIN listas l2 
                                ON l2.codigo = a.tipo
                             WHERE l1.categoria = 'STAT' 
                               AND l2.categoria = 'TPAT' 
                           ) 
            ORDER BY nome;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function get_solicitante()
    {
        $qr_sql = "
            SELECT DISTINCT a.codigo AS value, 
                   a.nome AS text
              FROM projetos.usuarios_controledi a
              JOIN projetos.atividades b 
                ON a.codigo = b.cod_solicitante
            ORDER BY a.nome;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function get_atendente()
    {
        $qr_sql = "
            SELECT DISTINCT a.codigo AS value, 
                   a.nome AS text
              FROM projetos.usuarios_controledi a
              JOIN projetos.atividades b 
                ON a.codigo = b.cod_atendente
            ORDER BY a.nome;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function get_tipo_solicitacao()
    {
        $qr_sql = "
            SELECT tm.codigo AS value,
                   tm.divisao || ' - ' || tm.descricao AS text
              FROM public.listas tm
              JOIN projetos.divisoes d
                ON d.codigo = tm.divisao
             WHERE tm.categoria   = 'TPMN' 
               AND tm.dt_exclusao IS NULL
               AND tm.divisao     <> '*'
             ORDER BY tm.divisao ASC,
                      tm.descricao ASC;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function get_classificacao()
    {
        $qr_sql = "
            SELECT cd_atividade_classificacao AS value,
                   ds_atividade_classificacao AS text
              FROM projetos.atividade_classificacao
             WHERE dt_exclusao IS NULL
             ORDER BY ds_atividade_classificacao ASC;";

        return $this->db->query($qr_sql)->result_array();
    }

    function buscarAtividade(&$result, $args=array())
    {
		$qr_sql = "
					SELECT '".site_url("atividade/atividade_solicitacao/index/")."/' || area || '/' || numero AS url
					  FROM projetos.atividades 
					 WHERE numero = ".intval($args["cd_atividade"]).";
                  ";
        $result = $this->db->query($qr_sql);
    }	
	
    function listar( &$result, $args=array() )
    {
        //
        // S T A T U S
        //
        $status="";
        $sep="";
        if($args['status_aguardando']=="S")
        { 
            $status.=$sep." 'AINI','ADIR','AMAN','AIST', 'CAAI', 'AICS', 'AINF', 'GCAI', 'AIGA', 'ICGA', 'AIGJ', 'AISB', 'SGAI', 'AIGD', 'AIRH', 'AIDI' ";
            $sep=",";
        }

        if($args['status_em_andamento']=="S")
        { 
            $status.=$sep." 'EANA','EMAN','EMST', 'CAEN', 'EECS', 'EAGJ', 'EMSB', 'EERH', 'SUST', 'EEDI', 'GFMN' ";
            $sep=",";
        }

        if($args['status_encerrado']=="S")
        {
            $status.=$sep." 'CAGD','COGD','COSB','CANC','CONC','LIBE','AGDF','CAST','COST', 'CACO', 'CACA', 'CACS', 'COCS', 'CONF', 'CANF', 'GCCA', 'GCCO', 'COGA', 'COGJ', 'COSB', 'CASB' ,'SGCA' , 'SGCO' , 'CARH', 'ACRH', 'SUSP', 'CADI', 'ACDI' ";
            $sep=",";
        }

        if($args['status_em_teste']=="S")
        { 
            $status.=$sep." 'ETES', 'AOCS' ";
            $sep=",";
        }

        if($args['status_aguardando_definicao']=="S")
        {
            $status.=$sep." 'AGDF', 'ASCS' ";
            $sep=",";
        }
		
        if($args['status_aguardando_usuario']=="S")
        {
            $status.=$sep." 'AUSR'";
            $sep=",";
        }		

        if(trim($status)!=''){ $status=" AND a.status_atual in ($status) "; }

        //
        //    S O L I C I T A Ç Õ E S   F E I T A S    E    R E C E B I D A S
        //

        $feitas='';
        $recebidas='';
        
        if(isset($args['calculo_taxa_joia']) AND trim($args['calculo_taxa_joia']) == 'S')
        {
            $feitas = "AND a.area = 'GA' 
                       AND a.tipo_solicitacao = 'CATJ'";
        }
        else if(isset($args['fl_juridico_emprestimo']) AND trim($args['fl_juridico_emprestimo']) == 'S' AND (in_array($args['cd_usuario_logado'], array(251, 296, 337, 412, 386, 22, 447))))
        {
            $feitas = "AND us.divisao = 'GJ'
                       AND a.area = 'GF'
                       AND a.tipo_solicitacao = 'EMPE'";
        }
        else if(isset($args['fl_administrativo']) AND trim($args['fl_administrativo']) == 'S' AND (in_array($args['cd_usuario_logado'], array(251, 5, 483, 359))))
        {
            $feitas = "AND us.divisao = 'GJ'
                       AND a.area = 'GAD'";
        }
        else
        {
            if(($args['feitas'] == 'S') AND ($args['recebidas'] == 'S'))
            {
                if($args['fl_gerente_view'] == 'S')
                {
                    $feitas="
                        AND (
                                (
                                    a.cod_solicitante IN 
                                    (
                                        SELECT uc.codigo
                                          FROM projetos.usuarios_controledi uc
                                         WHERE uc.divisao = '".$args['gerencia_usuario_logado']."'
                                    )
                                )
                                OR 
                                (
                                    a.cod_atendente IN 
                                    (
                                        SELECT uc.codigo
                                          FROM projetos.usuarios_controledi uc
                                         WHERE uc.divisao = '".$args['gerencia_usuario_logado']."'
                                    )
                                )
                                OR 
                                (
                                    a.cd_substituto IN 
                                    (
                                        SELECT uc.codigo
                                          FROM projetos.usuarios_controledi uc
                                         WHERE uc.divisao = '".$args['gerencia_usuario_logado']."'
                                    )
                                )
                                OR 
                                (
                                    a.cod_testador IN 
                                    (
                                        SELECT uc.codigo
                                          FROM projetos.usuarios_controledi uc
                                         WHERE uc.divisao = '".$args['gerencia_usuario_logado']."'
                                    )
                                )                               
                            )
                    ";
                }
                else
                {
                    $feitas=" AND (a.cod_testador=".$args["cd_usuario_logado"]." OR a.cod_solicitante=".$args["cd_usuario_logado"]." OR a.cd_substituto=".$args["cd_usuario_logado"]." OR a.cod_atendente=".$args["cd_usuario_logado"]."  ) ";
                }
            }
            elseif($args['feitas'] == 'S')
            {
                if($args['fl_gerente_view'] == 'S')
                {
                    $feitas="
                            AND a.cod_solicitante IN (SELECT uc.codigo
                                                        FROM projetos.usuarios_controledi uc
                                                       WHERE uc.divisao = '".$args['gerencia_usuario_logado']."')
                            
                            ";
                }
                else
                {
                    $feitas=" AND a.cod_solicitante = ".$args["cd_usuario_logado"]." ";
                }               
            }
            elseif($args['recebidas'] == 'S')
            {
                if($args['fl_gerente_view'] == 'S')
                {
                    $feitas="
                            AND (
                                    a.cod_atendente IN (SELECT uc.codigo
                                                          FROM projetos.usuarios_controledi uc
                                                         WHERE uc.divisao = '".$args['gerencia_usuario_logado']."')
                                    OR                
                                    a.cod_testador IN (SELECT uc.codigo
                                                         FROM projetos.usuarios_controledi uc
                                                        WHERE uc.divisao = '".$args['gerencia_usuario_logado']."')                                                    
                                )               
                            ";
                }
                else
                {
                    $feitas=" AND (a.cod_atendente = ".$args["cd_usuario_logado"]." OR a.cd_substituto = ".$args["cd_usuario_logado"]." OR a.cod_testador = ".$args["cd_usuario_logado"].") ";
                }
            }
            else
            {
                $feitas=" AND 0 = 1 ";
            }
        }
        
        
        $cronograma = "";
        if( isset($args['fl_cronograma']) && trim($args['fl_cronograma'])!= "")
        {
            if (trim($args['fl_cronograma']) == "S")
            {
                $cronograma = " AND 0 < 
                                      (SELECT COUNT(*)
                                         FROM projetos.atividade_cronograma ac
                                         JOIN projetos.atividade_cronograma_item aci 
                                           ON aci.cd_atividade_cronograma = ac.cd_atividade_cronograma
                                        WHERE ac.dt_exclusao IS NULL
                                          AND ac.cd_responsavel = a.cod_atendente
                                          AND aci.dt_exclusao  IS NULL
                                          AND aci.cd_atividade = a.numero
                                          AND ac.dt_inclusao   = (SELECT MAX(ac1.dt_inclusao)
                                                                    FROM projetos.atividade_cronograma ac1
                                                                    JOIN projetos.atividade_cronograma_item aci1 
                                                                      ON aci1.cd_atividade_cronograma = ac1.cd_atividade_cronograma
                                                                   WHERE ac1.dt_exclusao   IS NULL
                                                                     AND aci1.dt_exclusao  IS NULL
                                                                     AND aci1.cd_atividade = aci.cd_atividade))
                                         "; 
            }
            
            if (trim($args['fl_cronograma']) == "N")
            {
                $cronograma = " AND 0 = 
                                      (SELECT COUNT(*)
                                         FROM projetos.atividade_cronograma ac
                                         JOIN projetos.atividade_cronograma_item aci 
                                           ON aci.cd_atividade_cronograma = ac.cd_atividade_cronograma
                                        WHERE ac.dt_exclusao IS NULL
                                          AND ac.cd_responsavel = a.cod_atendente
                                          AND aci.dt_exclusao  IS NULL
                                          AND aci.cd_atividade = a.numero
                                          AND ac.dt_inclusao   = (SELECT MAX(ac1.dt_inclusao)
                                                                    FROM projetos.atividade_cronograma ac1
                                                                    JOIN projetos.atividade_cronograma_item aci1 
                                                                      ON aci1.cd_atividade_cronograma = ac1.cd_atividade_cronograma
                                                                   WHERE ac1.dt_exclusao   IS NULL
                                                                     AND aci1.dt_exclusao  IS NULL
                                                                     AND aci1.cd_atividade = aci.cd_atividade))
                                         ";                                         
            }           
        }
        
        $cronograma_grupo = "";
        if( isset($args['cd_atividade_cronograma_grupo']) && trim($args['cd_atividade_cronograma_grupo'])!= "")
        {
            $cronograma = " AND 0 < 
                                  (SELECT COUNT(*)
                                     FROM projetos.atividade_cronograma ac
                                     JOIN projetos.atividade_cronograma_item aci 
                                       ON aci.cd_atividade_cronograma = ac.cd_atividade_cronograma
                                    WHERE ac.dt_exclusao IS NULL
                                      AND ac.cd_responsavel = a.cod_atendente
                                      AND aci.dt_exclusao  IS NULL
                                      AND aci.cd_atividade = a.numero
                                      AND aci.cd_atividade_cronograma_grupo = ".intval($args['cd_atividade_cronograma_grupo'])."
                                      AND ac.dt_inclusao   = (SELECT MAX(ac1.dt_inclusao)
                                                                FROM projetos.atividade_cronograma ac1
                                                                JOIN projetos.atividade_cronograma_item aci1 
                                                                  ON aci1.cd_atividade_cronograma = ac1.cd_atividade_cronograma
                                                               WHERE ac1.dt_exclusao   IS NULL
                                                                 AND aci1.dt_exclusao  IS NULL
                                                                 AND aci1.cd_atividade = aci.cd_atividade))
                                         "; 
        }
        
        $cronograma_prioridade = "";
        if( isset($args['fl_prioridade']) && trim($args['fl_prioridade'])!= "")
        {
            if (trim($args['fl_prioridade']) == "S")
            {
                $cronograma_prioridade = " AND 0 < 
                                                  (SELECT COUNT(*)
                                                     FROM projetos.atividade_cronograma ac
                                                     JOIN projetos.atividade_cronograma_item aci 
                                                       ON aci.cd_atividade_cronograma = ac.cd_atividade_cronograma
                                                    WHERE ac.dt_exclusao IS NULL
                                                      AND ac.cd_responsavel = a.cod_atendente
                                                      AND aci.dt_exclusao  IS NULL
                                                      AND aci.nr_prioridade_gerente IS NOT NULL
                                                      AND aci.cd_atividade = a.numero
                                                      AND ac.dt_inclusao   = (SELECT MAX(ac1.dt_inclusao)
                                                                                FROM projetos.atividade_cronograma ac1
                                                                                JOIN projetos.atividade_cronograma_item aci1 
                                                                                  ON aci1.cd_atividade_cronograma = ac1.cd_atividade_cronograma
                                                                               WHERE ac1.dt_exclusao   IS NULL
                                                                                 AND aci1.dt_exclusao  IS NULL
                                                                                 AND aci1.cd_atividade = aci.cd_atividade))
                                         ";
            }
            
            if (trim($args['fl_prioridade']) == "N")
            {
                $cronograma_prioridade = " AND 0 = 
                                                  (SELECT COUNT(*)
                                                     FROM projetos.atividade_cronograma ac
                                                     JOIN projetos.atividade_cronograma_item aci 
                                                       ON aci.cd_atividade_cronograma = ac.cd_atividade_cronograma
                                                    WHERE ac.dt_exclusao IS NULL
                                                      AND ac.cd_responsavel = a.cod_atendente
                                                      AND aci.dt_exclusao  IS NULL
                                                      AND aci.nr_prioridade_gerente IS NULL
                                                      AND aci.cd_atividade = a.numero
                                                      AND ac.dt_inclusao   = (SELECT MAX(ac1.dt_inclusao)
                                                                                FROM projetos.atividade_cronograma ac1
                                                                                JOIN projetos.atividade_cronograma_item aci1 
                                                                                  ON aci1.cd_atividade_cronograma = ac1.cd_atividade_cronograma
                                                                               WHERE ac1.dt_exclusao   IS NULL
                                                                                 AND aci1.dt_exclusao  IS NULL
                                                                                 AND aci1.cd_atividade = aci.cd_atividade))
                                         ";
            }           
        }       


        
        $sql = "
                SELECT distinct a.numero, 
                       a.nr_prioridade,
                       a.cd_atendimento,
                       TO_CHAR(now(), 'DD/MM/YYYY - HH24:MI') AS agora,
                       TO_CHAR(a.dt_cad, 'DD/MM/YYYY') AS dt_cad,
                       dt_cad   AS data_cadastro,
                       TO_CHAR(a.dt_inicio_prev, 'dd/mm/yy') AS data_br,
                       a.dt_inicio_prev,
                       TO_CHAR(a.dt_limite, 'dd/mm/yyyy') AS data_limite,
                       TO_CHAR(a.dt_cad + interval '30' day, 'dd/mm/yyyy') AS dt_limite_doc,
                       a.dt_limite,
                       TO_CHAR(a.dt_limite_testes, 'dd/mm/yy') AS data_limite_teste,
                       a.dt_limite_testes,
                       a.dt_env_teste,
                       TO_CHAR(a.dt_fim_real, 'dd/mm/yyyy') AS data_conclusao,
                       l2.descricao AS tipo,
                       a.descricao,
                       a.area,
                       a.status_atual,
                       l.descricao as status,
					   COALESCE((SELECT COUNT(*)
						  FROM projetos.atividade_acompanhamento aa
						 WHERE aa.dt_exclusao IS NULL
						   AND aa.cd_atividade = a.numero
						 GROUP BY aa.cd_atividade), 0) AS qt_acomp,					   
                       a.sistema as sistema,
                       CASE WHEN ((current_date > a.dt_inicio_prev) AND (a.dt_inicio_real is NULL)) 
                            THEN 'S'
                            ELSE 'N'
                       END AS atrasado,
                      (SELECT ac.descricao
                         FROM projetos.atividade_cronograma ac
                         JOIN projetos.atividade_cronograma_item aci 
                           ON aci.cd_atividade_cronograma = ac.cd_atividade_cronograma
                        WHERE ac.dt_exclusao IS NULL
                          AND ac.cd_responsavel = a.cod_atendente
                          AND aci.dt_exclusao  IS NULL
                          AND aci.cd_atividade = a.numero
                          AND ac.dt_inclusao   = (SELECT MAX(ac1.dt_inclusao)
                                                    FROM projetos.atividade_cronograma ac1
                                                    JOIN projetos.atividade_cronograma_item aci1 
                                                      ON aci1.cd_atividade_cronograma = ac1.cd_atividade_cronograma
                                                   WHERE ac1.dt_exclusao   IS NULL
                                                     AND aci1.dt_exclusao  IS NULL
                                                     AND aci1.cd_atividade = aci.cd_atividade) 
                        ORDER BY ac.dt_final DESC                            
                        LIMIT 1) AS cronograma,
                      (SELECT ac.cd_atividade_cronograma
                         FROM projetos.atividade_cronograma ac
                         JOIN projetos.atividade_cronograma_item aci 
                           ON aci.cd_atividade_cronograma = ac.cd_atividade_cronograma
                        WHERE ac.dt_exclusao IS NULL
                          AND ac.cd_responsavel = a.cod_atendente
                          AND aci.dt_exclusao  IS NULL
                          AND aci.cd_atividade = a.numero
                          AND ac.dt_inclusao   = (SELECT MAX(ac1.dt_inclusao)
                                                    FROM projetos.atividade_cronograma ac1
                                                    JOIN projetos.atividade_cronograma_item aci1 
                                                      ON aci1.cd_atividade_cronograma = ac1.cd_atividade_cronograma
                                                   WHERE ac1.dt_exclusao   IS NULL
                                                     AND aci1.dt_exclusao  IS NULL
                                                     AND aci1.cd_atividade = aci.cd_atividade)
                        ORDER BY ac.dt_final DESC                            
                        LIMIT 1) AS cd_atividade_cronograma,    
                      (SELECT aci.nr_prioridade_gerente
                         FROM projetos.atividade_cronograma ac
                         JOIN projetos.atividade_cronograma_item aci 
                           ON aci.cd_atividade_cronograma = ac.cd_atividade_cronograma
                        WHERE ac.dt_exclusao IS NULL
                          AND ac.cd_responsavel = a.cod_atendente
                          AND aci.dt_exclusao  IS NULL
                          AND aci.cd_atividade = a.numero
                          AND ac.dt_inclusao   = (SELECT MAX(ac1.dt_inclusao)
                                                    FROM projetos.atividade_cronograma ac1
                                                    JOIN projetos.atividade_cronograma_item aci1 
                                                      ON aci1.cd_atividade_cronograma = ac1.cd_atividade_cronograma
                                                   WHERE ac1.dt_exclusao   IS NULL
                                                     AND aci1.dt_exclusao  IS NULL
                                                     AND aci1.cd_atividade = aci.cd_atividade)
                        ORDER BY ac.dt_final DESC                            
                        LIMIT 1) AS nr_prioridade_gerente,                                                   
                       a.cod_solicitante,
                       a.cod_atendente,
                       ua.guerra as nomeatend,
                       us.guerra as nomesolic,
                       a.cd_empresa,
                       a.cd_registro_empregado,
                       a.cd_sequencia,
                       projetos.participante_nome(a.cd_empresa, a.cd_registro_empregado, a.cd_sequencia) AS nome_participante,
                       us.divisao as div_solic,
                       pp.nome as projeto_nome,
                       CASE WHEN l.valor = 1 THEN 'blue'
                            WHEN l.valor = 2 THEN '#8B7D7B'
                            WHEN l.valor = 3 THEN 'red'
                            WHEN l.valor = 4 THEN '#FF6A00'
                            WHEN l.valor = 5 THEN '#4169E1'
                            ELSE 'green'
                       END AS status_cor,
                       CASE WHEN l.valor = 1 THEN 'label label-info'
                            WHEN l.valor = 2 THEN 'label'
                            WHEN l.valor = 3 THEN 'label label-important'
                            WHEN l.valor = 4 THEN 'label label-warning'
                            WHEN l.valor = 5 THEN 'label label-info'
                            ELSE 'label label-success'
                       END AS status_label,                    
                       (SELECT COUNT(*)
                          FROM projetos.atividade_anexo at
                         WHERE at.cd_atividade = a.numero) AS qt_anexo,
                       acl.ds_atividade_classificacao
                  FROM projetos.atividades a 
                  JOIN listas l 
                    ON l.codigo    = a.status_atual 
                   AND l.categoria = 'STAT'
                  JOIN listas l2 
                    ON l2.codigo    = a.tipo 
                   AND l2.categoria = 'TPAT'
                  JOIN projetos.usuarios_controledi ua 
                    ON ua.codigo = a.cod_atendente
                  JOIN projetos.usuarios_controledi us 
                    ON us.codigo = a.cod_solicitante
                  LEFT JOIN projetos.projetos pp 
                    ON pp.codigo = a.sistema
                  LEFT JOIN projetos.atividade_classificacao acl
                    ON acl.cd_atividade_classificacao = a.cd_atividade_classificacao
                 WHERE a.tipo <> 'L'
                    ".((trim($args['descricao']) != "") ? " AND UPPER(a.descricao) like UPPER('%".trim($args['descricao'])."%')" : "")."
                    ".((trim($args['numero']) != "") ? " AND a.numero = ".intval($args['numero']) : "")."
                    ".((trim($args['divisao_solicitante']) != "") ? " AND us.divisao = '".trim($args['divisao_solicitante'])."'" : "")."
                    ".((trim($args['projeto']) != "") ? " AND a.sistema = ".intval($args['projeto']) : "")."
                    ".((trim($args['cd_solicitante']) != "") ? " AND a.cod_solicitante = ".intval($args['cd_solicitante']) : "")."
                    ".((trim($args['cd_atendente']) != "") ? " AND a.cod_atendente = ".intval($args['cd_atendente']) : "")."
                    ".(((array_key_exists('fl_balanco_gi', $args)) AND (trim($args['cd_gerencia_atendente']) != "")) ? " AND a.area = '".trim($args['cd_gerencia_atendente'])."'" : "")."
                    ".(((array_key_exists('nr_prioridade_ini', $args)) AND (trim($args['nr_prioridade_ini']) != "") AND (trim($args['nr_prioridade_fim']) != "")) ? " AND a.nr_prioridade BETWEEN ".trim($args['nr_prioridade_ini'])." AND ".trim($args['nr_prioridade_fim']) : "")."
                    ".((trim($args['cd_empresa']) != "") ? " AND a.cd_empresa = ".intval($args['cd_empresa']) : "")."
                    ".((trim($args['cd_registro_empregado']) != "") ? " AND a.cd_registro_empregado = ".intval($args['cd_registro_empregado']) : "")."
                    ".((trim($args['seq_dependencia']) != "") ? " AND a.cd_sequencia = ".intval($args['seq_dependencia']) : "")."
                    ".((trim($args['cd_tipo_solicitacao']) != "") ? " AND a.tipo_solicitacao = '".trim($args['cd_tipo_solicitacao']."'") : "")."
                    ".(((array_key_exists('fl_balanco_gi', $args)) AND (trim($args['fl_balanco_gi']) != '')) ? " AND a.fl_balanco_gi = '".trim($args['fl_balanco_gi'])."'" : '')."
                    ".(((trim($args['dt_solicitacao_inicio']) != "") and  (trim($args['dt_solicitacao_fim']) != "")) ? " AND DATE_TRUNC('day', a.dt_cad) BETWEEN TO_DATE('".$args['dt_solicitacao_inicio']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_solicitacao_fim']."', 'DD/MM/YYYY')" : "")."
                    ".(((trim($args['dt_limite_doc_inicio']) != "") and  (trim($args['dt_limite_doc_fim']) != "")) ? " AND DATE_TRUNC('day', (a.dt_cad + interval '30' day)) BETWEEN TO_DATE('".$args['dt_limite_doc_inicio']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_limite_doc_fim']."', 'DD/MM/YYYY')" : "")."
                    ".(((trim($args['dt_envio_inicio']) != "") and  (trim($args['dt_envio_fim']) != "")) ? " AND DATE_TRUNC('day', a.dt_env_teste) BETWEEN TO_DATE('".$args['dt_envio_inicio']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_envio_fim']."', 'DD/MM/YYYY')" : "")."
                    ".(((trim($args['dt_conclusao_inicio']) != "") and  (trim($args['dt_conclusao_fim']) != "")) ? " AND DATE_TRUNC('day', a.dt_fim_real) BETWEEN TO_DATE('".$args['dt_conclusao_inicio']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_conclusao_fim']."', 'DD/MM/YYYY')" : "")."
                    ".(((array_key_exists('encaminhada', $args)) and ($args['encaminhada'] == TRUE)) ? " AND (a.forma IN (SELECT codigo FROM listas WHERE categoria = 'FDAP') OR COALESCE(a.forma,'') = '')" : "")."
                    ".(((array_key_exists('participante', $args)) and ($args['participante'] == TRUE)) ? " AND  a.cd_registro_empregado > 0" : "")."
                    ".(trim($args['cd_atividade_classificacao']) != '' ? "AND a.cd_atividade_classificacao = ".intval($args['cd_atividade_classificacao']) : "")."
                    $status
                    $feitas
                    $recebidas
                    $cronograma
                    $cronograma_prioridade
                    $cronograma_grupo
               ";

               if($this->session->userdata('codigo') == 251)
               {
                 ##echo "<pre style='text-align:left;'>".print_r($args,true)."<BR><BR>".$sql."</pre>"; exit;
               }

       

        // return result ...
        $query = $this->db->query($sql);
        $rows=$query->result_array();

        for($i=0; $i<sizeof($rows); $i++)
        {
            $q_tar=$this->db->query("
                                        SELECT t.cd_tarefa, 
                                               t.descricao, 
                                               t.fl_tarefa_tipo,
                                               t.status_atual,
                                               CASE WHEN l.valor = 1 THEN 'blue'        
                                                    WHEN l.valor = 2 THEN '#8B7D7B'
                                                    WHEN l.valor = 3 THEN 'red'
                                                    ELSE 'green'
                                               END AS status_cor                                               
                                          FROM projetos.tarefas t
                                          LEFT JOIN public.listas l
                                            ON l.codigo = t.status_atual
                                         WHERE t.cd_atividade = ".$rows[$i]['numero']." 
                                           AND t.dt_exclusao IS NULL 
                                         ORDER BY t.cd_tarefa
                                    ");
            $tarefas = $q_tar->result_array();
            $rows[$i]['tarefas'] = $tarefas;
        }

        $result = $rows;  
    }

    /**
     * Inserir solicitação de atividades da GAP para aba Solicitação
     *
     * @param array $d  campos necessários para inclusão da atividade para atendimento pela GAP, submeter um array com os seguintes campos: array('numero'=>'', 'area'=>'', 'tipo_solicitacao'=>'', 'tipo'=>'', 'titulo'=>'', 'descricao'=>'', 'problema'=>'', 'cod_atendente'=>'', 'dt_limite'=>'', 'cd_empresa'=>'', 'cd_registro_empregado'=>'', 'cd_sequencia'=>'', 'cd_plano'=>'', 'solicitante'=>'', 'forma'=>'', 'tp_envio'=>'', 'cd_atendimento'=>'', 'cod_solicitante'=>'', 'divisao'=>'');
     * @param array $e  pilha de error ocorridos
     * @return int      novo id gerado
     */
    function atendimento_solicitacao_inserir($d,&$e=array())
    {
        // valida ...

        if( trim($d['area'])=='' ){ $e[sizeof($e)] = "Campo area não informado!"; }
        if( intval($d['cod_solicitante'])==0 ){ $e[sizeof($e)] = "Campo cod_solicitante não informado!"; }
        if( trim($d['titulo'])=='' ){ $e[sizeof($e)] = "Campo titulo não informado!"; }
        if( trim($d['descricao'])=='' ){ $e[sizeof($e)] = "Campo descricao não informado!"; }
        if( intval($d['cod_atendente'])=='' ){ $e[sizeof($e)] = "Campo cod_atendente não informado!"; }
        if(trim($d['dt_limite'])!='')
        {
            $q = $this->db->query("SELECT TO_DATE(?, 'DD/MM/YYYY')-CURRENT_DATE AS dt_limite_valid",array(trim($d['dt_limite'])));
            $r = $q->row_array();
            if(intval($r['dt_limite_valid'])<0)
            {
                $e[sizeof($e)]="Valor do campo dt_limite deve ser maior que a data atual";
            }
        }

        // trata ...

        if(trim($d['dt_limite'])=='') { $dt_limite='null'; } else { $dt_limite= "TO_DATE(".$this->db->escape($d['dt_limite']).", 'DD/MM/YYYY')"; }
        if(intval($d['cd_empresa'])==0) { $cd_empresa='null'; } else { $cd_empresa=intval($d['cd_empresa']); }
        if(intval($d['cd_registro_empregado'])==0) { $cd_registro_empregado='null'; } else { $cd_registro_empregado=intval($d['cd_registro_empregado']); }
        if(trim($d['cd_sequencia'])=='') { $cd_sequencia='null'; } else { $cd_sequencia=intval($d['cd_sequencia']); }
        if(intval($d['cd_plano'])==0) { $cd_plano='null'; } else { $cd_plano=intval($d['cd_plano']); }
        if(intval($d['cd_atendimento'])==0) { $cd_atendimento='null'; } else { $cd_atendimento=intval($d['cd_atendimento']); }

        // monta ...

        $sql = "
            INSERT INTO projetos.atividades
            (
                dt_cad
                , area
                , cod_solicitante
                , tipo_solicitacao
                , tipo
                , titulo
                , descricao
                , problema
                , cod_atendente
                , dt_limite
                , cd_empresa
                , cd_registro_empregado
                , cd_sequencia
                , cd_plano
                , solicitante
                , forma
                , tp_envio
                , cd_atendimento
                , status_atual
                , divisao
            )
            VALUES
            (
                CURRENT_TIMESTAMP
                , ?                 /* area */
                , ?                 /* cod_solicitante */
                , ?                 /* tipo_solicitacao */
                , ?                 /* tipo */
                , ?                 /* titulo */
                , ?                 /* descricao */
                , ?                 /* problema */
                , ?                 /* cod_atendente */
                , $dt_limite        /* dt_limite */
                , $cd_empresa       
                , $cd_registro_empregado
                , $cd_sequencia
                , $cd_plano         
                , ?                 /* solicitante */
                , ?                 /* forma */
                , ?                 /* tp_envio */
                , $cd_atendimento
                , ?
                , ?
            )
        ";

        // roda ...

        if(sizeof($e)==0)
        {
            $q = $this->db->query($sql, array(
                $d['area']
                , intval($d['cod_solicitante'])
                , $d['tipo_solicitacao']
                , $d['tipo']
                , $d['titulo']
                , $d['descricao']
                , $d['problema']
                , intval($d['cod_atendente'])
                , $d['solicitante']
                , $d['forma']
                , intval($d['tp_envio'])
                , enum_public_listas::STAT_GAP_AGUARDANDO_INICIO
                , $d['divisao']
                ) 
            );

            if($q)
            {
                return $this->db->insert_id("projetos.atividades", "numero");
            }
            else
            {
                $e[sizeof($e)] = 'Problemas na inclusão da atividade!';
                return FALSE;
            }
        }
        else
        {
            return FALSE;
        }
    }

    /**
     * Salvar solicitação de atividades da GAP para aba Solicitação
     *
     * @param array $d  campos necessários para inclusão da atividade para atendimento pela GAP, submeter um array com os seguintes campos: array('numero'=>'', 'area'=>'', 'tipo_solicitacao'=>'', 'tipo'=>'', 'titulo'=>'', 'descricao'=>'', 'problema'=>'', 'cod_atendente'=>'', 'dt_limite'=>'', 'cd_empresa'=>'', 'cd_registro_empregado'=>'', 'cd_sequencia'=>'', 'cd_plano'=>'', 'solicitante'=>'', 'forma'=>'', 'tp_envio'=>'', 'cd_atendimento'=>'', 'cod_solicitante'=>'', 'divisao'=>'');
     * @param array $e  pilha de error ocorridos
     * @return boolean  sucesso ou falha
     */
    function atendimento_solicitacao_salvar($d,&$e=array())
    {
        // valida ...

        if( trim($d['area'])=='' ){ $e[sizeof($e)] = "Campo area não informado!"; }
        if( intval($d['cod_solicitante'])==0 ){ $e[sizeof($e)] = "Campo cod_solicitante não informado!"; }
        if( trim($d['titulo'])=='' ){ $e[sizeof($e)] = "Campo titulo não informado!"; }
        if( trim($d['descricao'])=='' ){ $e[sizeof($e)] = "Campo descricao não informado!"; }
        if( intval($d['cod_atendente'])=='' ){ $e[sizeof($e)] = "Campo cod_atendente não informado!"; }

        // trata ...

        if(trim($d['dt_limite'])=='') { $dt_limite='null'; } else { $dt_limite= "TO_DATE(".$this->db->escape($d['dt_limite']).", 'DD/MM/YYYY')"; }
        if(intval($d['cd_empresa'])==0) { $cd_empresa='null'; } else { $cd_empresa=intval($d['cd_empresa']); }
        if(intval($d['cd_registro_empregado'])==0) { $cd_registro_empregado='null'; } else { $cd_registro_empregado=intval($d['cd_registro_empregado']); }
        if(trim($d['cd_sequencia'])=='') { $cd_sequencia='null'; } else { $cd_sequencia=intval($d['cd_sequencia']); }
        if(intval($d['cd_plano'])==0) { $cd_plano='null'; } else { $cd_plano=intval($d['cd_plano']); }
        if(intval($d['cd_atendimento'])==0) { $cd_atendimento='null'; } else { $cd_atendimento=intval($d['cd_atendimento']); }

        // monta ...

        $sql = "
            UPDATE projetos.atividades
            SET area = ?
                , cod_solicitante = ?
                , tipo_solicitacao = ?
                , tipo = ?
                , titulo = ?
                , descricao = ?
                , problema = ?
                , cod_atendente = ?
                , dt_limite = $dt_limite
                , cd_empresa = $cd_empresa
                , cd_registro_empregado = $cd_registro_empregado
                , cd_sequencia = $cd_sequencia
                , cd_plano = $cd_plano
                , solicitante = ?
                , forma = ?
                , tp_envio = ?
                , cd_atendimento = $cd_atendimento
                , divisao = ?
            WHERE numero = ?
        ";

        // roda ...

        if(sizeof($e)==0)
        {
            $q = $this->db->query($sql, array(
                $d['area']
                , intval($d['cod_solicitante'])
                , $d['tipo_solicitacao']
                , $d['tipo']
                , $d['titulo']
                , $d['descricao']
                , $d['problema']
                , intval($d['cod_atendente'])
                , $d['solicitante']
                , $d['forma']
                , intval($d['tp_envio'])
                , $d['divisao']
                , intval($d['numero'])
                ) 
            );

            if($q)
            {
                return true;
            }
            else
            {
                $e[sizeof($e)] = 'Problemas na edição da atividade na aba Solicitação!';
                return FALSE;
            }
        }
        else
        {
            return FALSE;
        }
    }

    /**
     * Salvar solicitação de atividades da GAP para aba Solicitação
     *
     * @param array $d  campos necessários para inclusão da atividade para atendimento pela GAP, submeter um array com os seguintes campos: array('numero'=>'', 'area'=>'', 'tipo_solicitacao'=>'', 'tipo'=>'', 'titulo'=>'', 'descricao'=>'', 'problema'=>'', 'cod_atendente'=>'', 'dt_limite'=>'', 'cd_empresa'=>'', 'cd_registro_empregado'=>'', 'cd_sequencia'=>'', 'cd_plano'=>'', 'solicitante'=>'', 'forma'=>'', 'tp_envio'=>'', 'cd_atendimento'=>'', 'cod_solicitante'=>'', 'divisao'=>'');
     * @param array $e  pilha de error ocorridos
     * @return int      novo id gerado
     */
    function atendimento_atendimento_salvar($d,&$e=array())
    {
        // valida ...

        if( trim($d['status_atual'])=='' ){ $e[sizeof($e)] = "Campo status_atual não informado!"; }

        // trata ...

        if(intval($d['sistema'])==0) { $sistema='null'; } else { $sistema=intval($d['sistema']); }
        if(intval($d['cod_testador'])==0) { $cod_testador='null'; } else { $cod_testador=intval($d['cod_testador']); }
        if(trim($d['dt_limite_testes'])=='') { $dt_limite_testes='null'; } else { $dt_limite_testes= "TO_DATE(".$this->db->escape($d['dt_limite_testes']).", 'DD/MM/YYYY')"; }
        if(trim($d['dt_inicio_real'])=='') { $dt_inicio_real='null'; } else { $dt_inicio_real= "TO_DATE(".$this->db->escape($d['dt_inicio_real']).", 'DD/MM/YYYY')"; }
        if(trim($d['dt_fim_real'])=='') { $dt_fim_real='null'; } else { $dt_fim_real= "TO_DATE(".$this->db->escape($d['dt_fim_real']).", 'DD/MM/YYYY')"; }

        // especial

        $dt_env_teste = "NULL";
        if(trim($d['dt_limite_testes'])!="")
        {
            $q = $this->db->query('select dt_env_teste from projetos.atividades where numero=?', array($d['numero']));
            if($q)
            {
                $r=$q->row_array();
                if($r['dt_env_teste']=='')
                {
                    $dt_env_teste = "CURRENT_TIMESTAMP";
                }
                else
                {
                    $dt_env_teste = $this->db->escape($r['dt_env_teste']);
                }
            }
        }

        // monta ...

        $sql = "
            UPDATE projetos.atividades
            SET sistema = $sistema
            ,status_atual=?
            ,dt_env_teste=$dt_env_teste
            ,dt_limite_testes=$dt_limite_testes
            ,cod_testador=$cod_testador
            ,dt_inicio_real=$dt_inicio_real
            ,dt_fim_real=$dt_fim_real
            ,solucao=?
            ,complexidade=?
            ,numero_dias=?
            ,periodicidade=?
            WHERE
            numero = ?
        ";

        // roda ...

        if(sizeof($e)==0)
        {
            $q = $this->db->query($sql, array(
                $d['status_atual']
                , $d['solucao']
                , $d['complexidade']
                , intval($d['numero_dias'])
                , intval($d['periodicidade'])
                , intval($d['numero'])
                )
            );

            if($q)
            {
                return TRUE;
            }
            else
            {
                $e[sizeof($e)] = 'Problemas ao tentar salvar atividade da GAP na aba de Atendimento!';
                return FALSE;
            }
        }
        else
        {
            return FALSE;
        }
    }

    function carregar_pk($pk, &$ret, &$e=array())
    {
        $ret = array( 'numero'=>'', 'tipo'=>'', 'dt_cad'=>'', 'descricao'=>'', 'area'=>'', 'dt_inicio_prev'=>'', 'sistema'=>'', 'problema'=>'', 'solucao'=>'', 'dt_inicio_real'=>'', 'status_atual'=>'', 'complexidade'=>'', 'prioridade'=>'', 'negocio_fim'=>'', 'prejuizo'=>'', 'legislacao'=>'', 'situacao'=>'', 'dependencia'=>'', 'dias_realizados'=>'', 'cliente_externo'=>'', 'concorrencia'=>'', 'tarefa'=>'', 'tipo_solicitacao'=>'', 'numero_dias'=>'', 'dt_fim_prev'=>'', 'periodicidade'=>'', 'dt_fim_real'=>'', 'dt_deacordo'=>'', 'observacoes'=>'', 'divisao'=>'', 'origem'=>'', 'recurso'=>'', 'cod_atendente'=>'', 'cod_solicitante'=>'', 'dt_limite'=>'', 'dt_limite_testes'=>'', 'ok'=>'', 'complemento'=>'', 'num_dias_adicionados'=>'', 'titulo'=>'', 'cod_testador'=>'', 'cd_empresa'=>'', 'cd_registro_empregado'=>'', 'cd_sequencia'=>'', 'dt_retorno'=>'', 'pertinencia'=>'', 'cd_cenario'=>'', 'opt_grafica'=>'', 'opt_eletronica'=>'', 'opt_evento'=>'', 'opt_anuncio'=>'', 'opt_folder'=>'', 'opt_mala'=>'', 'opt_cartaz'=>'', 'opt_cartilha'=>'', 'opt_site'=>'', 'opt_outro'=>'', 'cores'=>'', 'formato'=>'', 'gramatura'=>'', 'quantia'=>'', 'custo'=>'', 'cc'=>'', 'pacs'=>'', 'patracs'=>'', 'nacs'=>'', 'cacs'=>'', 'lacs'=>'', 'dacs'=>'', 'forma'=>'', 'solicitante'=>'', 'cd_plano'=>'', 'dt_env_teste'=>'', 'dt_fim_real_nova'=>'', 'numero_at_origem'=>'', 'dt_implementacao_norma_legal'=>'', 'dt_prevista_implementacao_norma_legal'=>'', 'cd_recorrente'=>'', 'fl_teste_relevante'=>'', 'fl_encerrado_automatico'=>'', 'fl_teste_prorrogado'=>'', 'tp_envio'=>'', 'cd_atendimento'=>'');

        $q = $this->db->query("
        SELECT 
            numero
            , tipo
            , TO_CHAR(dt_cad, 'DD/MM/YYYY') as dt_cad
            , descricao
            , area 
            , TO_CHAR(dt_inicio_prev, 'DD/MM/YYYY') as dt_inicio_prev
            , sistema 
            , problema
            , solucao
            , TO_CHAR(dt_inicio_real, 'DD/MM/YYYY') as dt_inicio_real
            , status_atual
            , complexidade
            , prioridade
            , negocio_fim
            , prejuizo
            , legislacao
            , situacao
            , dependencia
            , dias_realizados
            , cliente_externo
            , concorrencia
            , tarefa
            , tipo_solicitacao
            , numero_dias
            , TO_CHAR(dt_fim_prev, 'DD/MM/YYYY') as dt_fim_prev
            , periodicidade
            , TO_CHAR(dt_fim_real, 'DD/MM/YYYY') as dt_fim_real
            , TO_CHAR(dt_deacordo, 'DD/MM/YYYY') as dt_deacordo
            , observacoes
            , divisao
            , origem
            , recurso
            , cod_atendente
            , cod_solicitante
            , TO_CHAR(dt_limite, 'DD/MM/YYYY') as dt_limite
            , TO_CHAR(dt_limite_testes, 'DD/MM/YYYY') as dt_limite_testes
            , ok
            , complemento
            , num_dias_adicionados
            , titulo
            , cod_testador
            , cd_empresa
            , cd_registro_empregado
            , cd_sequencia
            , to_char(dt_retorno, 'DD/MM/YYYY') as dt_retorno
            , pertinencia
            , cd_cenario
            , opt_grafica
            , opt_eletronica
            , opt_evento
            , opt_anuncio
            , opt_folder
            , opt_mala
            , opt_cartaz
            , opt_cartilha
            , opt_site
            , opt_outro
            , cores
            , formato
            , gramatura
            , quantia
            , custo
            , cc
            , pacs
            , patracs
            , nacs
            , cacs
            , lacs
            , dacs
            , forma
            , solicitante
            , cd_plano
            , to_char(dt_env_teste, 'DD/MM/YYYY') as dt_env_teste
            , to_char(dt_fim_real_nova, 'DD/MM/YYYY') as dt_fim_real_nova
            , numero_at_origem
            , TO_CHAR(dt_implementacao_norma_legal, 'DD/MM/YYYY') as dt_implementacao_norma_legal
            , TO_CHAR(dt_prevista_implementacao_norma_legal, 'DD/MM/YYYY') as dt_prevista_implementacao_norma_legal
            , cd_recorrente
            , fl_teste_relevante
            , fl_encerrado_automatico
            , fl_teste_prorrogado
            , tp_envio
            , cd_atendimento
        FROM projetos.atividades 
        WHERE numero=?;
        ", array(intval($pk)));
        
        if($q)
        {
            $col = $q->row_array();
            if(sizeof($col))
            {
                $ret = $col;
            }
            return TRUE;
        }
        else
        {
            $e[sizeof($e)] = 'Problemas com a query';
            return FALSE;
        }
    }

    function listar_encaminhada( &$result, &$count, $args=array() )
    {
        $this->load->library('pagination');
        
        // COUNT
        $sql = "
            SELECT count(*) as qtd
            FROM 
                projetos.atividades a
                JOIN listas l 
                ON l.codigo = a.status_atual
                AND l.categoria  = 'STAT'
                
                JOIN listas l2 
                ON l2.codigo = a.tipo
                AND l2.categoria = 'TPAT'
                
                JOIN projetos.usuarios_controledi ua 
                ON ua.codigo = a.cod_atendente
                
                JOIN projetos.usuarios_controledi us 
                ON us.codigo = a.cod_solicitante
            WHERE 
                a.forma IN (SELECT codigo FROM listas WHERE categoria = 'FDAP')
                AND (cod_testador = 191 OR cod_solicitante = 191 OR a.cod_atendente = 191)
                AND a.status_atual in ( 'AINI','ADIR','AMAN','AUSR','AIST', 'CAAI', 'AICS', 'EANA','EMAN','EMST', 'CAEN', 'EECS' , 'ETES', 'AOCS', 'EERH', 'AIRH', 'AIDI', 'EEDI', 'GFMN' )
        ";

        $query = $this->db->query($sql);
        $row = $query->row_array(0);
        $count = $row['qtd'];

        $this->setup_pagination($count);

        // RESULTS
        $sql = "
            SELECT 
                distinct a.numero, 
                TO_CHAR(now(), 'DD/MM/YYYY - HH24:MI') AS agora,
                TO_CHAR(a.dt_cad, 'dd/mm/yy') AS dt_cad,
                dt_cad  AS data_cadastro,
                TO_CHAR(a.dt_inicio_prev, 'dd/mm/yy') AS data_br,
                a.dt_inicio_prev,
                TO_CHAR(a.dt_limite, 'dd/mm/yy') AS data_limite,
                a.dt_limite,
                TO_CHAR(a.dt_limite_testes, 'dd/mm/yy') AS data_limite_teste,
                a.dt_limite_testes,
                TO_CHAR(a.dt_fim_real, 'dd/mm/yy') AS data_conclusao,
                l2.descricao AS tipo,
                a.descricao,
                a.area,
                a.status_atual,
                l.descricao as status,
                a.sistema as sistema,
                CASE WHEN ((current_date > a.dt_inicio_prev) AND (a.dt_inicio_real is NULL)) 
                THEN 'S'
                ELSE 'N'
                END AS atrasado,
                a.cod_solicitante,
                a.cod_atendente,
                ua.guerra as nomeatend,
                us.guerra as nomesolic,
                us.divisao as div_solic
            FROM 

                projetos.atividades a
                JOIN listas l 
                ON l.codigo = a.status_atual
                AND l.categoria  = 'STAT'
                
                JOIN listas l2 
                ON l2.codigo = a.tipo
                AND l2.categoria = 'TPAT'
                
                JOIN projetos.usuarios_controledi ua 
                ON ua.codigo = a.cod_atendente
                
                JOIN projetos.usuarios_controledi us 
                ON us.codigo = a.cod_solicitante
            WHERE 
                a.forma IN (SELECT codigo FROM listas WHERE categoria = 'FDAP')
                AND (cod_testador = 191 OR cod_solicitante = 191 OR a.cod_atendente = 191)
                AND a.status_atual in ( 'AINI','ADIR','AMAN','AUSR','AIST', 'CAAI', 'AICS', 'EANA','EMAN','EMST', 'CAEN', 'EECS' , 'ETES', 'AOCS', 'EERH', 'AIRH', 'AIDI', 'EEDI', 'GFMN' )

            GROUP BY 
                a.numero, a.sistema, a.dt_inicio_prev, a.descricao, a.divisao, a.dt_cad,  a.dt_limite_testes, a.dt_limite, a.dt_fim_real, a.tipo, a.area, l.descricao, a.status_atual, a.dt_inicio_real, a.cod_solicitante, a.cod_atendente , ua.guerra, us.guerra, us.divisao, l2.descricao  ORDER BY a.numero DESC, sistema, data_br, a.descricao

            LIMIT " . $this->pagination->per_page . " OFFSET " . $args["page"] . "
        ";

        $result = $this->db->query($sql);
    }
    
    function listar_legal( &$result, $args=array() )
    {
        $pertinencia = "";
        $operador    = "";
        
        if( $args['nao_verificado']=="S" )
        {
            $pertinencia = " a.pertinencia IS NULL AND a.status_atual = 'AIGC'";
            $operador = " OR ";
        }
        
        if( $args['nao_pertinente']=="S" )
        {
            $pertinencia .= $operador . " a.pertinencia = '0'";
            $operador = " OR ";
        }
        
        if( $args['pertinente_sem_reflexo']=="S" )
        {
            $pertinencia .= $operador . " a.pertinencia = '1'";
            $operador = " OR ";
        }
        
        if( $args['pertinente_com_reflexo']=="S" )
        {
            $pertinencia .= $operador . " a.pertinencia = '2'";
            $operador = " OR ";
        }
    
        $qr_sql = "
                    SELECT uc.guerra AS atendente,
                           TO_CHAR(a.dt_cad, 'DD/MM/YYYY') AS data,
                           TO_CHAR(a.dt_implementacao_norma_legal, 'DD/MM/YYYY') AS dt_implementacao_norma_legal,
                           TO_CHAR(a.dt_prevista_implementacao_norma_legal, 'DD/MM/YYYY') AS dt_prevista_implementacao_norma_legal,
                           a.numero,
                           a.area,
                           a.descricao,
                           a.cd_cenario,
                           c.cd_edicao,
                           CASE WHEN (a.status_atual = 'CAGC') THEN
                                                                (
                                                                SELECT ah.observacoes
                                                                  FROM projetos.atividade_historico ah
                                                                 WHERE ah.cd_atividade = a.numero
                                                                   AND ah.status_atual = 'CAGC'
                                                                 ORDER BY ah.codigo DESC 
                                                                 LIMIT 1
                                                                )
                                WHEN (a.status_atual = 'RAGC') THEN
                                                                (
                                                                SELECT ah.observacoes
                                                                  FROM projetos.atividade_historico ah
                                                                 WHERE ah.cd_atividade = a.numero
                                                                   AND ah.status_atual = 'RAGC'
                                                                 ORDER BY ah.codigo DESC 
                                                                 LIMIT 1
                                                                )
                                WHEN (a.pertinencia = '0') THEN 'Não pertinente'
                                WHEN (a.pertinencia = '1') THEN 'Pertinente, mas não altera processo'
                                WHEN (a.pertinencia = '2') THEN 'Pertinente e altera processo'
                                ELSE 'Não verificado'
                           END AS pertinencia,
                           CASE WHEN (a.status_atual = 'CAGC') THEN 'gray'
                                WHEN (a.status_atual = 'RAGC') THEN 'gray'
                                WHEN (a.pertinencia = '0')     THEN 'black'
                                WHEN (a.pertinencia = '1')     THEN 'green'
                                WHEN (a.pertinencia = '2')     THEN 'blue'
                                ELSE 'orange'
                           END AS cor,
                           CASE WHEN (a.status_atual = 'CAGC') THEN ''
                                WHEN (a.status_atual = 'RAGC') THEN ''
                                WHEN (a.pertinencia = '0')     THEN 'label-inverse'
                                WHEN (a.pertinencia = '1')     THEN 'label-success'
                                WHEN (a.pertinencia = '2')     THEN 'label-info'
                                ELSE 'label-important'
                           END AS cor_status                    
                      FROM projetos.atividades a 
                      JOIN projetos.usuarios_controledi uc
                        ON a.cod_atendente = uc.codigo
                      LEFT JOIN projetos.cenario c
                        ON c.cd_cenario = a.cd_cenario
                     WHERE a.tipo = 'L'
                       AND a.cod_atendente = ".intval($args['cd_usuario'])."
                         ".(((trim($args['dt_ini']) != "") AND (trim($args['dt_fim']) != "")) ? "AND CAST(a.dt_cad AS DATE) BETWEEN TO_DATE('".trim($args['dt_ini'])."','DD/MM/YYYY') AND TO_DATE('".trim($args['dt_fim'])."','DD/MM/YYYY') " : "")."
                         ".(trim($pertinencia) != "" ? "AND (".$pertinencia.")" : "")."
                     ORDER BY a.numero;
                  ";
        $result = $this->db->query($qr_sql);
    }
    
    private function setup_pagination($count)
    {
        // Setup pagination
        $config['enable_query_strings'] = FALSE;
        $config['base_url'] = $this->config->item('base_url') . 'index.php/atividade/legal/index';
        $config['per_page'] = 10000;
        $config['total_rows'] = $count;
        $this->pagination->initialize($config);
    }
    
    public function cronograma_grupos(&$result, $args=array())
    {
        $qr_sql = "
            SELECT cd_atividade_cronograma_grupo AS value,
                   ds_atividade_cronograma_grupo AS text
              FROM projetos.atividade_cronograma_grupo
             WHERE dt_exclusao IS NULL";
             
        $result = $this->db->query($qr_sql);
    }
    
    public function divisao_solicitante(&$result, $args=array())
    {
        $qr_sql = "
            SELECT distinct codigo AS value, 
                   nome AS text 
              FROM projetos.divisoes a 
              JOIN projetos.atividades b 
                ON a.codigo = b.divisao 
             ORDER BY nome ASC";
             
        $result = $this->db->query($qr_sql);
    }
    
    public function projetos(&$result, $args=array())
    {
        $qr_sql = "
            SELECT codigo AS value, 
                   nome AS text 
              FROM projetos.projetos 
             WHERE codigo IN
                 (
                   SELECT DISTINCT(a.sistema) 
                     FROM projetos.atividades a, listas l1, listas l2 
                    WHERE l1.codigo = a.status_atual 
                      AND l1.categoria = 'STAT' 
                      AND l2.categoria = 'TPAT' 
                      AND l2.codigo = a.tipo
                 ) 
            ORDER BY nome";
             
        $result = $this->db->query($qr_sql);
    }
    
    public function solicitacao(&$result, $args=array())
    {
        $qr_sql = "
            SELECT tm.codigo AS value,
                   tm.divisao || ' - ' || tm.descricao AS text
              FROM public.listas tm
              JOIN projetos.divisoes d
                ON d.codigo = tm.divisao
             WHERE tm.categoria   = 'TPMN' 
               AND tm.dt_exclusao IS NULL
               AND tm.divisao     <> '*'
             ORDER BY tm.divisao ASC,
                      tm.descricao ASC";
             
        $result = $this->db->query($qr_sql);
    }
    
    public function solicitante(&$result, $args=array())
    {
        $qr_sql = "
            SELECT distinct a.codigo AS value, 
                   a.nome AS text
              FROM projetos.usuarios_controledi a
              JOIN projetos.atividades b 
                ON a.codigo = b.cod_solicitante
             ORDER BY a.nome";
             
        $result = $this->db->query($qr_sql);
    }
    
    public function atendente(&$result, $args=array())
    {
        $qr_sql = "
            SELECT distinct a.codigo AS value, 
                   a.nome AS text
              FROM projetos.usuarios_controledi a
              JOIN projetos.atividades b 
                ON a.codigo = b.cod_atendente
             ORDER BY a.nome";
             
        $result = $this->db->query($qr_sql);
    }
    
    public function area_atendente(&$result, $args=array())
    {
        $qr_sql = "
            SELECT DISTINCT a.area AS value,
                   d.nome AS text 
              FROM projetos.atividades a
              JOIN projetos.divisoes d
                ON d.codigo = a.area
            WHERE a.tipo <> 'L';";
             
        $result = $this->db->query($qr_sql);
    
    }
	
    function notificacao(&$result, $args=array())
    {
        $qr_sql = "
					SELECT COUNT(*) AS qt_atividade
					  FROM projetos.atividades a
					 WHERE a.tipo          <> 'L'
					   AND a.dt_fim_real   IS NULL					
					   AND (a.cod_atendente = ".intval($args["cd_usuario"])." OR a.cd_substituto = ".intval($args["cd_usuario"]).")
					  -- AND (CASE WHEN a.area = 'GI' THEN a.status_atual NOT IN ('AUSR','ETES') ELSE 1 = 1 END )
             AND a.status_atual NOT IN ('AUSR','ETES', 'SUSP')
			      ";
             
        $result = $this->db->query($qr_sql);
    }	
}
?>