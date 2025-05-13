<?php
class Enquetes_model extends Model
{
    function __construct()
    {
        parent::Model();
    }

    function listar(&$result, $args=array())
    {
        $qr_sql = "
            SELECT e.cd_enquete, 
                   e.titulo, 
                   e.cd_site, 
                   u.nome, 
                   e.cd_responsavel, 
                   TO_CHAR(e.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao, 
                   TO_CHAR(e.dt_inicio, 'DD/MM/YYYY HH24:MI') AS dt_inicio, 
                   TO_CHAR(e.dt_fim, 'DD/MM/YYYY HH24:MI') AS dt_fim 
              FROM projetos.enquetes e 
              JOIN projetos.usuarios_controledi u 
                ON e.cd_responsavel = u.codigo 
             WHERE e.dt_exclusao IS NULL 
               ".(trim($args["titulo"]) != "" ? "AND UPPER(funcoes.remove_acento(TRIM(e.titulo))) LIKE UPPER(funcoes.remove_acento('%".utf8_decode(trim($args["titulo"]))."%'))" : "")."
               ".(trim($args["cd_enquete"]) != "" ? "AND e.cd_enquete = ".intval($args['cd_enquete']) : "")."
               ".(((trim($args["dt_ini"]) != "") and  (trim($args["dt_fim"]) != "")) ? "AND CAST(e.dt_inicio AS DATE) BETWEEN TO_DATE('".trim($args["dt_ini"])."', 'DD/MM/YYYY') AND TO_DATE('".trim($args["dt_fim"])."', 'DD/MM/YYYY')" : "")."
             ORDER BY e.cd_enquete DESC;";
        $result = $this->db->query($qr_sql);
    }

    function cadastro(&$result, $args=array())
    {
        /*
            'I' => 'Computador-IP (Público externo e/ou interno)'
            'U' => 'Usuário e-prev (Somente colaboradores)'
            'F' => 'Formulário (Digitação de formulários)'
            'P' => 'Participante'
            'R' => 'RE'
        */  
        #http://www.fundacaoceee.com.br/pesquisa.php?id=414&c=b0a4fed42fc9723fc5ef166da6e25614
        $qr_sql = "
                    SELECT e.cd_enquete, 
                           CASE WHEN e.controle_respostas = 'I' THEN 'http://www.fundacaoceee.com.br/pesquisa.php?id=' || e.cd_enquete::TEXT
                                WHEN e.controle_respostas = 'U' THEN '".base_url_eprev()."resp_enquetes_capa.php?c=' || e.cd_enquete::TEXT
                                WHEN e.controle_respostas = 'F' THEN '".base_url_eprev()."resp_enquetes_capa.php?c=' || e.cd_enquete::TEXT
                                WHEN e.controle_respostas = 'R' THEN '".base_url_eprev()."resp_enquetes_capa.php?c=' || e.cd_enquete::TEXT
                                WHEN e.controle_respostas = 'P' THEN '".base_url_eprev()."resp_enquetes_capa.php?c=' || e.cd_enquete::TEXT
                           END AS ds_url_pesquisa,
                           e.titulo AS ds_titulo, 
                           e.cd_site, 
                           e.cd_responsavel,
                           funcoes.get_usuario_area(e.cd_responsavel::INTEGER) AS cd_gerencia,
                           e.dt_inclusao, 
                           e.dt_exclusao,
                           e.texto_abertura, 
                           e.cd_evento_institucional, 
                           e.cd_publicacao, 
                           e.imagem, 
                           e.controle_respostas AS tp_controle_resposta, 
                           e.ultimo_respondente, 
                           e.cd_servico, 
                           e.tipo_enquete, 
                           e.tipo_layout, 
                           e.texto_encerramento, 
                           e.obrigatoriedade, 
                           e.nr_publico_total, 
                           e.cd_divisao_responsavel, 
                           e.flag_percentual_respondentes,
                           TO_CHAR(e.dt_inicio, 'DD/MM/YYYY') AS dt_inicio, 
                           TO_CHAR(e.dt_inicio, 'HH24:MI') AS hr_inicio, 
                           TO_CHAR(e.dt_fim, 'DD/MM/YYYY') AS dt_final, 
                           TO_CHAR(e.dt_fim, 'HH24:MI') AS hr_final,
                           CASE WHEN (e.cd_responsavel = ".$args['cd_usuario']." OR cd_divisao_responsavel IN ('".$args['cd_gerencia']."','".$args['cd_gerencia_ant']."')) THEN 'S' ELSE 'N' END AS fl_aba,
                           CASE WHEN e.cd_responsavel = ".$args['cd_usuario']." THEN 'S' ELSE 'N' END AS fl_editar,
                           (SELECT ep.cd_pergunta
                              FROM projetos.enquete_perguntas ep
                             WHERE ep.cd_enquete = e.cd_enquete
                               AND ep.texto IS NULL 
                               AND TRIM(COALESCE(ep.pergunta_texto,'')) <> ''
                            ORDER BY ep.cd_pergunta DESC) AS cd_pergunta_texto,						   
                           (SELECT ep.pergunta_texto
                              FROM projetos.enquete_perguntas ep
                             WHERE ep.cd_enquete = e.cd_enquete
                               AND ep.texto IS NULL 
                               AND TRIM(COALESCE(ep.pergunta_texto,'')) <> ''
                            ORDER BY ep.cd_pergunta DESC) AS pergunta_texto,
                           (SELECT ep.cd_agrupamento
                              FROM projetos.enquete_perguntas ep
                             WHERE ep.cd_enquete = e.cd_enquete
                               AND ep.texto IS NULL 
                               AND TRIM(COALESCE(ep.pergunta_texto,'')) <> ''
                            ORDER BY ep.cd_pergunta DESC) AS cd_agrupamento                         
                      FROM projetos.enquetes e 
                     WHERE e.cd_enquete = ".intval($args['cd_enquete'])."
                 ";
        $result = $this->db->query($qr_sql);
    }
    
    function salvar(&$result, $args=array())
    {
        if(intval($args['cd_enquete']) == 0)
        {
            $cd_enquete = intval($this->db->get_new_id("projetos.enquetes", "cd_enquete"));
        
            $qr_sql = "
                        INSERT INTO projetos.enquetes 
                             ( 
                               cd_enquete,
                               titulo,
                               nr_publico_total,                               
                               dt_inicio, 
                               dt_fim, 
                               controle_respostas,
                               cd_divisao_responsavel,
                               cd_responsavel, 
                               texto_abertura, 
                               texto_encerramento,
                               cd_usuario_inclusao
                             ) 
                        VALUES  
                             ( 
                               ".intval($cd_enquete).", 
                               ".(trim($args['ds_titulo']) != '' ? str_escape($args['ds_titulo']) : "DEFAULT").", 
                               ".(trim($args['nr_publico_total']) != '' ? intval($args['nr_publico_total']) : "DEFAULT").", 
                               ".(((trim($args['dt_inicio']) != '') and (trim($args['hr_inicio']) != '')) ? "TO_TIMESTAMP('".trim($args['dt_inicio'])." ".trim($args['hr_inicio'])."', 'DD/MM/YYYY HH24:MI')" : "DEFAULT").",
                               ".(((trim($args['dt_final']) != '') and (trim($args['hr_final']) != '')) ? "TO_TIMESTAMP('".trim($args['dt_final'])." ".trim($args['hr_final'])."', 'DD/MM/YYYY HH24:MI')" : "DEFAULT").",
                               ".(trim($args['tp_controle_resposta']) != '' ? "'".trim($args['tp_controle_resposta'])."'" : "DEFAULT").",
                               ".(trim($args['cd_divisao_responsavel']) != '' ? "'".trim($args['cd_divisao_responsavel'])."'" : "DEFAULT").", 
                               ".(trim($args['cd_responsavel']) != '' ? intval($args['cd_responsavel']) : "DEFAULT").",
                               ".(trim($args['texto_abertura']) != '' ? str_escape($args['texto_abertura']) : "DEFAULT").", 
                               ".(trim($args['texto_encerramento']) != '' ? str_escape($args['texto_encerramento']) : "DEFAULT").",
                               ".(trim($args['cd_usuario']) != '' ? intval($args['cd_usuario']) : "DEFAULT")."
                             )          
                      ";
        }
        else
        {
            $cd_enquete = intval($args['cd_enquete']);
        
            $qr_sql = "
                        UPDATE projetos.enquetes
                           SET titulo                 = ".(trim($args['ds_titulo']) != '' ? str_escape($args['ds_titulo']) : "DEFAULT").",
                               nr_publico_total       = ".(trim($args['nr_publico_total']) != '' ? intval($args['nr_publico_total']) : "DEFAULT").", 
                               dt_inicio              = ".(((trim($args['dt_inicio']) != '') and (trim($args['hr_inicio']) != '')) ? "TO_TIMESTAMP('".trim($args['dt_inicio'])." ".trim($args['hr_inicio'])."', 'DD/MM/YYYY HH24:MI')" : "DEFAULT").",
                               dt_fim                 = ".(((trim($args['dt_final']) != '') and (trim($args['hr_final']) != '')) ? "TO_TIMESTAMP('".trim($args['dt_final'])." ".trim($args['hr_final'])."', 'DD/MM/YYYY HH24:MI')" : "DEFAULT").",
                               controle_respostas     = ".(trim($args['tp_controle_resposta']) != '' ? "'".trim($args['tp_controle_resposta'])."'" : "DEFAULT").",
                               cd_divisao_responsavel = ".(trim($args['cd_divisao_responsavel']) != '' ? "'".trim($args['cd_divisao_responsavel'])."'" : "DEFAULT").",
                               cd_responsavel         = ".(trim($args['cd_responsavel']) != '' ? intval($args['cd_responsavel']) : "DEFAULT").",
                               texto_abertura         = ".(trim($args['texto_abertura']) != '' ? str_escape($args['texto_abertura']) : "DEFAULT").", 
                               texto_encerramento     = ".(trim($args['texto_encerramento']) != '' ? str_escape($args['texto_encerramento']) : "DEFAULT")."
                         WHERE cd_enquete = ".intval($args['cd_enquete'])."
                      ";
        }

        #echo "<PRE>$qr_sql</PRE>";exit;
        $result = $this->db->query($qr_sql);
        
        return $cd_enquete;
    }   

    function duplicar(&$result, $args=array())
    {
        if(intval($args['cd_enquete']) > 0)
        {
            $qr_sql = "
                        SELECT enquete_duplicar AS cd_enquete
						  FROM projetos.enquete_duplicar(".intval($args['cd_enquete']).")
                      ";
			$result = $this->db->query($qr_sql);
			#echo "<PRE>$qr_sql</PRE>";exit;
        }
    } 	
	
    function limparResposta(&$result, $args=array())
    {
        $qr_sql = "
					DELETE FROM projetos.usuarios_enquetes 
					 WHERE cd_enquete = ".intval($args['cd_enquete']).";
					
					DELETE FROM projetos.enquetes_participantes 
					 WHERE cd_enquete = ".intval($args['cd_enquete']).";
					
					DELETE FROM projetos.enquete_resultados 
					 WHERE cd_enquete = ".intval($args['cd_enquete']).";
                  ";
        $result = $this->db->query($qr_sql);
    } 	
	
    function estruturaSalvar(&$result, $args=array())
    {
        if(intval($args['cd_pergunta_texto']) == 0)
        {
            $cd_pergunta_texto = intval($this->db->get_new_id("projetos.enquete_perguntas", "cd_pergunta"));
        
            $qr_sql = "
                        INSERT INTO projetos.enquete_perguntas 
                             ( 
                               cd_pergunta,
                               cd_enquete,
                               cd_agrupamento, 
                               pergunta_texto,
							   nr_ordem
                             ) 
                        VALUES  
                             ( 
                               ".intval($cd_pergunta_texto).", 
                               ".(trim($args['cd_enquete']) != '' ? intval($args['cd_enquete']) : "DEFAULT").",
                               ".(trim($args['cd_agrupamento']) != '' ? intval($args['cd_agrupamento']) : "DEFAULT").",
                               ".(trim($args['pergunta_texto']) != '' ? str_escape($args['pergunta_texto']) : "DEFAULT").",
							   (COALESCE((SELECT MAX(nr_ordem) FROM projetos.enquete_perguntas WHERE cd_enquete = ".intval($args['cd_enquete'])."),0) + 1)
                             )          
                      ";
        }
        else
        {
            $qr_sql = "
                        UPDATE projetos.enquete_perguntas
                           SET cd_agrupamento = ".(trim($args['cd_agrupamento']) != '' ? intval($args['cd_agrupamento']) : "DEFAULT").",
                               pergunta_texto = ".(trim($args['pergunta_texto']) != '' ? str_escape($args['pergunta_texto']) : "DEFAULT").",
							   nr_ordem       = (COALESCE((SELECT MAX(nr_ordem) FROM projetos.enquete_perguntas WHERE cd_enquete = ".intval($args['cd_enquete'])."),0) + 1)
                         WHERE cd_pergunta = ".intval($args['cd_pergunta_texto'])."
                           AND cd_enquete  = ".intval($args['cd_enquete'])."
                      ";
        }

        #echo "<PRE>$qr_sql</PRE>";exit;
        $result = $this->db->query($qr_sql);
    } 	
	
    function agrupamento(&$result, $args=array())
    {
        $qr_sql = "
                    SELECT ea.cd_enquete,
                           ea.cd_agrupamento, 
                           ea.nome AS ds_agrupamento, 
                           ea.indic_escala, 
                           ea.mostrar_valores, 
                           ea.numero_colunas_maximo, 
                           ea.ncolsamp_diss, 
                           ea.ordem AS nr_ordem, 
                           ea.nota_rodape, 
                           ea.dt_exclusao, 
                           ea.cd_usu_exclusao, 
                           ea.disposicao
                      FROM projetos.enquete_agrupamentos ea
                     WHERE ea.dt_exclusao IS NULL
                       AND ea.cd_agrupamento = ".intval($args['cd_agrupamento'])."
                       AND ea.cd_enquete     = ".intval($args['cd_enquete'])."
                     ORDER BY ea.ordem, 
                              ea.nome
                  ";
        $result = $this->db->query($qr_sql);
    }   
    
    function agrupamentoListar(&$result, $args=array())
    {
        $qr_sql = "
                    SELECT ea.cd_enquete,
                           ea.cd_agrupamento, 
                           ea.nome AS ds_agrupamento, 
                           ea.indic_escala, 
                           ea.mostrar_valores, 
                           ea.numero_colunas_maximo, 
                           ea.ncolsamp_diss, 
                           ea.ordem AS nr_ordem, 
                           ea.nota_rodape, 
                           ea.dt_exclusao, 
                           ea.cd_usu_exclusao, 
                           ea.disposicao
                      FROM projetos.enquete_agrupamentos ea
                     WHERE ea.dt_exclusao IS NULL
                       AND ea.cd_enquete = ".intval($args['cd_enquete'])."
                     ORDER BY ea.ordem, 
                              ea.nome
                  ";
        $result = $this->db->query($qr_sql);
    } 

    function agrupamentoSalvar(&$result, $args=array())
    {
        if(intval($args['cd_agrupamento']) == 0)
        {
            $cd_agrupamento = intval($this->db->get_new_id("projetos.enquete_agrupamentos", "cd_agrupamento"));
        
            $qr_sql = "
                        INSERT INTO projetos.enquete_agrupamentos 
                             ( 
                               cd_agrupamento,
                               cd_enquete,
                               nome, 
                               ordem,
                               indic_escala,
                               mostrar_valores,
                               disposicao,
                               nota_rodape
                             ) 
                        VALUES  
                             ( 
                               ".intval($cd_agrupamento).", 
                               ".(trim($args['cd_enquete']) != '' ? intval($args['cd_enquete']) : "DEFAULT").",
                               ".(trim($args['ds_agrupamento']) != '' ? str_escape($args['ds_agrupamento']) : "DEFAULT").", 
                               ".(trim($args['nr_ordem']) != '' ? intval($args['nr_ordem']) : "DEFAULT").",
                               ".(trim($args['indic_escala']) != '' ? "'".trim($args['indic_escala'])."'" : "DEFAULT").",
                               ".(trim($args['mostrar_valores']) != '' ? "'".trim($args['mostrar_valores'])."'" : "DEFAULT").",
                               ".(trim($args['disposicao']) != '' ? "'".trim($args['disposicao'])."'" : "DEFAULT").",
                               ".(trim($args['nota_rodape']) != '' ? intval($args['nota_rodape']) : "DEFAULT")."
                             )          
                      ";
        }
        else
        {
            $cd_agrupamento = intval($args['cd_agrupamento']);
        
            $qr_sql = "
                        UPDATE projetos.enquete_agrupamentos 
                           SET nome            = ".(trim($args['ds_agrupamento']) != '' ? str_escape($args['ds_agrupamento']) : "DEFAULT").", 
                               ordem           = ".(trim($args['nr_ordem']) != '' ? intval($args['nr_ordem']) : "DEFAULT").",
                               indic_escala    = ".(trim($args['indic_escala']) != '' ? "'".trim($args['indic_escala'])."'" : "DEFAULT").",
                               mostrar_valores = ".(trim($args['mostrar_valores']) != '' ? "'".trim($args['mostrar_valores'])."'" : "DEFAULT").",
                               disposicao      = ".(trim($args['disposicao']) != '' ? "'".trim($args['disposicao'])."'" : "DEFAULT").",
                               nota_rodape     = ".(trim($args['nota_rodape']) != '' ? intval($args['nota_rodape']) : "DEFAULT")."
                         WHERE cd_agrupamento = ".intval($args['cd_agrupamento'])."
                           AND cd_enquete     = ".intval($args['cd_enquete'])."
                      ";
        }

        #echo "<PRE>$qr_sql</PRE>";exit;
        $result = $this->db->query($qr_sql);
        
        return $cd_agrupamento;
    }  

    function agrupamentoExcluir(&$result, $args=array())
    {
        if(intval($args['cd_agrupamento']) > 0)
        {
            $qr_sql = "
                        UPDATE projetos.enquete_agrupamentos 
                           SET dt_exclusao     = CURRENT_TIMESTAMP, 
                               cd_usu_exclusao = ".(trim($args['cd_usuario']) != '' ? intval($args['cd_usuario']) : "DEFAULT")."
                         WHERE cd_agrupamento = ".intval($args['cd_agrupamento'])."
                           AND cd_enquete     = ".intval($args['cd_enquete'])."
                      ";
        }
    
        #echo "<PRE>$qr_sql</PRE>";exit;
        $result = $this->db->query($qr_sql);
    }   
    
    function questao(&$result, $args=array())
    {
        $qr_sql = "
                    SELECT p.cd_enquete,
                           p.cd_pergunta, 
                           p.cd_agrupamento,
						   p.nr_ordem,
                           p.texto AS ds_pergunta, 
                           p.r1, p.r2, p.r3, p.r4, p.r5, p.r6, p.r7, p.r8, p.r9, p.r10, p.r11, p.r12, p.r13, p.r14, p.r15, 
                           p.rotulo1, rotulo2, p.rotulo3, p.rotulo4, p.rotulo5, p.rotulo6, p.rotulo7, p.rotulo8, p.rotulo9, p.rotulo10, p.rotulo11, p.rotulo12, p.rotulo13, p.rotulo14, p.rotulo15, 
                           p.r1_complemento, p.r2_complemento, p.r3_complemento, p.r4_complemento, p.r5_complemento, p.r6_complemento, 
                           p.r7_complemento, p.r8_complemento, p.r9_complemento, p.r10_complemento, p.r11_complemento, p.r12_complemento, 
                           p.r13_complemento, p.r14_complemento, p.r15_complemento,
                           p.r1_complemento_rotulo, p.r2_complemento_rotulo, p.r3_complemento_rotulo, p.r4_complemento_rotulo, p.r5_complemento_rotulo, p.r6_complemento_rotulo, 
                           p.r7_complemento_rotulo, p.r8_complemento_rotulo, p.r9_complemento_rotulo, p.r10_complemento_rotulo, p.r11_complemento_rotulo, p.r12_complemento_rotulo,
                           p.r13_complemento_rotulo, p.r14_complemento_rotulo, p.r15_complemento_rotulo,
                           p.legenda1, p.legenda2, p.legenda3, p.legenda4, p.legenda5, p.legenda6, p.legenda7, p.legenda8, p.legenda9, p.legenda10, p.legenda11, p.legenda12, 
                           p.legenda13, p.legenda14, p.legenda15,
                           p.r_diss, p.rotulo_dissertativa, 
                           p.r_justificativa, p.rotulo_justificativa
                      FROM projetos.enquete_perguntas p
                     WHERE p.dt_exclusao IS NULL
                       AND p.cd_pergunta = ".intval($args['cd_pergunta'])."
                       AND p.cd_enquete  = ".intval($args['cd_enquete'])."
                  ";
        $result = $this->db->query($qr_sql);
    }   
  
    function questaoSalvar(&$result, $args=array())
    {
        if(intval($args['cd_pergunta']) == 0)
        {
            $cd_pergunta = intval($this->db->get_new_id("projetos.enquete_perguntas", "cd_pergunta"));
            
            $resposta_campo = "";
            $resposta_valor = "";
            for ($i = 1; $i <= 15; $i++)
            {
                $resposta_campo.= " 
                                    r".$i.", 
                                    rotulo".$i.", 
                                    legenda".$i.",
                                    r".$i."_complemento, 
                                    r".$i."_complemento_rotulo, 
                                  ";
                                     
                $resposta_valor.= " 
                                    ".(trim($args['r'.$i]) != '' ? "'".trim($args['r'.$i])."'" : "DEFAULT").",
                                    ".(((trim($args['r'.$i]) == "S") and (trim($args['rotulo'.$i]) != '')) ? str_escape($args['rotulo'.$i]) : "DEFAULT").",
                                    ".(((trim($args['r'.$i]) == "S") and (trim($args['legenda'.$i]) != '')) ? str_escape($args['legenda'.$i]) : "DEFAULT").",
                                    ".(trim($args['r'.$i.'_complemento']) != '' ? "'".trim($args['r'.$i.'_complemento'])."'" : "DEFAULT").",
                                    ".(((trim($args['r'.$i.'_complemento']) == "S") and (trim($args['r'.$i.'_complemento_rotulo']) != '')) ? str_escape($args['r'.$i.'_complemento_rotulo']) : "DEFAULT").",
                                  ";                                 
            }               
        
            $qr_sql = "
                        INSERT INTO projetos.enquete_perguntas 
                             ( 
                               cd_pergunta,
                               cd_enquete,
                               cd_agrupamento, 
							   nr_ordem,
                               texto,
                               ".$resposta_campo."
                               r_diss, 
                               rotulo_dissertativa, 
                               r_justificativa,
                               rotulo_justificativa 
                             ) 
                        VALUES  
                             ( 
                               ".intval($cd_pergunta).", 
                               ".(trim($args['cd_enquete']) != '' ? intval($args['cd_enquete']) : "DEFAULT").",
                               ".(trim($args['cd_agrupamento']) != '' ? intval($args['cd_agrupamento']) : "DEFAULT").",
							   ".(trim($args['nr_ordem']) != '' ? intval($args['nr_ordem']) : "DEFAULT").",
                               ".(trim($args['ds_pergunta']) != '' ? str_escape($args['ds_pergunta']) : "DEFAULT").", 
                               ".$resposta_valor."
                               ".(trim($args['r_diss']) != '' ? "'".trim($args['r_diss'])."'" : "DEFAULT").",
                               ".(trim($args['rotulo_dissertativa']) != '' ? str_escape($args['rotulo_dissertativa']) : "DEFAULT").",
                               ".(trim($args['r_justificativa']) != '' ? "'".trim($args['r_justificativa'])."'" : "DEFAULT").",
                               ".(trim($args['rotulo_justificativa']) != '' ? str_escape($args['rotulo_justificativa']) : "DEFAULT")."                             
                             )          
                      ";
        }
        else
        {
            $cd_pergunta = intval($args['cd_pergunta']);
            
            $resposta = "";
            for ($i = 1; $i <= 15; $i++)
            {
                $resposta.= " 
                                r".$i."                    = ".(trim($args['r'.$i]) != '' ? "'".trim($args['r'.$i])."'" : "DEFAULT").",
                                rotulo".$i."               = ".(((trim($args['r'.$i]) == "S") and (trim($args['rotulo'.$i]) != '')) ? str_escape($args['rotulo'.$i]) : "DEFAULT").",
                                legenda".$i."              = ".(((trim($args['r'.$i]) == "S") and (trim($args['legenda'.$i]) != '')) ? str_escape($args['legenda'.$i]) : "DEFAULT").",
                                r".$i."_complemento        = ".(trim($args['r'.$i.'_complemento']) != '' ? "'".trim($args['r'.$i.'_complemento'])."'" : "DEFAULT").",
                                r".$i."_complemento_rotulo = ".(((trim($args['r'.$i.'_complemento']) == "S") and (trim($args['r'.$i.'_complemento_rotulo']) != '')) ? str_escape($args['r'.$i.'_complemento_rotulo']) : "DEFAULT").",
                            ";
            }           
        
            $qr_sql = "
                        UPDATE projetos.enquete_perguntas
                           SET ".$resposta."
                               cd_agrupamento       = ".(trim($args['cd_agrupamento']) != '' ? intval($args['cd_agrupamento']) : "DEFAULT").",
							   nr_ordem             = ".(trim($args['nr_ordem']) != '' ? intval($args['nr_ordem']) : "DEFAULT").",
                               texto                = ".(trim($args['ds_pergunta']) != '' ? str_escape($args['ds_pergunta']) : "DEFAULT").", 
                               r_diss               = ".(trim($args['r_diss']) != '' ? "'".trim($args['r_diss'])."'" : "DEFAULT").",
                               rotulo_dissertativa  = ".(trim($args['rotulo_dissertativa']) != '' ? str_escape($args['rotulo_dissertativa']) : "DEFAULT").",
                               r_justificativa      = ".(trim($args['r_justificativa']) != '' ? "'".trim($args['r_justificativa'])."'" : "DEFAULT").", 
                               rotulo_justificativa = ".(trim($args['rotulo_justificativa']) != '' ? str_escape($args['rotulo_justificativa']) : "DEFAULT")."
                         WHERE cd_pergunta = ".intval($args['cd_pergunta'])."
                           AND cd_enquete  = ".intval($args['cd_enquete'])."
                      ";
        }

        #echo "<PRE>$qr_sql</PRE>";exit;
        $result = $this->db->query($qr_sql);
        
        return $cd_pergunta;
    }   
    
    function questaoExcluir(&$result, $args=array())
    {
        if(intval($args['cd_pergunta']) > 0)
        {
            $qr_sql = "
                        UPDATE projetos.enquete_perguntas 
                           SET dt_exclusao     = CURRENT_TIMESTAMP, 
                               cd_usu_exclusao = ".(trim($args['cd_usuario']) != '' ? intval($args['cd_usuario']) : "DEFAULT")."
                         WHERE cd_pergunta = ".intval($args['cd_pergunta'])."
                           AND cd_enquete  = ".intval($args['cd_enquete'])."
                      ";
        }
    
        #echo "<PRE>$qr_sql</PRE>";exit;
        $result = $this->db->query($qr_sql);
    }    
	
    function questaoListar(&$result, $args=array())
    {
        $qr_sql = "
                    SELECT p.cd_enquete,
                           p.cd_pergunta, 
						   p.nr_ordem,
                           p.texto AS ds_pergunta, 
                           p.r1, 
                           p.r2, 
                           p.r3, 
                           p.r4, 
                           p.r5, 
                           p.r6, 
                           p.r7, 
                           p.r8, 
                           p.r9, 
                           p.r10, 
                           p.r11, 
                           p.r12,
                           p.r13,
                           p.r14,
                           p.r15,
                           p.cd_agrupamento, 
                           ea.nome AS ds_agrupamento,
                           ea.ordem AS nr_ordem_agrupamento
                      FROM projetos.enquete_perguntas p
                      JOIN projetos.enquete_agrupamentos ea
                        ON ea.cd_enquete     = p.cd_enquete
                       AND ea.cd_agrupamento = p.cd_agrupamento
                     WHERE p.dt_exclusao  IS NULL
                       AND ea.dt_exclusao IS NULL
                       AND p.cd_enquete   = ".intval($args['cd_enquete'])."
                     ORDER BY nr_ordem_agrupamento, 
					          p.nr_ordem,
                              p.cd_pergunta
                  ";
        $result = $this->db->query($qr_sql);
    }   
    
    function resposta(&$result, $args=array())
    {
        $qr_sql = "
                    SELECT er.cd_enquete,
                           er.cd_resposta, 
                           er.nome AS ds_resposta,
                           er.ordem AS nr_ordem,
						   er.valor AS vl_valor
                      FROM projetos.enquete_respostas er
                     WHERE er.cd_enquete  = ".intval($args['cd_enquete'])."
					   AND er.cd_resposta = ".intval($args['cd_resposta'])."					 
                     ORDER BY nr_ordem, 
                              ds_resposta
                  ";
        $result = $this->db->query($qr_sql);
    } 	
	
    function respostaListar(&$result, $args=array())
    {
        $qr_sql = "
                    SELECT er.cd_enquete,
                           er.cd_resposta, 
                           er.nome AS ds_resposta,
                           er.ordem AS nr_ordem,
						   er.valor
                      FROM projetos.enquete_respostas er
                     WHERE er.cd_enquete = ".intval($args['cd_enquete'])."
                     ORDER BY nr_ordem, 
                              ds_resposta
                  ";
        $result = $this->db->query($qr_sql);
    }

    function respostaSalvar(&$result, $args=array())
    {
        if(intval($args['cd_resposta']) == 0)
        {
            $cd_resposta = intval($this->db->get_new_id("projetos.enquete_respostas", "cd_resposta"));
        
            $qr_sql = "
                        INSERT INTO projetos.enquete_respostas 
                             ( 
                               cd_resposta,
                               cd_enquete,
                               nome, 
                               ordem,
                               valor
                             ) 
                        VALUES  
                             ( 
                               ".intval($cd_resposta).", 
                               ".(trim($args['cd_enquete']) != '' ? intval($args['cd_enquete']) : "DEFAULT").",
                               ".(trim($args['ds_resposta']) != '' ? str_escape($args['ds_resposta']) : "DEFAULT").", 
                               ".(trim($args['nr_ordem']) != '' ? intval($args['nr_ordem']) : "DEFAULT").",
                               ".(trim($args['vl_valor']) != '' ? floatval($args['vl_valor']) : "DEFAULT")."
                             )          
                      ";
        }
        else
        {
            $cd_resposta = intval($args['cd_resposta']);
        
            $qr_sql = "
                        UPDATE projetos.enquete_respostas 
                           SET nome  = ".(trim($args['ds_resposta']) != '' ? str_escape($args['ds_resposta']) : "DEFAULT").", 
                               ordem = ".(trim($args['nr_ordem']) != '' ? intval($args['nr_ordem']) : "DEFAULT").",
                               valor = ".(trim($args['vl_valor']) != '' ? floatval($args['vl_valor']) : "DEFAULT")."
                         WHERE cd_resposta = ".intval($args['cd_resposta'])."
                           AND cd_enquete  = ".intval($args['cd_enquete'])."
                      ";
        }

        #echo "<PRE>$qr_sql</PRE>";exit;
        $result = $this->db->query($qr_sql);
        
        return $cd_resposta;
    } 	
    
    function resultadoResumo(&$result, $args=array())
    {
        $qr_sql = "
					SELECT e.nr_publico_total, 
						   (
							SELECT COUNT(*)
							  FROM projetos.enquete_agrupamentos ea
							 WHERE ea.cd_enquete   = e.cd_enquete
							   AND ea.indic_escala = 'S'
						   ) AS vl_peso_1,
					       (
							SELECT avg(r2.valor)
							  FROM projetos.enquete_resultados r1, 
								   projetos.enquete_respostas r2, 
								   projetos.enquete_agrupamentos r3 
							 WHERE r2.cd_resposta = r1.valor 
							   AND r1.cd_enquete = e.cd_enquete
							   AND r2.cd_enquete = e.cd_enquete
							   AND r2.valor <> 0 
							   AND ip NOT LIKE ('%.%') 
							   AND r3.cd_enquete     = r2.cd_enquete 
							   AND r1.cd_agrupamento = r3.cd_agrupamento 
							   AND r1.cd_enquete     = r3.cd_enquete 
							   AND r3.indic_escala   = 'S'
							   ".(((trim($args["dt_ini"]) != "") and  (trim($args["dt_fim"]) != "")) ? "AND CAST(r1.dt_resposta AS DATE) BETWEEN TO_DATE('".trim($args["dt_ini"])."', 'DD/MM/YYYY') AND TO_DATE('".trim($args["dt_fim"])."', 'DD/MM/YYYY')" : "")."
                           ) AS vl_media_1,
					       (
							SELECT avg(r1.valor) as media 
							  FROM projetos.enquete_resultados r1, projetos.enquete_agrupamentos r3 
							 WHERE r1.cd_enquete = e.cd_enquete 
							   AND ip NOT LIKE ('%.%') 
							   AND r1.cd_agrupamento = r3.cd_agrupamento 
							   AND r1.cd_enquete     = r3.cd_enquete 
							   AND r3.indic_escala   = 'N'	
							   ".(((trim($args["dt_ini"]) != "") and  (trim($args["dt_fim"]) != "")) ? "AND CAST(r1.dt_resposta AS DATE) BETWEEN TO_DATE('".trim($args["dt_ini"])."', 'DD/MM/YYYY') AND TO_DATE('".trim($args["dt_fim"])."', 'DD/MM/YYYY')" : "")."							   
						   ) AS vl_media_2,
					       (
							SELECT COUNT(DISTINCT er.ip) 
                              FROM projetos.enquete_resultados er
                             WHERE er.cd_enquete = e.cd_enquete
					           ".(((trim($args["dt_ini"]) != "") and  (trim($args["dt_fim"]) != "")) ? "AND CAST(er.dt_resposta AS DATE) BETWEEN TO_DATE('".trim($args["dt_ini"])."', 'DD/MM/YYYY') AND TO_DATE('".trim($args["dt_fim"])."', 'DD/MM/YYYY')" : "")."
						   ) AS qt_respondente
					  FROM projetos.enquetes e
					 WHERE e.cd_enquete = ".intval($args['cd_enquete'])."
                  ";
        $result = $this->db->query($qr_sql);
    }

    function resultadoAgrupamento(&$result, $args=array())
    {
        $qr_sql = "
                    SELECT ea.cd_agrupamento, 
					       ea.nome, 
						   ea.indic_escala,
						   CASE WHEN ea.indic_escala = 'S'
						        THEN (SELECT AVG(er.valor)
                                        FROM projetos.enquete_resultados er
                                       WHERE er.cd_enquete     = ea.cd_enquete
                                         AND er.cd_agrupamento = ea.cd_agrupamento
										 ".(((trim($args["dt_ini"]) != "") and  (trim($args["dt_fim"]) != "")) ? "AND CAST(er.dt_resposta AS DATE) BETWEEN TO_DATE('".trim($args["dt_ini"])."', 'DD/MM/YYYY') AND TO_DATE('".trim($args["dt_fim"])."', 'DD/MM/YYYY')" : "")."
                                         AND er.ip NOT LIKE ('%.%')
                                         AND er.valor <> 6)
								ELSE (SELECT AVG(er.valor)
                                        FROM projetos.enquete_resultados er
                                       WHERE er.cd_enquete     = ea.cd_enquete
                                         AND er.cd_agrupamento = ea.cd_agrupamento
										 ".(((trim($args["dt_ini"]) != "") and  (trim($args["dt_fim"]) != "")) ? "AND CAST(er.dt_resposta AS DATE) BETWEEN TO_DATE('".trim($args["dt_ini"])."', 'DD/MM/YYYY') AND TO_DATE('".trim($args["dt_fim"])."', 'DD/MM/YYYY')" : "")."
                                         AND er.ip NOT LIKE ('%.%'))
						   END AS vl_media
			          FROM projetos.enquete_agrupamentos ea
					 WHERE ea.cd_enquete = ".intval($args['cd_enquete'])."
					 ORDER BY ea.ordem, 
					          ea.nome
                  ";
        $result = $this->db->query($qr_sql);
    }

    function resultadoQuestaoResumo(&$result, $args=array())
    {
		$qr_sql = "
					SELECT x.cd_enquete, 
						   x.cd_pergunta, 
						   x.nr_ordem_agrupamento,
						   x.nr_ordem_pergunta,
						   x.ds_pergunta, 
						   x.qt_resposta, 
						   x.vl_media,
						   x.vl_total,
						   (SELECT COUNT(*) 
							  FROM projetos.enquete_resultados er1
							 WHERE er1.cd_enquete    = x.cd_enquete
							   AND er1.questao::TEXT = ('R_'::TEXT || x.cd_pergunta::TEXT)
							   AND COALESCE(er1.descricao,'') <> '') AS qt_comentario					
					  FROM (
							SELECT ep.cd_enquete, 
								   ep.cd_pergunta, 
								   ea.ordem AS nr_ordem_agrupamento,
								   ep.nr_ordem AS nr_ordem_pergunta,
								   ep.texto AS ds_pergunta, 
								   COUNT(er.valor) AS qt_resposta, 
								   AVG(er.valor) AS vl_media,
								   SUM(er.valor) AS vl_total
							  FROM projetos.enquete_resultados er, 
								   projetos.enquete_perguntas ep, 
								   projetos.enquete_agrupamentos ea
							 WHERE (er.valor <> 6::NUMERIC AND ea.indic_escala = 'S' OR ea.indic_escala = 'N') 
							   AND er.questao::TEXT  = ('R_'::TEXT || ep.cd_pergunta::TEXT) 
							   AND er.cd_enquete     = ep.cd_enquete  
							   AND ea.cd_agrupamento = er.cd_agrupamento  
							   AND ea.cd_enquete     = er.cd_enquete
							   AND ea.cd_enquete     = ".intval($args['cd_enquete'])."
								".(((trim($args["dt_ini"]) != "") and  (trim($args["dt_fim"]) != "")) ? "AND CAST(er.dt_resposta AS DATE) BETWEEN TO_DATE('".trim($args["dt_ini"])."', 'DD/MM/YYYY') AND TO_DATE('".trim($args["dt_fim"])."', 'DD/MM/YYYY')" : "")."
							 GROUP BY ep.cd_enquete, 
									  ep.cd_pergunta, 
									  nr_ordem_agrupamento, 
									  nr_ordem_pergunta, 
									  ep.texto
							 ) x
					ORDER BY x.nr_ordem_agrupamento, 
						     x.nr_ordem_pergunta,
                             x.cd_pergunta
                  ";			
        $result = $this->db->query($qr_sql);
    }	
	
    function resultadoVerComentario(&$result, $args=array())
    {
        $qr_sql = "
					SELECT er.descricao 
					  FROM projetos.enquete_resultados er
					 WHERE er.cd_enquete    = ".intval($args['cd_enquete'])."
					   AND er.questao::TEXT = 'R_".intval($args['cd_pergunta'])."'::TEXT
					   AND COALESCE(er.descricao,'') <> ''
					   ".(((trim($args["dt_ini"]) != "") and  (trim($args["dt_fim"]) != "")) ? "AND CAST(er.dt_resposta AS DATE) BETWEEN TO_DATE('".trim($args["dt_ini"])."', 'DD/MM/YYYY') AND TO_DATE('".trim($args["dt_fim"])."', 'DD/MM/YYYY')" : "")."
					ORDER BY er.dt_resposta
                  ";
        $result = $this->db->query($qr_sql);
    }

    function resultadoVerGrafico(&$result, $args=array())
    {
		$qr_sql = "
					SELECT er.valor AS cd_resposta,
					       COALESCE((SELECT CASE WHEN er.valor::INTEGER = 1 THEN COALESCE(ep.legenda1, ep.rotulo1) 
						                         WHEN er.valor::INTEGER = 2 THEN COALESCE(ep.legenda2, ep.rotulo2) 
						                         WHEN er.valor::INTEGER = 3 THEN COALESCE(ep.legenda3, ep.rotulo3) 
						                         WHEN er.valor::INTEGER = 4 THEN COALESCE(ep.legenda4, ep.rotulo4) 
						                         WHEN er.valor::INTEGER = 5 THEN COALESCE(ep.legenda5, ep.rotulo5) 
						                         WHEN er.valor::INTEGER = 6 THEN COALESCE(ep.legenda6, ep.rotulo6) 
						                         WHEN er.valor::INTEGER = 7 THEN COALESCE(ep.legenda7, ep.rotulo7) 
						                         WHEN er.valor::INTEGER = 8 THEN COALESCE(ep.legenda8, ep.rotulo8) 
						                         WHEN er.valor::INTEGER = 9 THEN COALESCE(ep.legenda9, ep.rotulo9) 
						                         WHEN er.valor::INTEGER = 10 THEN COALESCE(ep.legenda10, ep.rotulo10) 
						                         WHEN er.valor::INTEGER = 11 THEN COALESCE(ep.legenda11, ep.rotulo11) 
                                                 WHEN er.valor::INTEGER = 12 THEN COALESCE(ep.legenda12, ep.rotulo12) 
                                                 WHEN er.valor::INTEGER = 13 THEN COALESCE(ep.legenda13, ep.rotulo13) 
                                                 WHEN er.valor::INTEGER = 14 THEN COALESCE(ep.legenda14, ep.rotulo14) 
						                         WHEN er.valor::INTEGER = 15 THEN COALESCE(ep.legenda15, ep.rotulo15) 
										         ELSE er.valor::TEXT
								            END
							           FROM projetos.enquete_perguntas ep 
							          WHERE ep.cd_enquete  = ".intval($args['cd_enquete'])." 
							            AND ep.cd_pergunta = ".intval($args['cd_pergunta'])."), er.valor::TEXT) AS ds_resposta,
					       (SELECT ep.texto
							  FROM projetos.enquete_perguntas ep 
							 WHERE ep.cd_enquete  = ".intval($args['cd_enquete'])." 
							   AND ep.cd_pergunta = ".intval($args['cd_pergunta']).") AS ds_pergunta,							   
						   COUNT(*) AS qt_resposta
					  FROM projetos.enquete_resultados er
					 WHERE cd_enquete       = ".intval($args['cd_enquete'])."
					   AND er.questao::TEXT = 'R_".intval($args['cd_pergunta'])."'::TEXT
					   ".(((trim($args["dt_ini"]) != "") and  (trim($args["dt_fim"]) != "")) ? "AND CAST(er.dt_resposta AS DATE) BETWEEN TO_DATE('".trim($args["dt_ini"])."', 'DD/MM/YYYY') AND TO_DATE('".trim($args["dt_fim"])."', 'DD/MM/YYYY')" : "")."
					 GROUP BY cd_resposta, ds_resposta, ds_pergunta
					 ORDER BY qt_resposta DESC
                  ";
        $result = $this->db->query($qr_sql);
    }	
	
    function resultadoQuestao(&$result, $args=array())
    {
		$qr_sql = "
                    SELECT p.cd_enquete, 
					       p.cd_pergunta, 
						   a.ordem, 
						   p.texto AS ds_pergunta, 
						   COUNT(r.valor) AS qt_resposta, 
						   AVG(r.valor) AS media
                      FROM projetos.enquete_resultados r, 
					       projetos.enquete_perguntas p, 
						   projetos.enquete_agrupamentos a
                     WHERE r.questao::TEXT = ('R_'::TEXT || p.cd_pergunta::TEXT) 
                       AND (r.valor <> 6::NUMERIC AND a.indic_escala = 'S' OR a.indic_escala = 'N') 
                       AND r.cd_enquete     = p.cd_enquete  
                       AND a.cd_agrupamento = r.cd_agrupamento  
                       AND a.cd_enquete     = r.cd_enquete
                       AND a.cd_enquete     = ".intval($args['cd_enquete'])."
                       ".(((trim($args["dt_ini"]) != "") and (trim($args["dt_fim"]) != "")) ? "AND CAST(r.dt_resposta AS DATE) BETWEEN TO_DATE('".trim($args["dt_ini"])."', 'DD/MM/YYYY') AND TO_DATE('".trim($args["dt_fim"])."', 'DD/MM/YYYY')" : "")."
                  GROUP BY p.cd_enquete, 
				           p.cd_pergunta, 
						   a.ordem, 
						   p.texto
                  ORDER BY a.ordem, 
				           p.nr_ordem,
				           p.cd_pergunta			
                  ";
        $result = $this->db->query($qr_sql);
    }	
	
    function resultadoQuestaoResposta(&$result, $args=array())
    {
		$qr_sql = "
                    SELECT er.valor AS cd_resposta,
						   CASE WHEN (SELECT indic_escala
						                FROM projetos.enquete_agrupamentos ea
							           WHERE ea.cd_enquete     = er.cd_enquete
							             AND ea.cd_agrupamento = er.cd_agrupamento) = 'S'
								THEN (SELECT nome 
								        FROM projetos.enquete_respostas r 
									   WHERE r.cd_enquete                   = er.cd_enquete 
									     AND CAST(r.cd_resposta AS NUMERIC) = er.valor)
								ELSE COALESCE((SELECT CASE WHEN er.valor::INTEGER = 1 THEN COALESCE(ep.legenda1, ep.rotulo1) 
						                                   WHEN er.valor::INTEGER = 2 THEN COALESCE(ep.legenda2, ep.rotulo2) 
						                                   WHEN er.valor::INTEGER = 3 THEN COALESCE(ep.legenda3, ep.rotulo3) 
						                                   WHEN er.valor::INTEGER = 4 THEN COALESCE(ep.legenda4, ep.rotulo4) 
						                                   WHEN er.valor::INTEGER = 5 THEN COALESCE(ep.legenda5, ep.rotulo5) 
						                                   WHEN er.valor::INTEGER = 6 THEN COALESCE(ep.legenda6, ep.rotulo6) 
						                                   WHEN er.valor::INTEGER = 7 THEN COALESCE(ep.legenda7, ep.rotulo7) 
						                                   WHEN er.valor::INTEGER = 8 THEN COALESCE(ep.legenda8, ep.rotulo8) 
						                                   WHEN er.valor::INTEGER = 9 THEN COALESCE(ep.legenda9, ep.rotulo9) 
						                                   WHEN er.valor::INTEGER = 10 THEN COALESCE(ep.legenda10, ep.rotulo10) 
						                                   WHEN er.valor::INTEGER = 11 THEN COALESCE(ep.legenda11, ep.rotulo11) 
                                                           WHEN er.valor::INTEGER = 12 THEN COALESCE(ep.legenda12, ep.rotulo12)
                                                           WHEN er.valor::INTEGER = 13 THEN COALESCE(ep.legenda13, ep.rotulo13)
                                                           WHEN er.valor::INTEGER = 14 THEN COALESCE(ep.legenda14, ep.rotulo14)
						                                   WHEN er.valor::INTEGER = 15 THEN COALESCE(ep.legenda15, ep.rotulo15)
									                       ELSE er.valor::TEXT
								                      END
							                     FROM projetos.enquete_perguntas ep 
							                    WHERE ep.cd_enquete  = er.cd_enquete 
							                      AND ep.cd_pergunta = ".intval($args['cd_pergunta'])."), er.valor::TEXT)
						   END AS ds_resposta,
			               (SELECT COUNT(*)
			                  FROM projetos.enquete_resultados er1
			                 WHERE er1.cd_enquete  = er.cd_enquete
						       AND er1.questao     = ('R_'::TEXT || ".intval($args['cd_pergunta'])."::TEXT)
							   AND er1.valor       = er.valor
					           AND COALESCE(er1.complemento,'') <> '') AS qt_complemento,
					       COUNT(er.valor) AS qt_resposta
                      FROM projetos.enquete_resultados er
                     WHERE er.cd_enquete = ".intval($args['cd_enquete'])."
					   AND er.questao::TEXT = ('R_'::TEXT || ".intval($args['cd_pergunta'])."::TEXT)
                       ".(((trim($args["dt_ini"]) != "") and (trim($args["dt_fim"]) != "")) ? "AND CAST(er.dt_resposta AS DATE) BETWEEN TO_DATE('".trim($args["dt_ini"])."', 'DD/MM/YYYY') AND TO_DATE('".trim($args["dt_fim"])."', 'DD/MM/YYYY')" : "")."
			      GROUP BY cd_resposta, ds_resposta, qt_complemento 
                  ORDER BY qt_resposta DESC			
                  ";
        $result = $this->db->query($qr_sql);
    }	
	
    function resultadoVerComplemento(&$result, $args=array())
    {
        $qr_sql = "
					SELECT er.complemento 
					  FROM projetos.enquete_resultados er
					 WHERE er.cd_enquete    = ".intval($args['cd_enquete'])."
					   AND er.questao::TEXT = 'R_".intval($args['cd_pergunta'])."'::TEXT
					   AND er.valor         = ".floatval($args['cd_resposta'])."
					   AND COALESCE(er.complemento,'') <> ''
					   ".(((trim($args["dt_ini"]) != "") and  (trim($args["dt_fim"]) != "")) ? "AND CAST(er.dt_resposta AS DATE) BETWEEN TO_DATE('".trim($args["dt_ini"])."', 'DD/MM/YYYY') AND TO_DATE('".trim($args["dt_fim"])."', 'DD/MM/YYYY')" : "")."
					ORDER BY er.dt_resposta
                  ";
        $result = $this->db->query($qr_sql);
    }	
	
    function combo_area_responsavel(&$result, $args=array())
    {
        $qr_sql = "
                    SELECT d.codigo AS value,
                           d.nome AS text
                      FROM projetos.divisoes d
                     ORDER BY text
                  ";
        $result = $this->db->query($qr_sql);
    } 

    function combo_agrupamento(&$result, $args=array())
    {
        $qr_sql = "
                    SELECT cd_agrupamento AS value,
                           nome AS text
                      FROM projetos.enquete_agrupamentos
                     WHERE dt_exclusao IS NULL
                       AND cd_enquete  = ".intval($args['cd_enquete'])."
                     ORDER BY text
                  ";
        $result = $this->db->query($qr_sql);
    }   
}
?>