<?php
class Nao_conformidade_model extends Model
{
    function __construct()
    {
        parent::Model();
    }

    function listar(&$result, $args=array())
    {
        // consistencia

        if (trim($args["limite_apre_ac_inicio"]) != '' OR trim($args["limite_apre_ac_fim"]) != '')
        {
            if (trim($args["limite_apre_ac_inicio"]) == '' OR trim($args["limite_apre_ac_fim"]) == '')
            {
                echo "Para filtrar por intervalo de datas é necessário informar as duas datas.";
                return false;
                exit;
            }
        }

        if (trim($args["proposta_inicio"]) != '' OR trim($args["proposta_fim"]) != '')
        {
            if (trim($args["proposta_inicio"]) == '' OR trim($args["proposta_fim"]) == '')
            {
                echo "Para filtrar por intervalo de datas é necessário informar as duas datas.";
                return false;
                exit;
            }
        }

        // mount query
        $where_data_limite = '';
        if (trim($args["limite_apre_ac_inicio"]) != '' && trim($args["limite_apre_ac_fim"]) != '')
        {
            $where_data_limite = " AND DATE_TRUNC('DAY', COALESCE(ac.dt_limite_apres,(nc.dt_cadastro + '15 days'::interval))) BETWEEN TO_DATE('{dlac_inicio}','DD/MM/YYYY') AND TO_DATE('{dlac_fim}','DD/MM/YYYY') ";
        }

        $where_data_proposta = '';
        if (trim($args["proposta_inicio"]) != '' && trim($args["proposta_fim"]) != '')
        {
            $where_data_proposta = " AND CAST(COALESCE(ac.dt_prorrogada, ac.dt_prop_imp) AS DATE) BETWEEN TO_DATE('{dpp_inicio}','DD/MM/YYYY') AND TO_DATE('{dpp_fim}','DD/MM/YYYY') ";
        }

        $where_diretoria = '';
        if ($args['diretoria'] != '')
        {
            $where_diretoria = "
			AND (CASE WHEN nc.cd_responsavel = 0
				  THEN nc.aberto_por     IN (SELECT codigo 
                                                               FROM projetos.usuarios_controledi 
                                                              WHERE divisao IN (SELECT codigo
                                                                                  FROM projetos.divisoes
                                                                                 WHERE area = '{diretoria}'))
				  ELSE nc.cd_responsavel IN (SELECT codigo 
                                                               FROM projetos.usuarios_controledi 
                                                              WHERE divisao IN (SELECT codigo
                                                                                  FROM projetos.divisoes
                                                                                 WHERE area = '{diretoria}'))
			END)
			";
        }

        $where_gerencia = "";
        if ($args['gerencia'] != '')
        {
            $where_gerencia = "
			AND (CASE WHEN nc.cd_responsavel = 0
				  THEN nc.aberto_por     IN (SELECT codigo 
                                                               FROM projetos.usuarios_controledi 
                                                              WHERE divisao = '{gerencia}')
				  ELSE nc.cd_responsavel IN (SELECT codigo 
                                                               FROM projetos.usuarios_controledi 
                                                              WHERE divisao = '{gerencia}')
			END)
			";
        }

        $where_status = '';
        if ($args['status'] == 'EN')
        {
            $where_status = " AND nc.data_fechamento IS NOT NULL ";
        }
        if ($args['status'] == 'NE')
        {
            $where_status = " AND nc.data_fechamento IS NULL ";
        }

        $where_implementada = '';
        if ($args['implementada'] == 'S')
        {
            $where_implementada = " AND (SELECT ac.dt_efe_imp 
                                           FROM projetos.acao_corretiva ac 
                                          WHERE ac.cd_acao     = nc.cd_nao_conformidade 
                                            AND ac.cd_processo = nc.cd_processo) IS NOT NULL ";
        }
        if ($args['implementada'] == 'N')
        {
            $where_implementada = " AND (SELECT ac.dt_efe_imp 
                                           FROM projetos.acao_corretiva ac 
                                          WHERE ac.cd_acao = nc.cd_nao_conformidade 
                                            AND ac.cd_processo = nc.cd_processo) IS NULL ";
        }

        $where_prorrogada = '';
        if ($args['prorrogada'] == 'S')
        {
            $where_prorrogada = " AND ac.dt_prorrogada IS NOT NULL ";
        }
        if ($args['prorrogada'] == 'N')
        {
            $where_prorrogada = " AND ac.dt_prorrogada IS NULL ";
        }

                $sql = "
		SELECT funcoes.nr_nc(nc.nr_ano,nc.nr_nc) AS numero_cad_nc, 
			   nc.descricao, 
			   pp.procedimento, 
			   (SELECT nome
				  FROM projetos.usuarios_controledi
				 WHERE codigo = nc.aberto_por) AS nome_aberto_por, 
			   (SELECT nome
				  FROM projetos.usuarios_controledi
				 WHERE codigo = nc.cd_responsavel) AS nome_responsavel, 
				TO_CHAR(COALESCE(ac.dt_limite_apres,(nc.dt_cadastro + '15 days'::interval)),'DD/MM/YYYY') AS dt_limite_apres, 
				TO_CHAR(ac.dt_apres,'DD/MM/YYYY') AS dt_apres, 
				TO_CHAR(ac.dt_prop_imp,'DD/MM/YYYY') AS dt_prop_imp, 
				TO_CHAR(COALESCE(ac.dt_prorrogacao_verificacao_eficacia, ac.dt_prop_verif),'DD/MM/YYYY') AS dt_prop_verif, 
				TO_CHAR(ac.dt_prorrogada,'DD/MM/YYYY') AS dt_prorrogada, 
				TO_CHAR(ac.dt_efe_imp,'DD/MM/YYYY') AS dt_efe_imp, 
				TO_CHAR(nc.data_fechamento,'DD/MM/YYYY') AS dt_encerramento, 
				TO_CHAR(nc.dt_cadastro,'DD/MM/YYYY') AS dt_inclusao, 
				nc.cd_nao_conformidade, 
				nc.cd_responsavel AS cod_responsavel, 
				nc.cd_substituto AS cod_substituto, 
				nc.cd_processo, 
				(SELECT COUNT(cd_nao_conformidade)
				   FROM projetos.aviso_evento_nc
				  WHERE cd_evento = 5
					AND cd_nao_conformidade NOT IN ( SELECT cd_acao 
													   FROM projetos.acao_corretiva )
					AND cd_nao_conformidade = nc.cd_nao_conformidade) AS nr_aviso,
			   uc2.nome AS auditor,
			   uc3.nome AS substituto,
			   nc.cd_nao_conformidade_origem_evento,
			   ncoe.ds_nao_conformidade_origem_evento,
			   
			   
			   CASE WHEN COALESCE(ac.dt_prorrogada,ac.dt_prop_imp) < COALESCE(ac.dt_efe_imp, COALESCE(ac.dt_prorrogada,ac.dt_prop_imp))
			        THEN 'S'
					ELSE 'N'
			   END AS fl_implementada_fora_prazo,
			   
			   CASE WHEN COALESCE(ac.dt_prorrogada,ac.dt_prop_imp) < CURRENT_DATE AND ac.dt_efe_imp IS NULL
			        THEN 'S'
					ELSE 'N'
			   END AS fl_proposta_fora_prazo,

			   CASE WHEN COALESCE(ac.dt_limite_apres,(nc.dt_cadastro + '15 days'::interval))::DATE < COALESCE(ac.dt_apres,CURRENT_TIMESTAMP)::DATE
			        THEN 'S'
					ELSE 'N'
			   END AS fl_apresentada_fora_prazo,
         	   (SELECT TO_CHAR(a.data ,'DD/MM/YYYY HH24:MI:SS')
                  FROM projetos.acompanhamento a
                 WHERE a.cd_nao_conformidade = nc.cd_nao_conformidade
                 ORDER BY a.data DESC
                 LIMIT 1) AS dt_acompanhamento	   
			   
		  FROM projetos.nao_conformidade nc
		  JOIN projetos.nao_conformidade_origem_evento ncoe
			ON ncoe.cd_nao_conformidade_origem_evento = nc.cd_nao_conformidade_origem_evento		  
		  JOIN projetos.processos pp 
		    ON nc.cd_processo = pp.cd_processo
          LEFT JOIN projetos.acao_corretiva ac 
		    ON ac.cd_processo = nc.cd_processo 
		   AND ac.cd_nao_conformidade = nc.cd_nao_conformidade
	      LEFT JOIN gestao.nao_conformidade_auditor nca
            ON nca.cd_processo = nc.cd_processo 
          LEFT JOIN projetos.usuarios_controledi uc2
            ON nca.cd_usuario_titular = uc2.codigo
          LEFT JOIN projetos.usuarios_controledi uc3
            ON nca.cd_usuario_substituto = uc3.codigo
		 WHERE 1=1

       AND nc.dt_cancelamento IS NULL
                   ".((trim($args['cd_nao_conformidade_origem_evento']) != "") ? " AND nc.cd_nao_conformidade_origem_evento = ".intval($args['cd_nao_conformidade_origem_evento'])  : "")."
                   ".((trim($args['cd_usuario_titular']) != "") ? " AND nca.cd_usuario_titular = ".intval($args['cd_usuario_titular'])  : "")."
                   ".((trim($args['cd_usuario_substituto']) != "") ? " AND nca.cd_usuario_substituto = ".intval($args['cd_usuario_substituto'])  : "")."
                   ".(((trim($args['dt_prop_verif_ini']) != "") and (trim($args['dt_prop_verif_fim']) != "")) ? " AND DATE_TRUNC('day', COALESCE(ac.dt_prorrogacao_verificacao_eficacia, ac.dt_prop_verif)) BETWEEN TO_DATE('".$args['dt_prop_verif_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_prop_verif_fim']."', 'DD/MM/YYYY')" : "")."
                   ".(((trim($args['dt_encerramento_ini']) != "") and (trim($args['dt_encerramento_fim']) != "")) ? " AND DATE_TRUNC('day', nc.data_fechamento) BETWEEN TO_DATE('".$args['dt_encerramento_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_encerramento_fim']."', 'DD/MM/YYYY')" : "")."
                   ".(((trim($args['dt_cadastro_ini']) != "") and (trim($args['dt_cadastro_fim']) != "")) ? " AND DATE_TRUNC('day', nc.dt_cadastro) BETWEEN TO_DATE('".$args['dt_cadastro_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_cadastro_fim']."', 'DD/MM/YYYY')" : "")."
                   ".(((trim($args['dt_implementacao_ini']) != "") and (trim($args['dt_implementacao_fim']) != "")) ? " AND DATE_TRUNC('day', ac.dt_efe_imp) BETWEEN TO_DATE('".$args['dt_implementacao_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_implementacao_fim']."', 'DD/MM/YYYY')" : "")."
				   $where_data_limite
                   $where_data_proposta
                   $where_diretoria
                   $where_gerencia
		   AND ( nc.cd_processo = {processo} OR {processo}=0 )
                   $where_status
                   $where_implementada
                   $where_prorrogada";


		esc( "{diretoria}", $args["diretoria"], $sql );
		esc( "{gerencia}", $args["gerencia"], $sql );
		esc( "{processo}", $args["processo"], $sql, 'int' );
		esc( "{dlac_inicio}", $args["limite_apre_ac_inicio"], $sql );
		esc( "{dlac_fim}", $args["limite_apre_ac_fim"], $sql );
		esc( "{dpp_inicio}", $args["proposta_inicio"], $sql );
		esc( "{dpp_fim}", $args["proposta_fim"], $sql );

        #echo "<pre>$sql</pre>";
        $result = $this->db->query($sql);
    }

    function auditores(&$result, $args=array())
    {
        $qr_sql = "
			SELECT codigo AS value,
				   nome AS text
			  FROM projetos.usuarios_controledi
			 WHERE indic_12 = '*'
			   AND tipo <> 'X'
			 ORDER BY nome;";

        $result = $this->db->query($qr_sql);
    }
    
    function cadastro(&$result, $args=array())
    {
        $qr_sql = "
                SELECT nc.cd_nao_conformidade,
                       TO_CHAR(nc.dt_cadastro,'DD/MM/YYYY') AS dt_cadastro,
                       TO_CHAR(nc.dt_alteracao,'DD/MM/YYYY') AS dt_alteracao,
                       TO_CHAR((nc.dt_cadastro + '15 days'),'DD/MM/YYYY') AS dt_limite_apres,
                       CASE WHEN CURRENT_DATE <= CAST((nc.dt_cadastro + '15 days') AS DATE)  
                            THEN 'N'
                            ELSE 'S'
                       END AS fl_limite_apres,
                       CASE WHEN (COALESCE(nc.disposicao,'') = '' OR COALESCE(nc.causa,'') = '')
                            THEN 'N'
                            ELSE 'S'
                       END AS fl_apresenta_ac,
                       nc.cd_processo,                  
                       pp.procedimento AS ds_processo,
                       nc.descricao,                 
                       nc.disposicao,                
                       nc.evidencias,				  
                       nc.acao_corretiva,			  
                       nc.causa,                     
                       TO_CHAR(nc.data_fechamento,'DD/MM/YYYY') AS dt_encerramento,   
                       TO_CHAR(nc.dt_implementacao,'DD/MM/YYYY') AS dt_implementacao,	
                       nc.cd_responsavel,           
                       ur.nome AS ds_responsavel,
                       us.nome AS ds_substituto,
                       nc.cd_gerente,				  
                       nc.aberto_por,           
                       uc.nome AS aberto_por_nome,
                       funcoes.nr_nc(nc.nr_ano,nc.nr_nc) AS numero_cad_nc,   	      
                       pp.envolvidos,
                       COALESCE(ac.cd_nao_conformidade,0) AS fl_ac,
					   nc.cd_substituto,
					   nc.cd_nao_conformidade_origem_evento,
					   ncoe.ds_nao_conformidade_origem_evento,
					   nc.ds_analise_abrangencia
                  FROM projetos.nao_conformidade nc
				  JOIN projetos.nao_conformidade_origem_evento ncoe
				    ON ncoe.cd_nao_conformidade_origem_evento = nc.cd_nao_conformidade_origem_evento
				  LEFT JOIN projetos.processos pp                       
					ON pp.cd_processo = nc.cd_processo
				  LEFT JOIN projetos.usuarios_controledi uc
					ON uc.codigo = nc.aberto_por
				  LEFT JOIN projetos.usuarios_controledi ur
					ON ur.codigo = nc.cd_responsavel
				  LEFT JOIN projetos.usuarios_controledi us
					ON us.codigo = nc.cd_substituto
				  LEFT JOIN projetos.acao_corretiva ac
					ON ac.cd_nao_conformidade = nc.cd_nao_conformidade
                 WHERE nc.cd_nao_conformidade = " . intval($args['cd_nao_conformidade']);
        #echo "<pre>$qr_sql</pre>";	
        $result = $this->db->query($qr_sql);
    }

    function cadastroSalvar(&$result, $args=array())
    {
        $retorno = 0;

        #echo "<pre>";

        if (intval($args['cd_nao_conformidade']) > 0)
        {
            ##UPDATE
            $qr_sql = "
                    UPDATE projetos.nao_conformidade 
                       SET cd_processo                       = " . intval($args['cd_processo']) . ",    
                           descricao                         = " . (trim($args['descricao']) == "" ? "DEFAULT" : str_escape($args['descricao'])) . ",  	
                           ds_analise_abrangencia            = " . (trim($args['ds_analise_abrangencia']) == "" ? "DEFAULT" : str_escape($args['ds_analise_abrangencia'])) . ",    
                           disposicao                        = " . (trim($args['disposicao']) == "" ? "DEFAULT" : str_escape($args['disposicao'])) . ",	
                           evidencias		                 = " . (trim($args['evidencias']) == "" ? "DEFAULT" : str_escape( $args['evidencias'])) . ",
                           causa                             = " . (trim($args['causa']) == "" ? "DEFAULT" : str_escape($args['causa'])) . ",  
                           cd_nao_conformidade_origem_evento = " . (trim($args['cd_nao_conformidade_origem_evento']) == "" ? "DEFAULT" : $args['cd_nao_conformidade_origem_evento']) . ",
                           cd_responsavel                    = " . (trim($args['cd_responsavel']) == "" ? "DEFAULT" : $args['cd_responsavel']) . ",
                           cd_substituto                     = " . (trim($args['cd_substituto']) == "" ? "DEFAULT" : $args['cd_substituto']) . ",
                           cd_usuario_atualizacao            = " . (trim($args['cd_usuario']) == "" ? "DEFAULT" : $args['cd_usuario']) . ",
                           dt_alteracao                      = CURRENT_TIMESTAMP
                     WHERE cd_nao_conformidade = " . intval($args['cd_nao_conformidade']);
            $this->db->query($qr_sql);
            $retorno = intval($args['cd_nao_conformidade']);
        }
        else
        {
            ##INSERT
            $qr_sql = " 
                    INSERT INTO projetos.nao_conformidade 
                              (
                                    cd_processo,          	
                                    descricao,            	
                                    disposicao,	
                                    ds_analise_abrangencia,		
                                    evidencias,			
                                    causa,        
                                    cd_nao_conformidade_origem_evento,
                                    cd_responsavel,  
									cd_substituto,
                                    aberto_por
                              )                        	
                     VALUES 
                              (                 	
                                    " . intval($args['cd_processo']) . ", 	
                                    " . (trim($args['descricao']) == "" ? "DEFAULT" : str_escape($args['descricao'])) . ", 
                                    " . (trim($args['disposicao']) == "" ? "DEFAULT" : str_escape($args['disposicao'])) . ", 
                                    " . (trim($args['ds_analise_abrangencia']) == "" ? "DEFAULT" : str_escape($args['ds_analise_abrangencia'])) . ",
                                    " . (trim($args['evidencias']) == "" ? "DEFAULT" : str_escape( $args['evidencias'])) . ", 
                                    " . (trim($args['causa']) == "" ? "DEFAULT" : str_escape($args['causa'])). ", 
                                    " . (trim($args['cd_nao_conformidade_origem_evento']) == "" ? "DEFAULT" : $args['cd_nao_conformidade_origem_evento']) . ", 
                                    " . (trim($args['cd_responsavel']) == "" ? "DEFAULT" : $args['cd_responsavel']) . ", 
                                    " . (trim($args['cd_substituto']) == "" ? "DEFAULT" : $args['cd_substituto']) . ", 
                                    " . (trim($args['cd_usuario']) == "" ? "DEFAULT" : $args['cd_usuario']) . "
                              );";
            #echo "<pre style='text-align: left;'>$qr_sql</pre>";exit;

            $this->db->query($qr_sql);

            $qr_sql = "
                        SELECT cd_nao_conformidade
                          FROM projetos.nao_conformidade
                         WHERE aberto_por = " . $args['cd_usuario'] . "
                         ORDER BY dt_cadastro DESC
                         LIMIT 1";
            $result = $this->db->query($qr_sql);
            $ar_reg = $result->row_array();
            $retorno = $ar_reg['cd_nao_conformidade'];
        }

        #echo "<pre style='text-align: left;'>$qr_sql</pre>";exit;

        return $retorno;
    }

    function data_min_prazo_validacao($result, $args)
    {
        $qr_sql = "
            SELECT TO_CHAR(COALESCE(funcoes.dia_util('DEPOIS', CURRENT_DATE, 5)),'DD/MM/YYYY') AS quinto_dia_util";
        $result = $this->db->query($qr_sql);
        return $result->row_array();
    }

    function acaoCorretiva(&$result, $args=array())
    {
        $qr_sql = "
                SELECT ac.cd_nao_conformidade, 
                       ac.cd_acao, 
                       TO_CHAR(ac.dt_limite_apres, 'DD/MM/YYYY') AS dt_limite_apres,
                       CASE WHEN CURRENT_DATE <= CAST(ac.dt_limite_apres AS DATE) AND ac.raz_nao_imp IS NULL 
                            THEN 'N'
                            ELSE 'S'
                       END AS fl_limite_apres,
                       TO_CHAR(ac.dt_apres, 'DD/MM/YYYY HH24:MI:SS') AS dt_apres, 
                       TO_CHAR(ac.dt_prop_imp, 'DD/MM/YYYY') AS dt_prop_imp, 
                       TO_CHAR(ac.dt_efe_imp, 'DD/MM/YYYY') AS dt_efe_imp, 
                       TO_CHAR(ac.dt_prop_verif, 'DD/MM/YYYY') AS dt_prop_verif, 
                       TO_CHAR(ac.dt_efe_verif, 'DD/MM/YYYY') AS dt_efe_verif, 
                       ac.ac_proposta, 
                       ac.raz_nao_imp, 
                       CASE WHEN ac.raz_nao_imp IS NULL 
                            THEN 'S'
                            ELSE 'N'
                       END AS fl_prorroga,
                       TO_CHAR(ac.dt_prorrogada, 'DD/MM/YYYY') AS dt_prorrogada, 
                       TO_CHAR(ac.dt_prorrogada_em, 'DD/MM/YYYY HH24:MI:SS') AS dt_prorrogada_em, 
                       ac.cd_usuario_prorrogacao, 
                       TO_CHAR(ac.dt_raz_nao_imp, 'DD/MM/YYYY') AS dt_raz_nao_imp,
                       TO_CHAR(ac.dt_proposta_prorrogacao, 'DD/MM/YYYY') AS dt_proposta_prorrogacao,
					   TO_CHAR(ac.dt_prorrogacao_verificacao_eficacia, 'DD/MM/YYYY') AS dt_prorrogacao_verificacao_eficacia
                  FROM projetos.acao_corretiva ac
                 WHERE ac.cd_nao_conformidade = " . intval($args['cd_nao_conformidade']);
        #echo "<pre>$qr_sql</pre>";	
        $result = $this->db->query($qr_sql);
    }

    function acaoCorretivaSalvar(&$result, $args=array())
    {
        if (intval($args['cd_acao']) > 0)
        {
            ##UPDATE##
            $qr_sql = " 
                    UPDATE projetos.acao_corretiva
                       SET ac_proposta                         = " . (trim($args['ac_proposta']) == "" ? "DEFAULT" : str_escape($args['ac_proposta'])) . ",
                           dt_prop_imp                         = " . (trim($args['dt_prop_imp']) == "" ? "DEFAULT" : "TO_DATE('" . $args['dt_prop_imp'] . "','DD/MM/YYYY')") . ",
                           dt_efe_imp                          = " . (trim($args['dt_efe_imp']) == "" ? "DEFAULT" : "TO_DATE('" . $args['dt_efe_imp'] . "','DD/MM/YYYY')") . ",
                           dt_efe_verif                        = " . (trim($args['dt_efe_verif']) == "" ? "DEFAULT" : "TO_DATE('" . $args['dt_efe_verif'] . "','DD/MM/YYYY')") . ",
                           dt_prop_verif                       = " . (trim($args['dt_prop_verif']) == "" ? "DEFAULT" : "TO_DATE('" . $args['dt_prop_verif'] . "','DD/MM/YYYY')") . ",
                           dt_prorrogada_em                    = (CASE WHEN dt_prorrogada IS NULL AND '" . trim($args['dt_prorrogada']) . "' <> '' THEN CURRENT_TIMESTAMP ELSE dt_prorrogada_em END),
                           cd_usuario_prorrogacao              = (CASE WHEN dt_prorrogada IS NULL AND '" . trim($args['dt_prorrogada']) . "' <> '' THEN " . intval($args['cd_usuario']) . " ELSE cd_usuario_prorrogacao END),
                           dt_prorrogada                       = " . (trim($args['dt_prorrogada']) == "" ? "DEFAULT" : "TO_DATE('" . $args['dt_prorrogada'] . "','DD/MM/YYYY')") . ",
                           dt_raz_nao_imp                      = (CASE WHEN raz_nao_imp IS NULL AND '" . trim($args['raz_nao_imp']) . "' <> '' THEN CURRENT_TIMESTAMP ELSE dt_raz_nao_imp END),
                           raz_nao_imp                         = " . (trim($args['raz_nao_imp']) == "" ? "DEFAULT" : str_escape($args['raz_nao_imp'])) . ",
                           dt_prorrogacao_verificacao_eficacia = " . (trim($args['dt_prorrogacao_verificacao_eficacia']) == "" ? "DEFAULT" : "TO_DATE('" . $args['dt_prorrogacao_verificacao_eficacia'] . "','DD/MM/YYYY')") . ",
                           dt_proposta_prorrogacao             = " . (trim($args['dt_proposta_prorrogacao']) == "" ? "DEFAULT" : "TO_DATE('" . $args['dt_proposta_prorrogacao'] . "','DD/MM/YYYY')") . ",
                           cd_usuario_atualizacao              = " . intval($args['cd_usuario']) . "
                     WHERE cd_nao_conformidade = " . intval($args['cd_nao_conformidade']);
            $this->db->query($qr_sql);
        }
        else
        {
            ##INSERT##
            $qr_sql = " 
                        INSERT INTO projetos.acao_corretiva
                             (
                               cd_processo, 
                               cd_nao_conformidade, 
                               cd_acao, 
                               dt_limite_apres, 
                               dt_apres, 
                               dt_prop_imp,  
                               ac_proposta, 
							   dt_prorrogacao_verificacao_eficacia,
                               cd_usuario_atualizacao
                             )
                        VALUES 
                             (
                               (SELECT cd_processo 
                                  FROM projetos.nao_conformidade 
                                 WHERE cd_nao_conformidade = " . intval($args['cd_nao_conformidade']) . "),
                               " . intval($args['cd_nao_conformidade']) . ",
                               " . intval($args['cd_nao_conformidade']) . ",
                               " . (trim($args['dt_limite_apres']) == "" ? "DEFAULT" : "TO_DATE('" . $args['dt_limite_apres'] . "','DD/MM/YYYY')") . ",
                               CURRENT_TIMESTAMP,
                               " . (trim($args['dt_prop_imp']) == "" ? "DEFAULT" : "TO_DATE('" . $args['dt_prop_imp'] . "','DD/MM/YYYY')") . ",
                               " . (trim($args['ac_proposta']) == "" ? "DEFAULT" : str_escape($args['ac_proposta'])) . ",
							   " . (trim($args['dt_prorrogacao_verificacao_eficacia']) == "" ? "DEFAULT" : "TO_DATE('" . $args['dt_prorrogacao_verificacao_eficacia'] . "','DD/MM/YYYY')") . ",
                               " . intval($args['cd_usuario']) . "
                             );";
            $this->db->query($qr_sql);
        }

        #echo "<pre>$qr_sql</pre>";exit;

        return intval($args['cd_nao_conformidade']);
        ;
    }

    function acompanha(&$result, $args=array())
    {
        $qr_sql = "
                SELECT a.cd_acompanhamento, 
                       TO_CHAR(a.data,'DD/MM/YYYY HH24:MI:SS') AS dt_cadastro, 
                       a.situacao,                          
                       u.nome AS registrado                    
                  FROM projetos.acompanhamento a
                  JOIN projetos.usuarios_controledi u
                    ON u.codigo = a.auditor
                 WHERE a.cd_nao_conformidade = " . intval($args['cd_nao_conformidade']) . "
                 ORDER BY a.data DESC";
        #echo "<pre>$qr_sql</pre>";	
        $result = $this->db->query($qr_sql);
    }

    function acompanhaSalvar(&$result, $args=array())
    {
        $retorno = 0;

        if (intval($args['cd_acompanhamento']) > 0)
        {
            ##UPDATE
            $retorno = intval($args['cd_acompanhamento']);
        }
        else
        {
            ##INSERT
            $new_id = intval($this->db->get_new_id("projetos.acompanhamento", "cd_acompanhamento"));
            $qr_sql = " 
                        INSERT INTO projetos.acompanhamento 
                              ( 
                                cd_acompanhamento,
                                cd_nao_conformidade,
                                cd_processo,
                                data,
                                situacao,
                                auditor
                              )                                     
                         VALUES 
                              (                               
                                " . $new_id . ",
                                " . intval($args['cd_nao_conformidade']) . ",                    
                                (SELECT cd_processo FROM projetos.nao_conformidade WHERE cd_nao_conformidade = " . intval($args['cd_nao_conformidade']) . "),
                                CURRENT_TIMESTAMP,                        
                                " . str_escape($args['situacao']) . ",                      
                                " . intval($args['cd_usuario']) . "                           
                              );";
            $this->db->query($qr_sql);
            $retorno = $new_id;
        }

        return $retorno;
    }

    function comboDiretoria(&$result, $args=array())
    {
        $qr_sql = "
                SELECT DISTINCT(area) AS value, 
                       area AS text 
                  FROM projetos.divisoes 
                 WHERE area IS NOT NULL 
                    OR TRIM(area) <> ''
                 ORDER BY text";

        $result = $this->db->query($qr_sql);
    }

    function comboGerencia(&$result, $args=array())
    {
        $qr_sql = "
                SELECT DISTINCT(codigo) AS value, 
                       nome AS text 
                  FROM projetos.divisoes
                 ORDER BY text";
	
        $result = $this->db->query($qr_sql);
    }

    function comboProcesso(&$result, $args=array())
    {
        $qr_sql = "
                SELECT cd_processo as value, 
                       procedimento as text 
                  FROM projetos.processos
                 ORDER BY text";

        $result = $this->db->query($qr_sql);
    }

    function comboResponsavel(&$result, $args=array())
    {
        $qr_sql = "
                SELECT uc.codigo AS value,
                       uc.nome AS text
                  FROM projetos.usuarios_controledi uc
                 WHERE uc.divisao NOT IN ('FC','SNG','CF','CEE')
                   AND (uc.tipo NOT IN ('X') OR uc.codigo = " . intval($args['cd_usuario']) . " OR uc.codigo = " . intval($args['cd_substituto']) . ")
                 ORDER BY text";

        $result = $this->db->query($qr_sql);
    }
	
    function comboOrigemEvento(&$result, $args=array())
    {
        $qr_sql = "
					SELECT ncoe.cd_nao_conformidade_origem_evento AS value,
						   ncoe.ds_nao_conformidade_origem_evento AS text
					  FROM projetos.nao_conformidade_origem_evento ncoe
					 WHERE ncoe.dt_exclusao IS NULL
						OR ncoe.cd_nao_conformidade_origem_evento IN (SELECT DISTINCT nc.cd_nao_conformidade_origem_evento 
																		FROM projetos.nao_conformidade nc) 
					 ORDER BY text
				  ";

        $result = $this->db->query($qr_sql);
    }	

    public function listar_anexo($cd_nao_conformidade)
    {
        $qr_sql = "
            SELECT cd_nao_conformidade_anexo, 
                   cd_usuario_inclusao,
                   arquivo, 
                   arquivo_nome, 
                   TO_CHAR(dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
                   funcoes.get_usuario_nome(cd_usuario_inclusao) AS ds_usuario_inclusao
              FROM projetos.nao_conformidade_anexo 
             WHERE cd_nao_conformidade = ".intval($cd_nao_conformidade)."
               AND dt_exclusao        IS NULL;";

        return $this->db->query($qr_sql)->result_array();
    }    

    public function salvar_anexo($cd_nao_conformidade, $cd_processo, $args = array())
    {
        $qr_sql ="
            INSERT INTO projetos.nao_conformidade_anexo
                 (
                    cd_nao_conformidade,
                    cd_processo, 
                    arquivo, 
                    arquivo_nome, 
                    cd_usuario_inclusao
                 )
            VALUES 
                 (
                    ".intval($cd_nao_conformidade).",
                    ".intval($cd_processo). ",
                    '".trim($args['arquivo'])."',
                    '".trim($args['arquivo_nome'])."',
                    ".intval($args['cd_usuario'])."
                 );";

        $this->db->query($qr_sql);
    }

    public function excluir_anexo($cd_nao_conformidade_anexo, $cd_usuario)
    {
        $qr_sql = "
            UPDATE projetos.nao_conformidade_anexo
               SET cd_usuario_exclusao = ".intval($cd_usuario).",
                   dt_exclusao         = CURRENT_TIMESTAMP
             WHERE cd_nao_conformidade_anexo = ".intval($cd_nao_conformidade_anexo).";";

        $this->db->query($qr_sql);
    }

}
?>