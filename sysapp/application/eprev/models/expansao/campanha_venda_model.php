<?php
class campanha_venda_model extends Model
{
    function __construct()
    {
        parent::Model();
    }
    
    function listar( &$result, $args=array() )
    {
        
        $qr_sql = "
					SELECT cv.cd_campanha_venda, 
						   cv.ds_campanha_venda, 
						   cv.cd_empresa, 
						   cv.sigla AS empresa, 
						   TO_CHAR(cv.dt_inicio, 'DD/MM/YYYY') AS dt_inicio,
						   TO_CHAR(cv.dt_final, 'DD/MM/YYYY') AS dt_final,
						   TO_CHAR(cv.dt_cadastro, 'DD/MM/YYYY') AS dt_cadastro,
						   TO_CHAR(cv.dt_ingresso, 'DD/MM/YYYY') AS dt_ingresso,
						   cv.qt_cpf, 
						   cv.qt_contato, 
						   cv.qt_contato_cpf, 
						   cv.qt_nao_encontrado, 
						   cv.qt_nao_encontrado_cpf, 
						   cv.qt_em_negociacao, 
						   cv.qt_em_negociacao_cpf,					   
						   cv.qt_proposta, 
						   cv.qt_proposta_cpf, 
						   cv.qt_inscrito, 
						   cv.qt_inscrito_cpf, 
						   cv.qt_ingresso, 
						   cv.qt_ingresso_cpf, 					   
						   cv.qt_agenda, 
						   cv.qt_agenda_cpf	
					  FROM expansao.campanha_venda_vw cv
					 WHERE 1 = 1
					   ".(count($args['cd_empresa']) > 0 ? "AND cv.cd_empresa IN (".implode(",",$args['cd_empresa']).")" : "")."
					   ".(((trim($args['dt_ini']) != "") AND (trim($args['dt_fim']) != "")) ? " AND DATE_TRUNC('day', cv.dt_inicio) BETWEEN TO_DATE('".$args['dt_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_fim']."', 'DD/MM/YYYY')" : "")."
					 ORDER BY cv.cd_campanha_venda DESC  
				  ";
		#echo "<PRE>$qr_sql</PRE>";
        $result = $this->db->query($qr_sql);
    }
    
    function empresa( &$result, $args=array() )
    {
        $qr_sql = "
            SELECT cd_empresa AS value,
                   sigla AS text
              FROM public.patrocinadoras 
             WHERE tipo_cliente = 'I';";
                         
        $result = $this->db->query($qr_sql);
    }
    
    function carrega( &$result, $args=array() )
    {
        $qr_sql = "
            SELECT cd_campanha_venda,
                   ds_campanha_venda,
                   cd_empresa,
                   TO_CHAR(dt_inicio, 'DD/MM/YYYY') AS dt_inicio,
                   TO_CHAR(dt_final, 'DD/MM/YYYY') AS dt_final,
                   TO_CHAR(dt_cadastro, 'DD/MM/YYYY') AS dt_cadastro,
                   TO_CHAR(dt_ingresso, 'DD/MM/YYYY') AS dt_ingresso,
                   TO_CHAR(dt_fechamento, 'DD/MM/YYYY HH24:MI:SS') AS dt_fechamento
              FROM expansao.campanha_venda
             WHERE cd_campanha_venda = ".intval($args['cd_campanha_venda']).";";
                         
        $result = $this->db->query($qr_sql);
    }
    
    function salvar( &$result, $args=array() )
    {
        if(intval($args['cd_campanha_venda']) == 0)
        {
            $cd_campanha_venda = intval($this->db->get_new_id("expansao.campanha_venda", "cd_campanha_venda"));
        
            $qr_sql = "
                INSERT INTO expansao.campanha_venda
                     (
                       cd_campanha_venda, 
                       ds_campanha_venda, 
                       cd_empresa,
                       dt_inicio, 
                       dt_final,
                       dt_cadastro,
                       dt_ingresso,
                       cd_usuario_inclusao,
                       cd_usuario_alteracao
                     )
                VALUES 
                     (
                       ".intval($cd_campanha_venda).",
                       ".(trim($args['ds_campanha_venda']) != '' ? str_escape($args['ds_campanha_venda']) : "DEFAULT").",
                       ".(trim($args['cd_empresa']) != '' ? intval($args['cd_empresa']) : "DEFAULT").",
                       ".(trim($args['dt_inicio']) != '' ? "TO_DATE('".trim($args['dt_inicio'])."', 'DD/MM/YYYY')" : "DEFAULT").",
                       ".(trim($args['dt_final']) != '' ? "TO_DATE('".trim($args['dt_final'])."', 'DD/MM/YYYY')" : "DEFAULT").",
                       ".(trim($args['dt_cadastro']) != '' ? "TO_DATE('".trim($args['dt_cadastro'])."', 'DD/MM/YYYY')" : "DEFAULT").",
                       ".(trim($args['dt_ingresso']) != '' ? "TO_DATE('".trim($args['dt_ingresso'])."', 'DD/MM/YYYY')" : "DEFAULT").",
                       ".intval($args['cd_usuario']).",
                       ".intval($args['cd_usuario'])."
                     );";
        }
        else
        {
            $cd_campanha_venda = intval($args['cd_campanha_venda']);
        
            $qr_sql = "
                UPDATE expansao.campanha_venda
                   SET ds_campanha_venda    = ".(trim($args['ds_campanha_venda']) != '' ? str_escape($args['ds_campanha_venda']) : "DEFAULT").",
                       cd_empresa           = ".(trim($args['cd_empresa']) != '' ? intval($args['cd_empresa']) : "DEFAULT").",
                       dt_inicio            = ".(trim($args['dt_inicio']) != '' ? "TO_DATE('".trim($args['dt_inicio'])."', 'DD/MM/YYYY')" : "DEFAULT").",
                       dt_final             = ".(trim($args['dt_final']) != '' ? "TO_DATE('".trim($args['dt_final'])."', 'DD/MM/YYYY')" : "DEFAULT").",
                       dt_cadastro          = ".(trim($args['dt_cadastro']) != '' ? "TO_DATE('".trim($args['dt_cadastro'])."', 'DD/MM/YYYY')" : "DEFAULT").",
                       dt_ingresso          = ".(trim($args['dt_ingresso']) != '' ? "TO_DATE('".trim($args['dt_ingresso'])."', 'DD/MM/YYYY')" : "DEFAULT").",
                       cd_usuario_alteracao = ".intval($args['cd_usuario']).",
                       dt_alteracao         = CURRENT_TIMESTAMP                       
                 WHERE cd_campanha_venda = ".intval($cd_campanha_venda).";";
        }
                         
        $result = $this->db->query($qr_sql);
    }
    
    function excluir( &$result, $args=array() )
    {
        $qr_sql = "
                UPDATE expansao.campanha_venda
                   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
                       dt_exclusao         = CURRENT_TIMESTAMP                       
                 WHERE cd_campanha_venda = ".intval($args['cd_campanha_venda']).";";
                         
        $result = $this->db->query($qr_sql);
    }
    
    function comboDelegacia( &$result, $args=array() )
    {
        $qr_sql = "
					SELECT x.value, x.text
					  FROM (
							SELECT DISTINCT COALESCE(delegacia,'') AS value,
								   COALESCE(delegacia,'') AS text
							  FROM familia_previdencia.afceee_cadastro 
							 UNION  
							SELECT DISTINCT COALESCE(delegacia,'') AS value,
								   COALESCE(delegacia,'') AS text
							  FROM familia_previdencia.cadastro
					       ) x
                     ORDER BY x.text;
                  ";        
        $result = $this->db->query($qr_sql);        
    }
    
    function comboCidade( &$result, $args=array() )
    {
        $qr_sql = "
					SELECT x.value, x.text
					  FROM (
							SELECT DISTINCT TRIM(COALESCE(cidade,'')) AS value,
								   TRIM(COALESCE(cidade,'')) AS text
							  FROM familia_previdencia.afceee_cadastro 
							 UNION  
							SELECT DISTINCT TRIM(COALESCE(cidade,'')) AS value,
								   TRIM(COALESCE(cidade,'')) AS text
							  FROM familia_previdencia.cadastro
					       ) x
                     ORDER BY x.text;
                  ";        
        $result = $this->db->query($qr_sql);
    }
	
    function comboTipoParticipante( &$result, $args=array() )
    {
        $qr_sql = "
					SELECT x.value, x.text
					  FROM (
							SELECT DISTINCT COALESCE(tipo_participante,'') AS value,
								   COALESCE(tipo_participante,'') AS text
							  FROM familia_previdencia.afceee_cadastro 
							 UNION  
							SELECT DISTINCT COALESCE(tipo_participante,'') AS value,
								   COALESCE(tipo_participante,'') AS text
							  FROM familia_previdencia.cadastro
					       ) x
                     ORDER BY x.text;
                  ";
        $result = $this->db->query($qr_sql);
    }	
    
    function comboIdade(&$result, $args=array())
    {
        $qr_sql = "
                    SELECT x.column2 AS value,
                           x.column2 AS text
                      FROM (VALUES 
                                    (1,'00 e 10'),
                                    (2,'00 e 17'),
                                    (3,'11 e 20'),
                                    (4,'21 e 30'),
                                    (5,'31 e 40'),
                                    (6,'41 e 50'),
                                    (7,'51 e 60'),
                                    (8,'61 e 70'),
                                    (9,'71 e 80'),
                                    (10,'91 e 100'),
                                    (11,'+ de 100'),
                                    (12,'Não identificado')
                           ) x
                     ORDER BY x.column1     
                  ";
             
        $result = $this->db->query($qr_sql);    
    }   
    
    function comboRenda(&$result, $args=array())
    {
        $qr_sql = "
                    SELECT x.column2 AS value,
                           x.column2 AS text
                      FROM (VALUES 
                                    (1,'0,01 e 1.000,00'),
                                    (2,'1.000,01 e 2.500,00'),
                                    (3,'2.500,01 e 5.000,00'),
                                    (4,'6.000,01 e 7.500,00'),
                                    (5,'7.500,01 e 10.000,00'),
                                    (6,'10.000,01 e 15.000,00'),
                                    (7,'15.000,01 e 20.000,00'),
                                    (8,'20.000,01 e 30.000,00'),
                                    (9,'+ de 30.000,00'),
                                    (10,'Não identificado')
                           ) x
                     ORDER BY x.column1
                  ";
        $result = $this->db->query($qr_sql);    
    }
    
    private function whereIdade($ar_where)
    {
        return "
                    AND (CASE WHEN COALESCE(EXTRACT(years FROM AGE(x.dt_nascimento)),-1) BETWEEN 00 AND 10 THEN '00 e 10'
					          WHEN COALESCE(EXTRACT(years FROM AGE(x.dt_nascimento)),-1) BETWEEN 00 AND 17 THEN '00 e 17'
                              WHEN COALESCE(EXTRACT(years FROM AGE(x.dt_nascimento)),-1) BETWEEN 11 AND 20 THEN '11 e 20'
                              WHEN COALESCE(EXTRACT(years FROM AGE(x.dt_nascimento)),-1) BETWEEN 21 AND 30 THEN '21 e 30'
                              WHEN COALESCE(EXTRACT(years FROM AGE(x.dt_nascimento)),-1) BETWEEN 31 AND 40 THEN '31 e 40'
                              WHEN COALESCE(EXTRACT(years FROM AGE(x.dt_nascimento)),-1) BETWEEN 41 AND 50 THEN '41 e 50'
                              WHEN COALESCE(EXTRACT(years FROM AGE(x.dt_nascimento)),-1) BETWEEN 51 AND 60 THEN '51 e 60'
                              WHEN COALESCE(EXTRACT(years FROM AGE(x.dt_nascimento)),-1) BETWEEN 61 AND 70 THEN '61 e 70'
                              WHEN COALESCE(EXTRACT(years FROM AGE(x.dt_nascimento)),-1) BETWEEN 71 AND 80 THEN '71 e 80'
                              WHEN COALESCE(EXTRACT(years FROM AGE(x.dt_nascimento)),-1) BETWEEN 81 AND 90 THEN '81 e 90'
                              WHEN COALESCE(EXTRACT(years FROM AGE(x.dt_nascimento)),-1) BETWEEN 91 AND 100 THEN '91 e 100'
                              WHEN COALESCE(EXTRACT(years FROM AGE(x.dt_nascimento)),-1) > 100 THEN '+ de 100'
                              ELSE 'Não identificado'
                        END)
                    IN ('".implode("','",$ar_where)."')
               ";
    }   
    
    private function whereIdadeDependente($ar_where)
    {
        return "
                    AND 0 < (SELECT COUNT(*)
                               FROM public.dependentes d,
                                    public.participantes pd
                              WHERE d.cd_empresa             = x.cd_empresa
                                AND d.cd_registro_empregado  = x.cd_registro_empregado
                                AND pd.cd_empresa            = d.cd_empresa
                                AND pd.cd_registro_empregado = d.cd_registro_empregado
                                AND pd.seq_dependencia       = d.seq_dependencia
                                AND (CASE WHEN COALESCE(EXTRACT(years FROM AGE(pd.dt_nascimento)),-1) BETWEEN 00 AND 10 THEN '00 e 10'
                                          WHEN COALESCE(EXTRACT(years FROM AGE(pd.dt_nascimento)),-1) BETWEEN 00 AND 17 THEN '00 e 17'
                                          WHEN COALESCE(EXTRACT(years FROM AGE(pd.dt_nascimento)),-1) BETWEEN 11 AND 20 THEN '11 e 20'
                                          WHEN COALESCE(EXTRACT(years FROM AGE(pd.dt_nascimento)),-1) BETWEEN 21 AND 30 THEN '21 e 30'
                                          WHEN COALESCE(EXTRACT(years FROM AGE(pd.dt_nascimento)),-1) BETWEEN 31 AND 40 THEN '31 e 40'
                                          WHEN COALESCE(EXTRACT(years FROM AGE(pd.dt_nascimento)),-1) BETWEEN 41 AND 50 THEN '41 e 50'
                                          WHEN COALESCE(EXTRACT(years FROM AGE(pd.dt_nascimento)),-1) BETWEEN 51 AND 60 THEN '51 e 60'
                                          WHEN COALESCE(EXTRACT(years FROM AGE(pd.dt_nascimento)),-1) BETWEEN 61 AND 70 THEN '61 e 70'
                                          WHEN COALESCE(EXTRACT(years FROM AGE(pd.dt_nascimento)),-1) BETWEEN 71 AND 80 THEN '71 e 80'
                                          WHEN COALESCE(EXTRACT(years FROM AGE(pd.dt_nascimento)),-1) BETWEEN 81 AND 90 THEN '81 e 90'
                                          WHEN COALESCE(EXTRACT(years FROM AGE(pd.dt_nascimento)),-1) BETWEEN 91 AND 100 THEN '91 e 100'
                                          WHEN COALESCE(EXTRACT(years FROM AGE(pd.dt_nascimento)),-1) > 100 THEN '+ de 100'
                                          ELSE 'Não identificado'
                                    END) IN ('".implode("','",$ar_where)."'))
               ";
    }   
    
    private function whereRenda($ar_where)
    {
        return "
                AND (CASE WHEN COALESCE(x.vl_renda,-1) = 0 THEN 'Sem'
                                  WHEN COALESCE(x.vl_renda,-1) BETWEEN 0000.01 AND 1000 THEN '0,01 e 1.000,00'
                                  WHEN COALESCE(x.vl_renda,-1) BETWEEN 1000.01 AND 2500 THEN '1.000,01 e 2.500,00'
                                  WHEN COALESCE(x.vl_renda,-1) BETWEEN 2500.01 AND 5000 THEN '2.500,01 e 5.000,00'
                                  WHEN COALESCE(x.vl_renda,-1) BETWEEN 5000.01 AND 7500 THEN '6.000,01 e 7.500,00'
                                  WHEN COALESCE(x.vl_renda,-1) BETWEEN 7500.01 AND 10000 THEN '7.500,01 e 10.000,00'
                                  WHEN COALESCE(x.vl_renda,-1) BETWEEN 10000.01 AND 15000 THEN '10.000,01 e 15.000,00'
                                  WHEN COALESCE(x.vl_renda,-1) BETWEEN 15000.01 AND 20000 THEN '15.000,01 e 20.000,00'
                                  WHEN COALESCE(x.vl_renda,-1) BETWEEN 20000.01 AND 30000 THEN '20.000,01 e 30.000,00'
                                  WHEN COALESCE(x.vl_renda,-1) > 30000.01 THEN '+ de 30.000,00'
                                  ELSE 'Não identificado'
                    END) IN ('".implode("','",$ar_where)."')                               
               ";
    } 
    
    function familia_listar( &$result, $args=array() )
    {
        $qr_sql = "
            SELECT ci.cd_campanha_venda_item,
                   ci.cd_campanha_venda,
                   ".intval($args['cd_campanha_venda'])." AS cd_campanha_venda_new,
                   (SELECT COUNT(*) FROM expansao.campanha_venda cv WHERE cv.cd_campanha_venda = ".intval($args['cd_campanha_venda'])." AND cv.dt_final > CURRENT_TIMESTAMP) AS fl_edita_campanha,
                   x.cd_origem,
                   x.nome, 
                   x.cpf,
                   TO_CHAR(x.dt_nascimento, 'DD/MM/YYYY') AS dt_nascimento,
                   COALESCE(EXTRACT(years FROM AGE(x.dt_nascimento)),-1) AS nr_idade,
                   x.endereco, 
                   x.bairro, 
                   x.cidade, 
                   x.cep, 
                   x.uf, 
                   x.telefone_1, 
                   x.telefone_2,  
                   x.email_1, 
                   x.email_2,
                   x.delegacia,
                   x.origem,
                   (SELECT COUNT(*)
                      FROM public.dependentes d
                     WHERE d.cd_empresa = x.cd_empresa
                       AND d.cd_registro_empregado = x.cd_registro_empregado) AS qt_dependente,
                   x.vl_renda
              FROM (
                    SELECT af.cd_cadastro_afceee AS cd_origem,
                           af.nome, 
                           af.cpf,
                           af.dt_nascimento::DATE AS dt_nascimento,
                           convert_from(convert_to(COALESCE(af.endereco,''),'utf-8'),'latin-1') AS endereco,
                           convert_from(convert_to(COALESCE(af.bairro,''),'utf-8'),'latin-1') AS bairro,
                           af.cidade AS cidade,
                           af.cep, 
                           af.uf, 
						   COALESCE(af.telefone,'') AS telefone_1, 
						   COALESCE(af.telefone_2,'') AS telefone_2,  
						   COALESCE(af.email_1,'') AS email_1, 
						   COALESCE(af.email_2,'') AS email_2,
                           af.delegacia,
						   af.vl_renda,
						   af.cd_empresa,
						   af.cd_registro_empregado,
						   af.seq_dependencia,
                           'AFCEEE' AS origem
                      FROM familia_previdencia.afceee_cadastro af
                     WHERE af.cpf IS NOT NULL
					   ".(count($args['cd_empresa']) > 0 ? "AND af.cd_empresa IN (".implode(",",$args['cd_empresa']).")" : "")."
					   ".(trim($args['nome']) != "" ? "AND funcoes.remove_acento(UPPER(af.nome)) LIKE funcoes.remove_acento(UPPER('%".utf8_decode(trim($args['nome']))."%'))" : "" )."
					   ".(trim($args['bairro']) != "" ? "AND funcoes.remove_acento(UPPER(af.bairro)) LIKE funcoes.remove_acento(UPPER('%".utf8_decode(trim($args['bairro']))."%'))" : "" )."
					   ".(is_array($args["ar_delegacia"]) ? "AND af.delegacia IN ('".implode("','",$args['ar_delegacia'])."')" : "" )."
					   ".(is_array($args["ar_cidade"]) ? "AND TRIM(COALESCE(af.cidade,'')) IN ('".implode("','",$args['ar_cidade'])."')" : "" )."
					   ".(is_array($args["ar_tipo_participante"]) ? "AND projetos.participante_tipo(af.cd_empresa, af.cd_registro_empregado, af.seq_dependencia) IN ('".implode("','",$args['ar_tipo_participante'])."')" : "" )."
                       AND 0 = (SELECT COUNT(*) 
                                  FROM familia_previdencia.cadastro c 
                                 WHERE c.cpf = af.cpf)
                       AND af.cpf NOT IN (SELECT funcoes.format_cpf(p.cpf_mf) 
                                            FROM public.participantes p 
                                           WHERE p.cd_empresa IN (19,20)
                                             AND p.seq_dependencia = 0)
                     UNION
					 
                    SELECT af.cd_sintec_cadastro AS cd_origem,
                           af.nome, 
                           af.cpf,
                           af.dt_nascimento::DATE AS dt_nascimento,
                           convert_from(convert_to(COALESCE(af.endereco,''),'utf-8'),'latin-1') AS endereco,
                           convert_from(convert_to(COALESCE(af.bairro,''),'utf-8'),'latin-1') AS bairro,
                           af.cidade AS cidade,
                           af.cep, 
                           af.uf, 
						   COALESCE(af.telefone,'') AS telefone_1, 
						   COALESCE(af.telefone_2,'') AS telefone_2,  
						   COALESCE(af.email_1,'') AS email_1, 
						   COALESCE(af.email_2,'') AS email_2,
                           NULL AS delegacia,
						   af.vl_renda,
						   af.cd_empresa,
						   af.cd_registro_empregado,
						   af.seq_dependencia,
                           'SINTEC' AS origem
                      FROM familia_previdencia.sintec_cadastro af
                     WHERE af.cpf IS NOT NULL
					   ".(count($args['cd_empresa']) > 0 ? "AND af.cd_empresa IN (".implode(",",$args['cd_empresa']).")" : "")."
					   ".(trim($args['nome']) != "" ? "AND funcoes.remove_acento(UPPER(af.nome)) LIKE funcoes.remove_acento(UPPER('%".utf8_decode(trim($args['nome']))."%'))" : "" )."
					   ".(trim($args['bairro']) != "" ? "AND funcoes.remove_acento(UPPER(af.bairro)) LIKE funcoes.remove_acento(UPPER('%".utf8_decode(trim($args['bairro']))."%'))" : "" )."
					   ".(is_array($args["ar_cidade"]) ? "AND TRIM(COALESCE(af.cidade,'')) IN ('".implode("','",$args['ar_cidade'])."')" : "" )."
					   ".(is_array($args["ar_tipo_participante"]) ? "AND projetos.participante_tipo(af.cd_empresa, af.cd_registro_empregado, af.seq_dependencia) IN ('".implode("','",$args['ar_tipo_participante'])."')" : "" )."
                       ".(is_array($args["ar_delegacia"]) ? "AND 0=1" : "" )."
                       AND 0 = (SELECT COUNT(*) 
                                  FROM familia_previdencia.cadastro c 
                                 WHERE c.cpf = af.cpf)
					   AND 0 = (SELECT COUNT(*) 
								  FROM familia_previdencia.afceee_cadastro ac 
								 WHERE ac.cpf = af.cpf)									 
                       AND af.cpf NOT IN (SELECT funcoes.format_cpf(p.cpf_mf) 
                                            FROM public.participantes p 
                                           WHERE p.cd_empresa IN (19,20)
                                             AND p.seq_dependencia = 0)
                     UNION					 

                    SELECT c.cd_cadastro AS cd_origem,
                           c.nome, 
                           c.cpf,
                           c.dt_nascimento,
                           c.endereco, 
                           c.bairro, 
                           c.cidade, 
                           c.cep, 
                           c.uf, 
						   COALESCE(c.telefone,'') AS telefone_1, 
						   COALESCE(c.celular,'') AS telefone_2,  
						   COALESCE(c.email,'') AS email_1, 
						   COALESCE(c.email_2,'') AS email_2,
                           c.delegacia,
						   c.vl_renda,
						   c.cd_empresa,
						   c.cd_registro_empregado,
						   c.seq_dependencia,						   
                           'CADASTRO' AS origem
                      FROM familia_previdencia.cadastro c
                     WHERE c.cpf IS NOT NULL
					   ".(count($args['cd_empresa']) > 0 ? "AND c.cd_empresa IN (".implode(",",$args['cd_empresa']).")" : "")."
					   ".(trim($args['nome']) != "" ? "AND funcoes.remove_acento(UPPER(c.nome)) LIKE funcoes.remove_acento(UPPER('%".utf8_decode(trim($args['nome']))."%'))" : "" )."
					   ".(trim($args['bairro']) != "" ? "AND funcoes.remove_acento(UPPER(c.bairro)) LIKE funcoes.remove_acento(UPPER('%".utf8_decode(trim($args['bairro']))."%'))" : "" )."
					   ".(is_array($args["ar_delegacia"]) ? "AND c.delegacia IN ('".implode("','",$args['ar_delegacia'])."')" : "" )."
					   ".(is_array($args["ar_cidade"]) ? "AND TRIM(COALESCE(c.cidade,'')) IN ('".implode("','",$args['ar_cidade'])."')" : "" )."
					   ".(is_array($args["ar_tipo_participante"]) ? "AND projetos.participante_tipo(c.cd_empresa, c.cd_registro_empregado, c.seq_dependencia) IN ('".implode("','",$args['ar_tipo_participante'])."')" : "" )."
                       AND c.fl_inscrito <> 'S' 
                       /*AND 0 = (SELECT COUNT(*) 
                                  FROM familia_previdencia.afceee_cadastro af 
                                 WHERE af.cpf = c.cpf)*/
                    ) x
             LEFT JOIN expansao.campanha_venda_item ci
               ON ci.cpf = x.cpf
              AND ci.ds_origem = x.origem
              AND ci.cd_origem = x.cd_origem 
              AND ci.cd_campanha_venda = ".intval($args['cd_campanha_venda'])."
              AND ci.dt_exclusao IS NULL

             WHERE 1 = 1
             ".(trim($args['cpf']) != "" ? "AND x.cpf = '".trim($args['cpf'])."'" : "" )."
             ".(trim($args['fl_incluido']) == "S" ? "AND ci.dt_inclusao IS NOT NULL" : "" )."
             ".(trim($args['fl_incluido']) == "N" ? "AND ci.dt_inclusao IS NULL" : "" )."
             ".(is_array($args["ar_idade"]) ? $this->whereIdade($args["ar_idade"]) : "")."
             ".(is_array($args["ar_idade_dependente"]) ? $this->whereIdadeDependente($args["ar_idade_dependente"]) : "")."
             ".(is_array($args["ar_renda"]) ? $this->whereRenda($args["ar_renda"]) : "")."
			 ".(is_array($args["ar_origem"]) ? "AND x.origem IN ('".implode("','",$args['ar_origem'])."')" : "" )."
			 
             ORDER BY x.nome;";
        
        #echo "<PRE style='text-align:left;'>".$qr_sql."</PRE>";exit;
        
        $result = $this->db->query($qr_sql);
    }
    
    function comboCampanha( &$result, $args=array() )
    {
        $qr_sql = "
					SELECT cd_campanha_venda AS value,
						   ds_campanha_venda AS text
					  FROM expansao.campanha_venda
					 WHERE dt_exclusao IS NULL
					   AND cd_empresa IN (".implode(",",$args['cd_empresa']).")
                  ";
               
        $result = $this->db->query($qr_sql);
    }
    
    function salvar_item( &$result, $args=array() )
    {
        $cd_campanha_venda_item = intval($this->db->get_new_id("expansao.campanha_venda_item", "cd_campanha_venda_item"));
    
        $qr_sql = "
            INSERT INTO expansao.campanha_venda_item
                (
                   cd_campanha_venda_item,
                   cd_campanha_venda, 
                   cpf,
                   ds_origem,
                   cd_origem,
                   cd_usuario_inclusao
                )
            VALUES 
                (
                    ".intval($cd_campanha_venda_item).",
                    ".(trim($args['cd_campanha_venda']) != '' ? intval($args['cd_campanha_venda']) : "DEFAULT").",
                    ".(trim($args['cpf']) != '' ? "'".trim($args['cpf'])."'" : "DEFAULT").",
                    ".(trim($args['ds_origem']) != '' ? "'".trim($args['ds_origem'])."'" : "DEFAULT").",
                    ".(trim($args['cd_origem']) != '' ? intval($args['cd_origem']) : "DEFAULT").",
                    ".intval($args['cd_usuario'])."
                );";
        
        $result = $this->db->query($qr_sql);
        
        return $cd_campanha_venda_item;
    }
    
    function fechar_campanha(&$result, $args=array())
    {
        $qr_sql = "
            UPDATE expansao.campanha_venda
               SET cd_usuario_fechamento  = ".intval($args['cd_usuario']).",
                   dt_fechamento          = CURRENT_TIMESTAMP
             WHERE cd_campanha_venda = ".intval($args['cd_campanha_venda']).";";

        $result = $this->db->query($qr_sql);
    }
    
    function excluir_item( &$result, $args=array() )
    {
        $qr_sql = "
            UPDATE expansao.campanha_venda_item
               SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
                   dt_exclusao         = CURRENT_TIMESTAMP
             WHERE cd_campanha_venda_item = ".intval($args['cd_campanha_venda_item']).";";

        $result = $this->db->query($qr_sql);
    }

    function excluir_item_origem( &$result, $args=array() )
    {
        $qr_sql = "
            UPDATE expansao.campanha_venda_item
               SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
                   dt_exclusao         = CURRENT_TIMESTAMP
             WHERE cd_campanha_venda = ".intval($args['cd_campanha_venda'])."
               AND ds_origem         = '".trim($args['ds_origem'])."'
               AND cd_origem         = ".intval($args['cd_origem']).";";

        $result = $this->db->query($qr_sql);
    }
    
    function salvar_all_item( &$result, $args=array() )
    {
        $qr_sql = "   
            UPDATE expansao.campanha_venda_item
               SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
                   dt_exclusao         = CURRENT_TIMESTAMP
             WHERE cd_campanha_venda = ".intval($args['cd_campanha_venda'])."
               AND dt_exclusao       IS NULL
               AND cpf IN ('".implode("','", $args['arr_not_cpf'])."');
               
               
            INSERT INTO expansao.campanha_venda_item  (cpf, cd_campanha_venda, cd_usuario_inclusao)
            SELECT x.column1, ".intval($args['cd_campanha_venda']).", ".intval($args['cd_usuario'])."
              FROM (VALUES ('".implode("'),('", $args['arr_cpf'])."')) x
             WHERE x.column1 NOT IN (SELECT a.cpf
                                       FROM expansao.campanha_venda_item a
                                      WHERE a.cd_campanha_venda = ".intval($args['cd_campanha_venda'])."
                                        AND a.dt_exclusao IS NULL);
               ";
    
        $result = $this->db->query($qr_sql);
        
    }
}
?>