<?php
class atividade_atendimento_model extends Model
{
    #### STATUS DE AGUARDANDO INICIO ####
    var $ar_status_inicio = Array('AINI','AIST', 'AICS', 'AINF', 'GCAI', 'AIGA','AISB','AIGJ','SGAI', 'AIRH', 'AIDI', 'CAAI');  
    
    #### STATUS DE CONCLUIDO ####
    var $ar_status_concluido = Array('COSB','COST','CONC', 'CACO','CONF','COGA','COCS','GCCO','CAGJ','COGJ','SGCA','SGCO','COGD','ACRH');        
    
    #### STATUS QUE DEFINE FIM REAL ####
    var $ar_status_fim_real = Array('CAST','COST','CONC','COGA','CAGJ','COGJ','GCCA','GCCO','CANF','CONF','CANC','AGDF','COCS','CACS','COSB','CASB','SGCA','SGCO','CAGD','COGD', 'CARH', 'ACRH', 'ACDI', 'CADI', 'CACA', 'CACO');
        
    function __construct()
    {
        parent::Model();
    }
    
    function atividade(&$result, $args=array())
    {
        $qr_sql = "
                    SELECT a.numero,
                           a.area AS cd_gerencia_destino,
                           funcoes.get_usuario_area(a.cod_solicitante::integer) AS cd_gerencia_solicitante,
                           a.nr_prioridade,
                           a.sistema,
                           a.cod_solicitante,
                           a.tipo_solicitacao,
                           TO_CHAR(a.dt_cad, 'DD/MM/YYYY HH24:MI:SS') AS dt_cad,
                           TO_CHAR(a.dt_cad, 'DD/MM/YYYY') AS dt_cadastro,
                           a.tipo AS tipo_ativ,
                           a.status_atual,
                           a.cod_atendente,
                           COALESCE(a.complexidade,'0001') AS complexidade,
                           a.descricao,
                           a.problema,
                           a.solucao,
                           a.negocio_fim,
                           a.prejuizo,
                           a.legislacao,
                           a.cliente_externo,
                           a.concorrencia,
                           a.ok,
                           a.complemento,
                           a.num_dias_adicionados,
                           a.numero_dias,
                           a.periodicidade,
                           a.cd_substituto,
						   a.cd_atividade_classificacao,
                           COALESCE(a.cod_testador, a.cod_solicitante) AS cod_testador,
                           TO_CHAR(a.dt_implementacao_norma_legal, 'DD/MM/YYYY') AS dt_implementacao_norma_legal,
                           TO_CHAR(a.dt_prevista_implementacao_norma_legal, 'DD/MM/YYYY') AS dt_prevista_implementacao_norma_legal,
                           a.pertinencia,
                           a.cd_cenario,
                           a.fl_balanco_gi,
                           TO_CHAR(a.dt_inicio_prev, 'DD/MM/YYYY') AS dt_inicio_prev,
                           TO_CHAR(a.dt_fim_prev, 'DD/MM/YYYY') AS dt_fim_prev,
                           TO_CHAR(a.dt_inicio_real, 'DD/MM/YYYY') AS dt_inicio_real,
                           TO_CHAR(a.dt_fim_real, 'DD/MM/YYYY HH24:MI:SS') AS dt_fim_real,
                           TO_CHAR(a.dt_env_teste, 'DD/MM/YYYY HH24:MI:SS') AS dt_env_teste,
                           TO_CHAR(a.dt_limite_testes, 'DD/MM/YYYY') AS dt_limite_teste,
                           TO_CHAR(a.dt_limite, 'DD/MM/YYYY') AS dt_limite,
                           TO_CHAR(a.dt_aguardando_usuario, 'DD/MM/YYYY HH24:MI:SS') AS dt_aguardando_usuario,
                           TO_CHAR(a.dt_aguardando_usuario_limite, 'DD/MM/YYYY') AS dt_aguardando_usuario_limite,
                           a.fl_teste_relevante,
                           (SELECT COUNT(*)
                              FROM projetos.atividade_anexo aa
                             WHERE aa.dt_exclusao IS NULL
                               AND aa.cd_atividade = a.numero) AS qt_anexo,
                           d.codigo || ' - ' || d.nome AS gerencia_destino,
                           l.descricao AS status_atividade,
                           CASE WHEN l.valor = 1 THEN 'label label-info'
                                WHEN l.valor = 2 THEN 'label'
                                WHEN l.valor = 3 THEN 'label label-important'
                                WHEN l.valor = 4 THEN 'label label-warning'
                                WHEN l.valor = 5 THEN 'label label-info'
                                ELSE 'label label-success'
                           END AS class_status,
                           ats.cd_atividade_solucao,
                           ats.cd_categoria AS cd_solucao_categoria,
                           ats.ds_assunto AS ds_solucao_assunto,
                           a.cd_empresa,
                           a.cd_registro_empregado,
                           a.cd_sequencia
                      FROM projetos.atividades a
                      LEFT JOIN projetos.divisoes d
                        ON d.codigo = a.area
                      LEFT JOIN listas l
                        ON l.categoria = 'STAT' 
                       AND l.codigo    = a.status_atual
                       AND l.divisao   = a.area 
                      LEFT JOIN projetos.atividade_solucao ats
                        ON ats.cd_atividade = a.numero
                     WHERE a.numero = ".intval($args["cd_atividade"])."
                  ";
        #echo "<PRE> $qr_sql </PRE>";
        $result = $this->db->query($qr_sql);
    }

    function sugerirDataTeste(&$result, $args=array())
    {
        $qr_sql = "
                    SELECT TO_CHAR(funcoes.dia_util('DEPOIS', CURRENT_DATE, 5),'DD/MM/YYYY') AS dt_sugerida
                  ";
        $result = $this->db->query($qr_sql);
    }      

    function cb_classificacao(&$result, $args=array())
    {
        $qr_sql = "
                    SELECT cd_atividade_classificacao AS value, 
					       ds_atividade_classificacao AS text
					  FROM projetos.atividade_classificacao
					 ORDER BY text
                  ";
        $result = $this->db->query($qr_sql);
    }	
    
    function cb_sistema(&$result, $args=array())
    {
        $qr_sql = "
                    SELECT codigo AS value, 
                           nome AS text
                      FROM projetos.projetos 
                     WHERE (area         = '".$args['cd_gerencia_destino']."' ".(trim($args['cd_gerencia_destino']) == 'GI' ? "OR area = 'GGS' OR area = 'GTI'" : "")." )
                       AND dt_exclusao  IS NULL 
                       AND fl_atividade = 'S' 
                     ORDER BY text
                  ";
        $result = $this->db->query($qr_sql);
    }

    function cb_status_atual(&$result, $args=array())
    {
        if(strtoupper(trim($args['cd_gerencia_destino'])) == 'GS-RH')
        {
            $args['cd_gerencia_destino'] = 'GC-RH';
        }

        if((trim($args['cd_gerencia_destino']) == "GRI"))
        {
            if(trim($args['status_atual']) == 'APCS')
            {
                $qr_sql = "
                            SELECT l.codigo AS value, 
                                   l.descricao AS text
                              FROM public.listas l
                             WHERE l.categoria  = 'STAT' 
                               AND l.dt_exclusao IS NULL 
                               AND l.divisao    = '".trim($args['cd_gerencia_destino'])."' 
                               AND l.codigo IN ('APCS', 'COCS', 'CACS' )
                             ORDER BY text
                          ";            
            }
            elseif(in_array(trim($args['status_atual']), $this->ar_status_fim_real))
            {
                $qr_sql = "
                            SELECT l.codigo AS value, 
                                   l.descricao AS text
                              FROM public.listas l
                             WHERE l.categoria  = 'STAT' 
                               AND l.dt_exclusao IS NULL 
                               AND l.divisao    = '".trim($args['cd_gerencia_destino'])."' 
                               AND l.codigo     = '".trim($args['status_atual'])."'
                             ORDER BY text
                          ";            
            }
            else
            {
                $qr_sql = "
                            SELECT l.codigo AS value, 
                                   l.descricao AS text
                              FROM public.listas l
                             WHERE l.categoria  = 'STAT' 
                               AND l.dt_exclusao IS NULL 
                               AND l.divisao    = '".trim($args['cd_gerencia_destino'])."' 
                               AND l.codigo     NOT IN ('CONC', 'COCS', 'APCS' ) 
                             ORDER BY text
                          ";            
            }
        }
        else
        {
            $qr_sql = "
                        SELECT l.codigo AS value, 
                               l.descricao AS text
                          FROM public.listas l
                         WHERE l.categoria  = 'STAT' 
                           AND (CASE WHEN l.divisao = 'GC' THEN COAlESCE(l.tipo,'') <> 'N' ELSE TRUE END)
                           AND l.dt_exclusao IS NULL 
                           AND l.divisao    = '".trim($args['cd_gerencia_destino'])."' 
                           ".(trim($args['dt_fim_real']) == "" ? "AND l.codigo NOT IN ('CONC')" : "")."
                         ORDER BY text
                      ";
        }
        
        $result = $this->db->query($qr_sql);
    }   
    
    function cb_testador(&$result, $args=array())
    {
        $qr_sql = "
                    SELECT uc.codigo AS value,
                           uc.nome AS text
                      FROM projetos.usuarios_controledi uc
                     WHERE (uc.tipo <> 'X' OR uc.codigo IN (".intval($args['cod_testador'])."))
                       --AND uc.tipo NOT IN ('E','T')
                     ORDER BY text
                  ";
        $result = $this->db->query($qr_sql);
    }   

    function cb_complexidade(&$result, $args=array())
    {
        $qr_sql = "
                    SELECT codigo AS value,
                           descricao AS text
                      FROM public.listas 
                     WHERE categoria = 'CPLX' 
                     ORDER BY value
                  ";
        $result = $this->db->query($qr_sql);
    }
    
    function cb_solucao(&$result, $args=array())
    {
        $qr_sql = "
                    SELECT codigo AS value,
                           descricao AS text
                      FROM public.listas
                     WHERE divisao   = '".$args['cd_gerencia_destino']."' 
                       AND categoria = 'SOLU'
                     ORDER BY text
                  ";
        $result = $this->db->query($qr_sql);
    }   

    function cronogramaCombo(&$result, $args=array())
    {
        $qr_sql = "
                    SELECT ac.cd_atividade_cronograma AS value, 
                           ac.descricao AS text
                      FROM projetos.atividade_cronograma ac
                     WHERE ac.cd_responsavel = ".intval($args['cod_atendente'])."
                       AND ac.dt_exclusao    IS NULL
                       AND 0 = (SELECT COUNT(*)
                                  FROM projetos.atividade_cronograma_item aci
                                 WHERE aci.cd_atividade_cronograma = ac.cd_atividade_cronograma
                                   AND aci.cd_atividade  = ".intval($args['cd_atividade'])."
                                   AND aci.dt_exclusao   IS NULL)
                       AND ac.dt_encerra IS NULL
                     ORDER BY text
                  ";
        $result = $this->db->query($qr_sql);
    }
    
    function cronogramaListar(&$result, $args=array())
    {
        $qr_sql = "
                    SELECT ac.cd_atividade_cronograma, 
                           aci.cd_atividade_cronograma_item, 
                           (ac.descricao || ' - ' || uc.guerra) AS descricao,
                           ac.dt_encerra
                      FROM projetos.atividade_cronograma ac
                      JOIN projetos.atividade_cronograma_item aci
                        ON aci.cd_atividade_cronograma = ac.cd_atividade_cronograma
                      JOIN projetos.usuarios_controledi uc
                        ON uc.codigo = ac.cd_responsavel
                     WHERE aci.cd_atividade  = ".intval($args['cd_atividade'])."
                       AND ac.dt_exclusao    IS NULL
                       AND aci.dt_exclusao   IS NULL
                     ORDER BY ac.descricao                  
                  ";
        $result = $this->db->query($qr_sql);
    }   
    
    function tarefaListar(&$result, $args=array())
    {
        $qr_sql = "
                    SELECT t.cd_tarefa,
                           t.cd_atividade,                  
                           u.nome AS ds_responsavel, 
                           t.programa AS programa, 
                           t.resumo, 
                           t.status_atual AS st_tarefa,
                           h.status_atual AS st_historico,
                           TO_CHAR(t.dt_inicio_prog, 'DD/MM/YYYY HH24:MI:SS') AS dt_inicio_prog,
                           TO_CHAR(t.dt_fim_prog, 'DD/MM/YYYY HH24:MI:SS') AS dt_fim_prog,
                           TO_CHAR(t.dt_ok_anal, 'DD/MM/YYYY HH24:MI:SS') AS dt_ok_anal,
                           CASE WHEN (h.status_atual='AMAN') THEN 'Aguardando Manutenção' 
                                WHEN (h.status_atual='EMAN') THEN 'Em Manutenção' 
                                WHEN (h.status_atual='AINI') THEN 'Aguardando Início' 
                                WHEN (h.status_atual='LIBE') THEN 'Liberada' 
                                WHEN (h.status_atual='CONC') THEN 'Concluída'
                                WHEN (h.status_atual='CANC') THEN 'Cancelada'
                                WHEN (h.status_atual='AGDF') THEN 'Aguardando Definição'
                                WHEN (h.status_atual='SUSP' AND (SELECT status_atual FROM projetos.atividades WHERE numero = t.cd_atividade)='ADIR') THEN 'Atividade Aguardando Diretoria'
                                WHEN (h.status_atual='SUSP' AND (SELECT status_atual FROM projetos.atividades WHERE numero = t.cd_atividade)='AUSR') THEN 'Atividade Aguardando Usuário'
                                WHEN (h.status_atual='SUSP' AND (SELECT status_atual FROM projetos.atividades WHERE numero = t.cd_atividade)='SUSP') THEN 'Atividade Suspensa'
                                WHEN (h.status_atual='SUSP') THEN 'Em Manutenção (Pausa)'
                           END as status,
                           t.fl_tarefa_tipo
                      FROM projetos.tarefas t, 
                           projetos.usuarios_controledi u, 
                           projetos.tarefa_historico h 
                     WHERE t.cd_atividade = ".intval($args['cd_atividade'])."
                       AND t.cd_recurso   = u.codigo 
                       AND t.cd_atividade = h.cd_atividade
                       AND t.cd_tarefa    = h.cd_tarefa
                       AND h.dt_inclusao  = (SELECT MAX(dt_inclusao)
                                               FROM projetos.tarefa_historico
                                              WHERE cd_atividade = h.cd_atividade
                                                AND cd_tarefa    = h.cd_tarefa)
                       AND t.dt_exclusao IS NULL
                     ORDER BY t.cd_tarefa DESC,
                              t.dt_inicio_prog              
                  ";
        $result = $this->db->query($qr_sql);
    }

    function salvar(&$result, $args=array())
    {   
        #### ATUARIAL - LIMPA DATA LIMITE ####
        $dt_limite = "dt_limite";
        if(trim($args['status_atual']) == "ICGA")
        {
            $dt_limite = "NULL";
        }       
        
        #### INFORMATICA / COMUNICACAO - DEFINE A DATA DE ENVIO PARA TESTE ####
        if(trim($args['dt_env_teste']) != "")
        {
            $args['dt_env_teste'] = "dt_env_teste";
        }
        elseif((trim($args['dt_env_teste']) == "") AND (trim($args['status_atual']) == "ETES"))
        {
            $args['dt_env_teste'] = "CURRENT_TIMESTAMP";
        }
        elseif((trim($args['dt_env_teste']) == "") AND (trim($args['status_atual']) == "AOCS"))
        {
            $args['dt_env_teste'] = "CURRENT_TIMESTAMP";
        }       
        else
        {
            $args['dt_env_teste'] = "DEFAULT";
        }
        
        #### DEFINE A DATA FIM REAL ####
        $dt_fim_real = "dt_fim_real";
        if(in_array(trim($args['status_atual']), $this->ar_status_fim_real))
        {
            $dt_fim_real = "COALESCE(dt_fim_real, CURRENT_TIMESTAMP)";
        }
        
        $qr_sql = "
                    UPDATE projetos.atividades 
                       SET sistema            = ".(trim($args['sistema']) != '' ? intval($args['sistema']) : "DEFAULT").",
                           status_atual       = ".(trim($args['status_atual']) != '' ? "'".trim($args['status_atual'])."'" : "DEFAULT").",
                           solucao            = ".(trim($args['solucao']) != '' ? "'".trim($args['solucao'])."'" : "DEFAULT").",
                           complexidade       = ".(trim($args['complexidade']) != '' ? "'".trim($args['complexidade'])."'" : "DEFAULT").",
                           dt_inicio_real     = ".(trim($args['dt_inicio_real']) != '' ? "TO_DATE('".$args['dt_inicio_real']."', 'DD/MM/YYYY')" : "DEFAULT").",
                           dt_inicio_prev     = ".(trim($args['dt_inicio_prev']) != '' ? "TO_DATE('".$args['dt_inicio_prev']."', 'DD/MM/YYYY')" : "DEFAULT").",
                           dt_fim_prev        = ".(trim($args['dt_fim_prev']) != '' ? "TO_DATE('".$args['dt_fim_prev']."', 'DD/MM/YYYY')" : "DEFAULT").",
                           dt_env_teste       = ".$args['dt_env_teste'].",
                           fl_teste_relevante = ".(trim($args['fl_teste_relevante']) != '' ? "'".trim($args['fl_teste_relevante'])."'" : "DEFAULT").",
                           dt_limite_testes   = ".(trim($args['dt_limite_teste']) != '' ? "TO_DATE('".$args['dt_limite_teste']."', 'DD/MM/YYYY')" : "DEFAULT").",
                           cod_testador       = ".(trim($args['cod_testador']) != '' ? intval($args['cod_testador']) : "DEFAULT").",
                           cd_atividade_classificacao = ".(intval($args['cd_atividade_classificacao']) > 0 ? intval($args['cd_atividade_classificacao']) : "DEFAULT").",
                           fl_balanco_gi      = ".(trim($args['fl_balanco_gi']) != '' ? "'".trim($args['fl_balanco_gi'])."'" : "DEFAULT").",    
                           dt_limite          = ".$dt_limite.",
                           dt_fim_real        = ".$dt_fim_real."
                     WHERE numero = ".intval($args['numero']).";
                  ";
                  
        #### VERIFICA TROCA DE STATUS - GRAVA HISTORICO - ENVIA EMAIL ####
        if(trim($args['status_atual']) != trim($args['status_anterior']))
        {
            $qr_sql.= "
                        INSERT INTO projetos.atividade_historico 
                             ( 
                                cd_atividade, 
                                cd_recurso, 
                                dt_inicio_prev,
                                status_atual,
                                observacoes 
                             )
                        VALUES 
                             ( 
                                ".intval($args['numero']).", 
                                ".intval($args['cd_usuario']).",
                                CURRENT_TIMESTAMP,
                                ".(trim($args['status_atual']) != '' ? "'".trim($args['status_atual'])."'" : "DEFAULT").",
                                'Troca de Status'
                             );
                      ";

            #### ENVIAR EMAIL ####
            $quebra = chr(10);
            
            $assunto = "'(' || UPPER(COALESCE(l.descricao, '')) || ') Alteração de Situação da Atividade nº ".intval($args['numero'])."'";
            $para    = "COALESCE(funcoes.get_usuario(COALESCE(a.cod_testador, a.cod_solicitante)) || '@eletroceee.com.br;','') || COALESCE((funcoes.get_usuario(a.cod_solicitante)) || '@eletroceee.com.br','') ||
                       (CASE WHEN cod_solicitante = 287 THEN ';' || funcoes.get_usuario(40) || '@eletroceee.com.br;' || funcoes.get_usuario(75) || '@eletroceee.com.br'  
                         ELSE ''
                   END)";
            $cc      = "COALESCE((funcoes.get_usuario(a.cod_atendente)) || '@eletroceee.com.br','')";
            
            if((trim($args['status_atual']) == "ETES") AND (intval($args['sistema']) == 204))
            {
                $assunto = "'ATENÇÃO: ALTERAÇÃO NO CONTRATO DE EMPRÉSTIMO (' || UPPER(COALESCE(l.descricao, '')) || ') Atividade nº ".intval($args['numero'])."'";
                $cc.= "';gpsuporte@eletroceee.com.br;alongaray@eletroceee.com.br;rtortorelli@eletroceee.com.br;amedeiros@eletroceee.com.br'";
            }

            $mensagem = "Prezado(a): ' || COALESCE(funcoes.get_usuario_nome(COALESCE(a.cod_testador, a.cod_solicitante)),'') || '".$quebra.$quebra;
            $mensagem.= "Foi alterado o status da atividade.".$quebra.$quebra;
            
            if(trim($args['status_atual']) == "AUSR")
            {
                $mensagem.= "*************************************************************************************************".$quebra;
                $mensagem.= "A ATIVIDADE ESTÁ AGUARDANDO USUÁRIO, a data limite para retorno é ' || COALESCE(TO_CHAR(a.dt_aguardando_usuario_limite, 'DD/MM/YYYY'),'') || ', após será ENCERRADA".$quebra;
                $mensagem.= "*************************************************************************************************".$quebra;
            }           
            
            $mensagem.= "-------------------------------------------------------------".$quebra;
            $mensagem.= "Atividade: ".intval($args['numero']).$quebra;
            $mensagem.= "Solicitante: ' || COALESCE(funcoes.get_usuario_nome(a.cod_solicitante),'') || '".$quebra;
            $mensagem.= "Atendente: ' || COALESCE(funcoes.get_usuario_nome(a.cod_atendente),'') || '".$quebra;
            $mensagem.= "Situação: ' || UPPER(COALESCE(l.descricao, '')) || '".$quebra;

            #### FALTA INCLUIR INFORMAÇÃO DE ANEXOS ####
            $mensagem.= "-------------------------------------------------------------".$quebra;
            $mensagem.= "DATA LIMITE PARA TESTES: ' || COALESCE(TO_CHAR(a.dt_limite_testes, 'DD/MM/YYYY'),'') || '".$quebra;
            $mensagem.= "Testador: ' || COALESCE(funcoes.get_usuario_nome(COALESCE(a.cod_testador, a.cod_solicitante)),'') || '".$quebra;
            $mensagem.= "-------------------------------------------------------------".$quebra;
            $mensagem.= "Descrição:".$quebra."' || COALESCE(a.descricao,'') || '".$quebra;
            $mensagem.= "-------------------------------------------------------------".$quebra;
            $mensagem.= "Link para Atividade: ".$quebra;
            $mensagem.= site_url('atividade/atividade_solicitacao/index/'.trim($args['cd_gerencia_destino']).'/'.intval($args['numero'])).$quebra;
            $mensagem.= "-------------------------------------------------------------".$quebra;        
            $mensagem.= "Justificativa da Manutenção: ".$quebra."' || COALESCE(a.problema,'') || '".$quebra;
            $mensagem.= "-------------------------------------------------------------".$quebra;
            $mensagem.= "Descrição da Manutenção: ".$quebra."' || COALESCE(a.solucao,'') || '".$quebra;
            $mensagem.= "-------------------------------------------------------------".$quebra;
            $mensagem.= "Observações: ".$quebra."' || COALESCE(a.observacoes,'') || '".$quebra;
            $mensagem.= "-------------------------------------------------------------".$quebra;            
            
            $qr_sql.= "
                        INSERT INTO projetos.envia_emails 
                             ( 
                               dt_envio, 
                               de,
                               para, 
                               cc,  
                               cco, 
                               assunto,
                               texto,
                               cd_evento     
                             ) 
                        SELECT CURRENT_TIMESTAMP AS dt_envio, 
                               'Controle de Atividades (Solicitado pela ' || a.divisao || ')' AS de,
                               ".$para." AS para, 
                               ".$cc." AS cc, 
                               '' AS cco,
                               ".$assunto." AS assunto, 
                               '".$mensagem."' AS texto,
                               131 AS cd_evento
                          FROM projetos.atividades a
                          LEFT JOIN public.listas l
                            ON l.codigo    = a.status_atual 
                           AND l.categoria = 'STAT'
                         WHERE a.numero = ".intval($args['numero']).";
                      ";
        }
        
        
        #### ACOES PARA ATIVIDADES DA INFORMATICA ####
        if(trim($args['cd_gerencia_destino']) == "GI")
        {
            #### BANCO DE SOLUCAO - SUPORTE INFORMATICA ####        
            if(trim($args['cd_solucao_categoria']) != "")
            {
                if($this->get_atividade_solucao($args['numero']) == 0)
                {
                    #### INSERE ####
                    $qr_sql.= "
                                INSERT INTO projetos.atividade_solucao
                                     (
                                       cd_atividade, 
                                       cd_categoria, 
                                       ds_assunto
                                     )
                                VALUES
                                     (
                                       ".intval($args['numero']).", 
                                       ".(trim($args['cd_solucao_categoria']) != '' ? "'".trim($args['cd_solucao_categoria'])."'" : "DEFAULT").",
                                       ".(trim($args['ds_solucao_assunto']) != '' ? "'".trim($args['ds_solucao_assunto'])."'" : "DEFAULT")."
                                     );
                              ";                
                }
                else
                {
                    #### ATUALIZA ####
                    $qr_sql.= "
                                UPDATE projetos.atividade_solucao
                                   SET cd_categoria = ".(trim($args['cd_solucao_categoria']) != '' ? "'".trim($args['cd_solucao_categoria'])."'" : "DEFAULT").",
                                       ds_assunto   = ".(trim($args['ds_solucao_assunto']) != '' ? "'".trim($args['ds_solucao_assunto'])."'" : "DEFAULT")."
                                 WHERE cd_atividade = ".intval($args['numero']).";
                              ";                
                }
            }
            
            #### ATIVIDADE AGUARDANDO DIRETORIA ####        
            if(trim($args['status_atual']) == "ADIR")
            {
                //Coloca o status para PAUSE nas tarefas com status PLAY.
                $qr_sql.= "
                            INSERT INTO projetos.tarefa_historico 
                                 ( 
                                    cd_atividade, 
                                    cd_tarefa, 
                                    cd_recurso, 
                                    timestamp_alteracao, 
                                    descricao, 
                                    status_atual,
                                    cd_usuario_inclusao
                                 )
                            SELECT t.cd_atividade,
                                   t.cd_tarefa, 
                                   t.cd_recurso,
                                   CURRENT_TIMESTAMP,
                                   'Atividade Aguardando diretoria.', 
                                   'SUSP',
                                   ".intval($args['cd_usuario'])."                                 
                              FROM projetos.tarefas t
                             WHERE t.cd_atividade = ".intval($args['numero'])."
                               AND t.status_atual IN ('EMAN','SUSP');

                            UPDATE projetos.tarefas AS t
                               SET status_atual = 'SUSP'
                             WHERE (t.cd_atividade, t.cd_tarefa) IN (SELECT t1.cd_atividade, t1.cd_tarefa
                                                                       FROM projetos.tarefas t1
                                                                      WHERE t1.cd_atividade = ".intval($args['numero'])."
                                                                        AND t1.status_atual IN ('EMAN','SUSP'));                                                                        
                          ";
            }
            
            #### ATIVIDADE AGUARDANDO USUARIO ####      
            if(trim($args['status_atual']) == "AUSR")
            {
                //Coloca o status para PAUSE nas tarefas com status PLAY.
                $qr_sql.= "
                            INSERT INTO projetos.tarefa_historico 
                                 ( 
                                    cd_atividade, 
                                    cd_tarefa, 
                                    cd_recurso, 
                                    timestamp_alteracao, 
                                    descricao, 
                                    status_atual,
                                    cd_usuario_inclusao
                                 )
                            SELECT t.cd_atividade,
                                   t.cd_tarefa, 
                                   t.cd_recurso,
                                   CURRENT_TIMESTAMP,
                                   'Atividade Aguardando usuário.', 
                                   'SUSP',
                                   ".intval($args['cd_usuario'])."                                 
                              FROM projetos.tarefas t
                             WHERE t.cd_atividade = ".intval($args['numero'])."
                               AND t.status_atual IN ('EMAN','SUSP');

                            UPDATE projetos.tarefas AS t
                               SET status_atual = 'SUSP'
                             WHERE (t.cd_atividade, t.cd_tarefa) IN (SELECT t1.cd_atividade, t1.cd_tarefa
                                                                       FROM projetos.tarefas t1
                                                                      WHERE t1.cd_atividade = ".intval($args['numero'])."
                                                                        AND t1.status_atual IN ('EMAN','SUSP'));                                                                        
                          ";
            }   

            #### ATIVIDADE AGUARDANDO DEFINIÇÃO ####        
            if(trim($args['status_atual']) == "AGDF")
            {
                //Coloca o status para Aguardando definição  nas tarefas não concluídas (ok do analista). 
                $qr_sql.= "
                            INSERT INTO projetos.tarefa_historico 
                                 ( 
                                    cd_atividade, 
                                    cd_tarefa, 
                                    cd_recurso, 
                                    timestamp_alteracao, 
                                    descricao, 
                                    status_atual,
                                    cd_usuario_inclusao
                                 )
                            SELECT t.cd_atividade,
                                   t.cd_tarefa, 
                                   t.cd_recurso,
                                   CURRENT_TIMESTAMP,
                                   'Atividade Aguardando definição.', 
                                   'AGDF',
                                   ".intval($args['cd_usuario'])."                                 
                              FROM projetos.tarefas t
                             WHERE t.cd_atividade = ".intval($args['numero'])."
                               AND t.status_atual <> 'CONC';

                            UPDATE projetos.tarefas AS t
                               SET status_atual = 'AGDF'
                             WHERE (t.cd_atividade, t.cd_tarefa) IN (SELECT t1.cd_atividade, t1.cd_tarefa
                                                                       FROM projetos.tarefas t1
                                                                      WHERE t1.cd_atividade = ".intval($args['numero'])."
                                                                        AND t1.status_atual <> 'CONC');                                                                     
                          ";
            }

            #### ATIVIDADE CANCELADA ####       
            if(trim($args['status_atual']) == "CANC")
            {
                //Coloca o status para Aguardando definição  nas tarefas não concluídas (ok do analista). 
                $qr_sql.= "
                            INSERT INTO projetos.tarefa_historico 
                                 ( 
                                    cd_atividade, 
                                    cd_tarefa, 
                                    cd_recurso, 
                                    timestamp_alteracao, 
                                    descricao, 
                                    status_atual,
                                    cd_usuario_inclusao
                                 )
                            SELECT t.cd_atividade,
                                   t.cd_tarefa, 
                                   t.cd_recurso,
                                   CURRENT_TIMESTAMP,
                                   'Atividade Cancelada.', 
                                   'CANC',
                                   ".intval($args['cd_usuario'])."                                 
                              FROM projetos.tarefas t
                             WHERE t.cd_atividade = ".intval($args['numero'])."
                               AND t.status_atual <> 'CONC';

                            UPDATE projetos.tarefas AS t
                               SET status_atual = 'CANC'
                             WHERE (t.cd_atividade, t.cd_tarefa) IN (SELECT t1.cd_atividade, t1.cd_tarefa
                                                                       FROM projetos.tarefas t1
                                                                      WHERE t1.cd_atividade = ".intval($args['numero'])."
                                                                        AND t1.status_atual <> 'CONC');                                                                     
                          ";
            }

            #### ATIVIDADE SUSPENSA ####        
            if(trim($args['status_atual']) == "SUSP")
            {
                //Coloca o status para Suspensa nas tarefas não concluídas (ok do analista). 
                $qr_sql.= "
                            INSERT INTO projetos.tarefa_historico 
                                 ( 
                                    cd_atividade, 
                                    cd_tarefa, 
                                    cd_recurso, 
                                    timestamp_alteracao, 
                                    descricao, 
                                    status_atual,
                                    cd_usuario_inclusao
                                 )
                            SELECT t.cd_atividade,
                                   t.cd_tarefa, 
                                   t.cd_recurso,
                                   CURRENT_TIMESTAMP,
                                   'Atividade Suspensa.', 
                                   'SUSP',
                                   ".intval($args['cd_usuario'])."                                 
                              FROM projetos.tarefas t
                             WHERE t.cd_atividade = ".intval($args['numero'])."
                               AND t.status_atual <> 'CONC';

                            UPDATE projetos.tarefas AS t
                               SET status_atual = 'SUSP'
                             WHERE (t.cd_atividade, t.cd_tarefa) IN (SELECT t1.cd_atividade, t1.cd_tarefa
                                                                       FROM projetos.tarefas t1
                                                                      WHERE t1.cd_atividade = ".intval($args['numero'])."
                                                                        AND t1.status_atual <> 'CONC');                                                                     
                          ";
            }           
        }

        if(
            in_array(trim($args['status_atual']), $this->ar_status_fim_real) 
            AND 
            trim($args['cd_gerencia_solicitante']) == 'GCM'
            AND
            trim($args['cd_empresa']) != ''
            AND
            trim($args['cd_registro_empregado']) != ''
            AND
            trim($args['cd_sequencia']) != ''
        )
        {
            $qr_sql.= "
                INSERT INTO projetos.atendimento_retorno_participante
                     (
                        cd_atividade, 
                        cd_usuario_inclusao, 
                        cd_usuario_alteracao
                     )
                VALUES 
                     (
                        ".intval($args['numero']).",
                        ".intval($args['cd_usuario']).",
                        ".intval($args['cd_usuario'])."
                     );";
        }
        
        #echo "<PRE> $qr_sql </PRE>";exit;
        $result = $this->db->query($qr_sql);
    }   

    public function get_atividade_solucao($cd_atividade)
    {
      $qr_sql = "
        SELECT COUNT(*) AS tl
          FROM projetos.atividade_solucao
         WHERE cd_atividade = ".intval($cd_atividade).";";

      $row = $this->db->query($qr_sql)->row_array();

      return intval($row['tl']);
    }
}
?>