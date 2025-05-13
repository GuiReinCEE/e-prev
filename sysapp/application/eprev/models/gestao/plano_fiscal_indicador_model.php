<?php

class Plano_fiscal_indicador_model extends Model
{
    function __construct()
    {
        parent::Model();
    }
    
    function listar(&$result, $args=array())
    {
        $qr_sql = "
            SELECT pfi.cd_plano_fiscal_indicador,
                   pfi.nr_ano || '/' || TO_CHAR(pfi.nr_mes, 'FM00')  AS nr_ano_mes,
                   TO_CHAR(pfi.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
                   uc.nome,
                   (SELECT COUNT(pfii.*)
                      FROM gestao.plano_fiscal_indicador_item pfii
                     WHERE pfii.dt_exclusao IS NULL
                       AND pfii.cd_plano_fiscal_indicador = pfi.cd_plano_fiscal_indicador) AS qt_itens,
                   (SELECT COUNT(pfiir.*) 
                      FROM gestao.plano_fiscal_indicador_item pfiir
                     WHERE pfiir.dt_exclusao IS NULL
                       AND pfiir.cd_plano_fiscal_indicador = pfi.cd_plano_fiscal_indicador
                       AND pfiir.dt_resposta IS NOT NULL) AS qt_respondidos,
				   (SELECT COUNT(pfiir.*) 
                      FROM gestao.plano_fiscal_indicador_item pfiir
                     WHERE pfiir.dt_exclusao IS NULL
                       AND pfiir.cd_plano_fiscal_indicador = pfi.cd_plano_fiscal_indicador
                       AND pfiir.dt_confirmacao IS NOT NULL) AS qt_assinados,
                   TO_CHAR(pfi.dt_encerra, 'DD/MM/YYYY HH24:MI:SS') AS dt_encerra,
                   uc2.nome AS usuario_encerrado
              FROM gestao.plano_fiscal_indicador pfi
              JOIN projetos.usuarios_controledi uc
                ON uc.codigo = pfi.cd_usuario_inclusao
              LEFT JOIN projetos.usuarios_controledi uc2
                ON uc2.codigo = pfi.cd_usuario_encerra
             WHERE pfi.dt_exclusao IS NULL
			 ".(trim($args['nr_ano']) != '' ? "AND pfi.nr_ano = ".intval($args['nr_ano']) : '')."
			 ".(trim($args['nr_mes']) != '' ? "AND pfi.nr_mes = ".intval($args['nr_mes']) : '')."
             ORDER BY nr_ano, nr_mes";

        $result = $this->db->query($qr_sql);
    }
    
    function listar_relatorio(&$result, $args=array())
    {
        $qr_sql = "
			SELECT pfp.nr_ano || '/' || TO_CHAR(pfp.nr_mes, 'FM00') AS nr_ano_mes,
                   pfii.cd_plano_fiscal_indicador_item,
                   pfii.cd_gerencia,
                   pfii.nr_item,
                   uc.nome,
                   pfii.descricao,
                   TO_CHAR(pfii.dt_envio, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio,
                   TO_CHAR(pfii.dt_resposta, 'DD/MM/YYYY HH24:MI:SS') AS dt_resposta,
				   TO_CHAR(pfii.dt_confirmacao, 'DD/MM/YYYY HH24:MI:SS') AS dt_confirmacao,
                   TO_CHAR(pfii.dt_limite, 'DD/MM/YYYY') AS dt_limite,
                   TO_CHAR(pfii.dt_encaminhamento, 'DD/MM/YYYY') AS dt_encaminhamento,
                   pfii.peso,
                   pfii.meta,
                   pfii.unidade,
                   pfii.resultado,
                   pfii.retorno,
                   pfii.fl_copiar_resultado,
                   pfii.fl_status,
				   COALESCE(pfis.ds_plano_fiscal_indicador_status,'NÃO INFORMADO') AS ds_status,
                   uc2.nome AS gerente,
				   pfii.cd_gerencia_gerente,
                   pfii.cd_gerente,
				   pfii.cd_usuario_confirmacao,
				   ua.nome AS usuario_confirmacao,
                   TO_CHAR(pfp.dt_encerra, 'DD/MM/YYYY HH24:MI:SS') AS dt_encerra,
				   a.ds_plano_fiscal_indicador_area
              FROM gestao.plano_fiscal_indicador_item pfii
			  JOIN gestao.plano_fiscal_indicador_area a
			    ON a.cd_plano_fiscal_indicador_area = pfii.cd_plano_fiscal_indicador_area
              JOIN gestao.plano_fiscal_indicador pfp
                ON pfp.cd_plano_fiscal_indicador = pfii.cd_plano_fiscal_indicador
			  LEFT JOIN gestao.plano_fiscal_indicador_status pfis
			    ON pfis.cd_plano_fiscal_indicador_status = pfii.fl_status	
			  JOIN projetos.usuarios_controledi uc
			    ON uc.codigo = pfii.cd_responsavel
			  JOIN projetos.usuarios_controledi uc2
			    ON uc2.codigo = pfii.cd_gerente
			  LEFT JOIN projetos.usuarios_controledi ua
			    ON ua.codigo = pfii.cd_usuario_confirmacao					   
			 WHERE pfii.dt_exclusao IS NULL
               ".(trim($args['nr_ano']) != '' ? "AND pfp.nr_ano = ".intval($args['nr_ano']) : '')."
			   ".(trim($args['nr_mes']) != '' ? "AND pfp.nr_mes = ".intval($args['nr_mes']) : '')."
               ".(trim($args['nr_item']) != '' ? "AND pfii.nr_item = ".intval($args['nr_item']) : '')."
			   ".(trim($args['fl_assinado']) == 'S' ? "AND pfii.dt_confirmacao IS NOT NULL" : '')."
			   ".(trim($args['fl_assinado']) == 'N' ? "AND pfii.dt_confirmacao IS NULL" : '')."
			   ".(trim($args['fl_status']) != '' ? "AND pfii.fl_status = '".trim($args['fl_status'])."'" : '')."		
               ".(trim($args['responsavel_gerencia']) != '' ? "AND pfii.cd_gerencia_gerente = '".trim($args['responsavel_gerencia'])."'" : '')."	
               ".(trim($args['responsavel']) != '' ? "AND pfii.cd_gerente = ".intval($args['responsavel']) : '')."
               ".(trim($args['usuario']) != '' ? "AND pfii.cd_responsavel = ".intval($args['usuario']) : '')."
               ".(trim($args['usuario_gerencia']) != '' ? "AND (SELECT divisao FROM projetos.usuarios_controledi WHERE codigo = pfii.cd_responsavel) = '".trim($args['usuario_gerencia'])."'" : '')."
               ".(((trim($args['dt_envio_ini']) != "") AND (trim($args['dt_envio_fim']) != "")) ? " AND DATE_TRUNC('day',pfii.dt_envio) BETWEEN TO_DATE('".$args['dt_envio_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_envio_fim']."', 'DD/MM/YYYY')" : "")."
               ".(((trim($args['dt_limite_ini']) != "") AND (trim($args['dt_limite_fim']) != "")) ? " AND DATE_TRUNC('day',pfii.dt_limite) BETWEEN TO_DATE('".$args['dt_limite_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_limite_fim']."', 'DD/MM/YYYY')" : "")."  
               ".(((trim($args['dt_resposta_ini']) != "") AND (trim($args['dt_resposta_fim']) != "")) ? " AND DATE_TRUNC('day',pfii.dt_resposta) BETWEEN TO_DATE('".$args['dt_resposta_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_resposta_fim']."', 'DD/MM/YYYY')" : "")."  
               ".(((trim($args['dt_assinatura_ini']) != "") AND (trim($args['dt_assinatura_fim']) != "")) ? " AND DATE_TRUNC('day',pfii.dt_confirmacao) BETWEEN TO_DATE('".$args['dt_assinatura_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_assinatura_fim']."', 'DD/MM/YYYY')" : "")."
               ".(((trim($args['dt_encaminhamento_ini']) != "") AND (trim($args['dt_encaminhamento_fim']) != "")) ? " AND DATE_TRUNC('day',pfii.dt_encaminhamento) BETWEEN TO_DATE('".$args['dt_encaminhamento_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_encaminhamento_fim']."', 'DD/MM/YYYY')" : "")."
			 ORDER BY pfii.nr_item;";
         #echo '<pre>'.$qr_sql;
        $result = $this->db->query($qr_sql);
    }
    
    function carrega(&$result, $args=array())
    {
        $qr_sql = " 
            SELECT pfi.cd_plano_fiscal_indicador,
                   pfi.nr_ano,
                   TO_CHAR(pfi.nr_mes, 'FM00') AS nr_mes,
                   pfi.cd_dir_financeiro,
                   pfi.cd_dir_administrativo,
                   pfi.cd_dir_seguridade,
                   pfi.cd_presidente,
                   funcoes.get_usuario_nome(pfi.cd_dir_financeiro) AS usuario_dir_financeiro,
                   funcoes.get_usuario_nome(pfi.cd_dir_administrativo) AS usuario_dir_administrativo,
                   funcoes.get_usuario_nome(pfi.cd_dir_seguridade) AS usuario_dir_seguridade,
                   funcoes.get_usuario_nome(pfi.cd_presidente) AS usuario_presidente,
                   funcoes.get_usuario_nome(pfi.cd_usuario_envio_diretoria) AS usuario_envio_diretoria,
				   pfi.cd_dir_financeiro_sub,
                   pfi.cd_dir_administrativo_sub,
                   pfi.cd_dir_seguridade_sub,
                   pfi.cd_presidente_sub,
				   TO_CHAR(pfi.dt_envio_diretoria, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio_diretoria,
				   TO_CHAR(pfi.dt_limite_diretoria, 'DD/MM/YYYY') AS dt_limite_diretoria,
				   (SELECT COUNT(*)
				      FROM gestao.plano_fiscal_indicador_diretoria pfpd
					 WHERE pfpd.cd_plano_fiscal_indicador = pfi.cd_plano_fiscal_indicador
                       AND pfpd.dt_exclusao IS NULL) AS tl_assinatura_diretoria,
				   (SELECT uc.assinatura
				      FROM gestao.plano_fiscal_indicador_diretoria pfid
					  JOIN projetos.usuarios_controledi uc
					    ON uc.codigo = pfid.cd_usuario_assinatura
					 WHERE pfid.cd_plano_fiscal_indicador = pfi.cd_plano_fiscal_indicador
					   AND pfid.cd_diretoria = 'FIN'
                       AND pfid.dt_exclusao IS NULL) AS assinatura_dir_financeiro, 
				   (SELECT uc.assinatura
				      FROM gestao.plano_fiscal_indicador_diretoria pfid
					  JOIN projetos.usuarios_controledi uc
					    ON uc.codigo = pfid.cd_usuario_assinatura
					 WHERE pfid.cd_plano_fiscal_indicador = pfi.cd_plano_fiscal_indicador
					   AND pfid.cd_diretoria IN ('ADM','INFR')
                       AND pfid.dt_exclusao IS NULL) AS assinatura_dir_administrativo, 					   
				   (SELECT uc.assinatura
				      FROM gestao.plano_fiscal_indicador_diretoria pfid
					  JOIN projetos.usuarios_controledi uc
					    ON uc.codigo = pfid.cd_usuario_assinatura
					 WHERE pfid.cd_plano_fiscal_indicador = pfi.cd_plano_fiscal_indicador
					   AND pfid.cd_diretoria IN ('SEG','PREV')
                       AND pfid.dt_exclusao IS NULL) AS assinatura_dir_seguridade,
				   (SELECT uc.assinatura
				      FROM gestao.plano_fiscal_indicador_diretoria pfid
					  JOIN projetos.usuarios_controledi uc
					    ON uc.codigo = pfid.cd_usuario_assinatura
					 WHERE pfid.cd_plano_fiscal_indicador = pfi.cd_plano_fiscal_indicador
					   AND pfid.cd_diretoria = 'PRE'
                       AND pfid.dt_exclusao IS NULL) AS assinatura_presidente,
					   
				   (SELECT pfid.cd_usuario_assinatura
				      FROM gestao.plano_fiscal_indicador_diretoria pfid
					 WHERE pfid.cd_plano_fiscal_indicador = pfi.cd_plano_fiscal_indicador
					   AND pfid.cd_diretoria = 'FIN'
                       AND pfid.dt_exclusao IS NULL) AS cd_assinatura_dir_financeiro, 
				   (SELECT pfid.cd_usuario_assinatura
				      FROM gestao.plano_fiscal_indicador_diretoria pfid
					 WHERE pfid.cd_plano_fiscal_indicador = pfi.cd_plano_fiscal_indicador
					   AND pfid.cd_diretoria IN ('ADM','INFR')
                       AND pfid.dt_exclusao IS NULL) AS cd_assinatura_dir_administrativo, 
				   (SELECT pfid.cd_usuario_assinatura
				      FROM gestao.plano_fiscal_indicador_diretoria pfid
					 WHERE pfid.cd_plano_fiscal_indicador = pfi.cd_plano_fiscal_indicador
					   AND pfid.cd_diretoria IN ('SEG','PREV')
                       AND pfid.dt_exclusao IS NULL) AS cd_assinatura_dir_seguridade,				   
				   (SELECT pfid.cd_usuario_assinatura
				      FROM gestao.plano_fiscal_indicador_diretoria pfid
					 WHERE pfid.cd_plano_fiscal_indicador = pfi.cd_plano_fiscal_indicador
					   AND pfid.cd_diretoria = 'PRE'
                       AND pfid.dt_exclusao IS NULL) AS cd_assinatura_presidente,					   
					   
				   (SELECT TO_CHAR(pfid.dt_inclusao,'DD/MM/YYYY HH24:MI:SS')
				      FROM gestao.plano_fiscal_indicador_diretoria pfid
					 WHERE pfid.cd_plano_fiscal_indicador = pfi.cd_plano_fiscal_indicador
					   AND pfid.cd_diretoria = 'FIN'
                       AND pfid.dt_exclusao IS NULL) AS dt_assinatura_dir_financeiro, 
				   (SELECT TO_CHAR(pfid.dt_inclusao,'DD/MM/YYYY HH24:MI:SS')
				      FROM gestao.plano_fiscal_indicador_diretoria pfid
					 WHERE pfid.cd_plano_fiscal_indicador = pfi.cd_plano_fiscal_indicador
					   AND pfid.cd_diretoria IN ('ADM','INFR')
                       AND pfid.dt_exclusao IS NULL) AS dt_assinatura_dir_administrativo, 
				   (SELECT TO_CHAR(pfid.dt_inclusao,'DD/MM/YYYY HH24:MI:SS')
				      FROM gestao.plano_fiscal_indicador_diretoria pfid
					 WHERE pfid.cd_plano_fiscal_indicador = pfi.cd_plano_fiscal_indicador
					   AND pfid.cd_diretoria IN ('SEG','PREV')
                       AND pfid.dt_exclusao IS NULL) AS dt_assinatura_dir_seguridade,				   
				   (SELECT TO_CHAR(pfid.dt_inclusao,'DD/MM/YYYY HH24:MI:SS')
				      FROM gestao.plano_fiscal_indicador_diretoria pfid
					 WHERE pfid.cd_plano_fiscal_indicador = pfi.cd_plano_fiscal_indicador
					   AND pfid.cd_diretoria = 'PRE'
                       AND pfid.dt_exclusao IS NULL) AS dt_assinatura_presidente,							   
				   
                   TO_CHAR(pfi.dt_encerra, 'DD/MM/YYYY HH24:MI:SS') AS dt_encerra,
                   funcoes.get_usuario_nome(pfi.cd_usuario_encerra) AS usuario_encerrado,
                   uc1.observacao AS obs_financeiro,
                   uc2.observacao AS obs_administrativo,
                   uc3.observacao AS obs_seguridade,
                   uc4.observacao AS obs_presidente,
                   pfi.ds_justificativa
              FROM gestao.plano_fiscal_indicador pfi
              LEFT JOIN projetos.usuarios_controledi uc1
                ON uc1.codigo = pfi.cd_dir_financeiro
              LEFT JOIN projetos.usuarios_controledi uc2
                ON uc2.codigo = pfi.cd_dir_administrativo
              LEFT JOIN projetos.usuarios_controledi uc3
                ON uc3.codigo = pfi.cd_dir_seguridade
              LEFT JOIN projetos.usuarios_controledi uc4
                ON uc4.codigo = pfi.cd_presidente
				
             WHERE pfi.dt_exclusao IS NULL
               AND pfi.cd_plano_fiscal_indicador = ".intval($args['cd_plano_fiscal_indicador']).";";

        $result = $this->db->query($qr_sql);
    }
    
    function unidade(&$result, $args=array())
    {
        $qr_sql = "
            SELECT cd_plano_fiscal_indicador_unidade AS value,
                   descricao AS text
              FROM gestao.plano_fiscal_indicador_unidade
             WHERE dt_exclusao IS NULL";

        $result = $this->db->query($qr_sql);
    }
    
    function salvar(&$result, $args=array())
    {
        if(intval($args['cd_plano_fiscal_indicador']) > 0)
        {
            $qr_sql = "
                UPDATE gestao.plano_fiscal_indicador
                   SET cd_dir_financeiro         = ".intval($args['cd_dir_financeiro']).",
                       cd_dir_administrativo     = ".intval($args['cd_dir_administrativo']).",
                       cd_dir_seguridade         = ".intval($args['cd_dir_seguridade']).",
                       cd_presidente             = ".intval($args['cd_presidente']).",
					   cd_dir_financeiro_sub     = ".(trim($args['cd_dir_financeiro_sub']) == "" ? "DEFAULT" : intval($args['cd_dir_financeiro_sub'])).",
                       cd_dir_administrativo_sub = ".(trim($args['cd_dir_administrativo_sub']) == "" ? "DEFAULT" : intval($args['cd_dir_administrativo_sub'])).",
                       cd_dir_seguridade_sub     = ".(trim($args['cd_dir_seguridade_sub']) == "" ? "DEFAULT" : intval($args['cd_dir_seguridade_sub'])).",
                       cd_presidente_sub         = ".(trim($args['cd_presidente_sub']) == "" ? "DEFAULT" : intval($args['cd_presidente_sub']))."
                 WHERE cd_plano_fiscal_indicador = ".intval($args['cd_plano_fiscal_indicador']);
            
            $this->db->query($qr_sql);
                
        }
        else
        {
            $qr_sql = "
                INSERT INTO gestao.plano_fiscal_indicador
                     (
                       nr_ano,
                       nr_mes,
                       cd_dir_financeiro,
                       cd_dir_administrativo,
                       cd_dir_seguridade,
                       cd_presidente,
					   cd_dir_financeiro_sub,
                       cd_dir_administrativo_sub,
                       cd_dir_seguridade_sub,
                       cd_presidente_sub,
                       cd_usuario_inclusao
                     )
                VALUES
                     (
                       ".intval($args['nr_ano']).",
                       ".intval($args['nr_mes']).",
                       ".intval($args['cd_dir_financeiro']).",
                       ".intval($args['cd_dir_administrativo']).",
                       ".intval($args['cd_dir_seguridade']).",
                       ".intval($args['cd_presidente']).",
					   ".(trim($args['cd_dir_financeiro_sub']) == "" ? "DEFAULT" : intval($args['cd_dir_financeiro_sub'])).",
					   ".(trim($args['cd_dir_administrativo_sub']) == "" ? "DEFAULT" : intval($args['cd_dir_administrativo_sub'])).",
					   ".(trim($args['cd_dir_seguridade_sub']) == "" ? "DEFAULT" : intval($args['cd_dir_seguridade_sub'])).",
					   ".(trim($args['cd_presidente_sub']) == "" ? "DEFAULT" : intval($args['cd_presidente_sub'])).",
                       ".(trim($args['cd_usuario']) == "" ? "DEFAULT" : intval($args['cd_usuario']))." 
                     )";

            $this->db->query($qr_sql);
            
            $qr_sql = "
                SELECT cd_plano_fiscal_indicador
                  FROM gestao.plano_fiscal_indicador
                 WHERE nr_ano              = ".intval($args['nr_ano'])."
                   AND nr_mes              = ".intval($args['nr_mes'])."
                   AND cd_usuario_inclusao = ".intval($args['cd_usuario'])."
                   AND dt_exclusao IS NULL
                 ORDER BY cd_plano_fiscal_indicador DESC 
                 LIMIT 1";
            
            $result = $this->db->query($qr_sql);
            
            $row = $result->row_array();
            
            $args['cd_plano_fiscal_indicador'] = $row['cd_plano_fiscal_indicador'];
        }
        
        return $args['cd_plano_fiscal_indicador'];
    }
    
    function excluir_plano(&$result, $args=array())
    {
        $qr_sql = "
                UPDATE gestao.plano_fiscal_indicador
                   SET dt_exclusao         = CURRENT_TIMESTAMP,
                       cd_usuario_exclusao = ".intval($args['cd_usuario'])."
                 WHERE cd_plano_fiscal_indicador = ".intval($args['cd_plano_fiscal_indicador']);
            
            $this->db->query($qr_sql);
    }
    
    function total_enviados(&$result, $args=array())
    {
        $qr_sql = "
            SELECT COUNT(*) AS tl
              FROM gestao.plano_fiscal_indicador_item
             WHERE dt_exclusao IS NULL
               AND dt_envio IS NULL
               AND cd_plano_fiscal_indicador = ". intval($args['cd_plano_fiscal_indicador']);

        $result = $this->db->query($qr_sql);
    }
    
    function salvar_item(&$result, $args=array())
    {
        if(intval($args['cd_plano_fiscal_indicador_item']) > 0)
        {

            $qr_sql = "
                UPDATE gestao.plano_fiscal_indicador_item
                   SET cd_gerencia                    = ".(trim($args['cd_gerencia']) == "" ? "DEFAULT" : "'".trim($args['cd_gerencia'])."'")." ,
                       cd_responsavel                 = ".(trim($args['cd_responsavel']) == "" ? "DEFAULT" : intval($args['cd_responsavel'])).",
                       nr_item                        = ".(trim($args['nr_item']) == "" ? "DEFAULT" : intval($args['nr_item'])).",
                       descricao                      = ".(trim($args['descricao']) == "" ? "DEFAULT" : "'".utf8_decode(trim($args['descricao']))."'").",
					   cd_plano_fiscal_indicador_area = ".(trim($args['cd_plano_fiscal_indicador_area']) == "" ? "DEFAULT" : intval($args['cd_plano_fiscal_indicador_area'])).",
                       peso                 		  = ".floatval($args['peso']).",
                       meta                 		  = '".$args['meta']."',
					   dt_limite            		  = ".(trim($args['dt_limite']) == "" ? "dt_limite" : "TO_DATE('".trim($args['dt_limite'])."','DD/MM/YYYY')").",
                       unidade              		  = ".(trim($args['unidade']) == "" ? "DEFAULT" : "'".utf8_decode(trim($args['unidade']))."'").",
                       cd_gerente           		  = ".(trim($args['cd_gerente']) == "" ? "DEFAULT" : intval($args['cd_gerente'])).",
                       cd_gerencia_gerente  		  = ".(trim($args['cd_gerencia_gerente']) == "" ? "DEFAULT" : "'".trim($args['cd_gerencia_gerente'])."'").",
                       resultado            		  = ".(trim($args['resultado']) == "" ? "DEFAULT" : "'".utf8_decode(trim($args['resultado']))."'").",
					   retorno             		 	  = ".(trim($args['retorno']) == "" ? "DEFAULT" : "'".utf8_decode(trim($args['retorno']))."'").",
					   fl_status   		    		  = ".(trim($args['fl_status']) == "" ? "DEFAULT" : "'".utf8_decode(trim($args['fl_status']))."'").",
					   fl_copiar_resultado  		  = ".(trim($args['fl_copiar_resultado']) == "" ? "DEFAULT" : "'".utf8_decode(trim($args['fl_copiar_resultado']))."'").",
                       fl_criterio                = ".(trim($args['fl_criterio']) == "" ? "DEFAULT" : "'".utf8_decode(trim($args['fl_criterio']))."'").",
                       cd_plano_fiscal_indicador_periodicidade                 = ".(trim($args['cd_plano_fiscal_indicador_periodicidade']) == "" ? "DEFAULT" : intval($args['cd_plano_fiscal_indicador_periodicidade'])).",
                       cd_usuario_alteracao 		  = ".(trim($args['cd_usuario']) == "" ? "DEFAULT" : intval($args['cd_usuario'])).",
                       dt_alteracao         		  = CURRENT_TIMESTAMP
                 WHERE cd_plano_fiscal_indicador_item = ".intval($args['cd_plano_fiscal_indicador_item']);
        }
        else
        {
            $qr_sql = "
                INSERT INTO gestao.plano_fiscal_indicador_item
                     (
                       cd_plano_fiscal_indicador,
                       cd_gerencia,
                       cd_responsavel,
                       nr_item,
                       descricao,
					   cd_plano_fiscal_indicador_area,
                       cd_usuario_inclusao,
                       peso,
                       meta,
                       unidade,
                       cd_gerente,
                       cd_gerencia_gerente,
					   fl_copiar_resultado,
             fl_criterio,
             cd_plano_fiscal_indicador_periodicidade
                     )
                VALUES
                     (
                       ".intval($args['cd_plano_fiscal_indicador']).",
                       ".(trim($args['cd_gerencia']) == "" ? "DEFAULT" : "'".trim($args['cd_gerencia'])."'")." ,
                       ".(trim($args['cd_responsavel']) == "" ? "DEFAULT" : intval($args['cd_responsavel'])).",
                       ".(trim($args['nr_item']) == "" ? "DEFAULT" : intval($args['nr_item'])).",
                       ".(trim($args['descricao']) == "" ? "DEFAULT" : "'".utf8_decode(trim($args['descricao']))."'")." ,
					   ".(trim($args['cd_plano_fiscal_indicador_area']) == "" ? "DEFAULT" : intval($args['cd_plano_fiscal_indicador_area'])).",
                       ".(trim($args['cd_usuario']) == "" ? "DEFAULT" : intval($args['cd_usuario'])).",
                       ".floatval($args['peso']).",
                       '".$args['meta']."',
                       ".(trim($args['unidade']) == "" ? "DEFAULT" : "'".utf8_decode(trim($args['unidade']))."'").",
                       ".(trim($args['cd_gerente']) == "" ? "DEFAULT" : intval($args['cd_gerente'])).",
                       ".(trim($args['cd_gerencia_gerente']) == "" ? "DEFAULT" : "'".trim($args['cd_gerencia_gerente'])."'").", 
                       ".(trim($args['fl_copiar_resultado']) == "" ? "DEFAULT" : "'".trim($args['fl_copiar_resultado'])."'").",
                       ".(trim($args['fl_criterio']) == "" ? "DEFAULT" : "'".utf8_decode(trim($args['fl_criterio']))."'").",
                       ".(trim($args['cd_plano_fiscal_indicador_periodicidade']) == "" ? "DEFAULT" : intval($args['cd_plano_fiscal_indicador_periodicidade']))."
                     )";
        }

        $result = $this->db->query($qr_sql);
    }
    
    function listar_itens(&$result, $args=array())
    {
        $qr_sql = "
            SELECT pfii.cd_plano_fiscal_indicador_item,
                   pfii.cd_gerencia,
                   pfii.nr_item,
                   uc.nome,
                   pfii.descricao,
                   TO_CHAR(pfii.dt_envio, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio,
                   TO_CHAR(pfii.dt_resposta, 'DD/MM/YYYY HH24:MI:SS') AS dt_resposta,
				   TO_CHAR(pfii.dt_confirmacao, 'DD/MM/YYYY HH24:MI:SS') AS dt_confirmacao,
                   TO_CHAR(pfii.dt_limite, 'DD/MM/YYYY') AS dt_limite,
                   pfii.peso,
                   pfii.meta,
                   pfii.unidade,
                   pfii.resultado,
                   pfii.retorno,
                   pfii.fl_copiar_resultado,
                   pfii.fl_status,
				   COALESCE(pfis.ds_plano_fiscal_indicador_status,'NÃO INFORMADO') AS ds_status,
                   uc2.nome AS gerente,
				   pfii.cd_gerencia_gerente,
                   pfii.cd_gerente,
                   pfiu.qt_decimal,
				   pfii.cd_usuario_confirmacao,
				   ua.nome AS usuario_confirmacao,
                   TO_CHAR(pfp.dt_encerra, 'DD/MM/YYYY HH24:MI:SS') AS dt_encerra,
				   a.ds_plano_fiscal_indicador_area,
                  CASE WHEN pfii.fl_criterio = 'L' THEN 'Qualitativo'
                       WHEN pfii.fl_criterio = 'T' THEN 'Quantitativo'
                       ELSE ''
                   END AS criterio,
                   pfip.ds_plano_fiscal_indicador_periodicidade
              FROM gestao.plano_fiscal_indicador_item pfii
			  JOIN gestao.plano_fiscal_indicador_area a
			    ON a.cd_plano_fiscal_indicador_area = pfii.cd_plano_fiscal_indicador_area
              JOIN gestao.plano_fiscal_indicador pfp
                ON pfp.cd_plano_fiscal_indicador = pfii.cd_plano_fiscal_indicador
			  LEFT JOIN gestao.plano_fiscal_indicador_status pfis
			    ON pfis.cd_plano_fiscal_indicador_status = pfii.fl_status			  
              JOIN projetos.usuarios_controledi uc
                ON uc.codigo = pfii.cd_responsavel 
              JOIN projetos.usuarios_controledi uc2
                ON uc2.codigo = pfii.cd_gerente   
              LEFT JOIN projetos.usuarios_controledi ua
                ON ua.codigo = pfii.cd_usuario_confirmacao   				
              LEFT JOIN gestao.plano_fiscal_indicador_unidade pfiu 
                ON pfiu.cd_plano_fiscal_indicador_unidade = pfii.unidade
              LEFT JOIN gestao.plano_fiscal_indicador_periodicidade pfip 
                ON pfip.cd_plano_fiscal_indicador_periodicidade = pfii.cd_plano_fiscal_indicador_periodicidade
             WHERE pfii.dt_exclusao IS NULL
               AND pfii.cd_plano_fiscal_indicador = ".intval($args['cd_plano_fiscal_indicador'])."
			   ".(trim($args['fl_respondido']) == 'S' ? "AND pfii.dt_resposta IS NOT NULL" : '')."
               ".(trim($args['fl_respondido']) == 'N' ? "AND pfii.dt_resposta IS NULL" : '')."
			   ".(trim($args['fl_assinado']) == 'S' ? "AND pfii.dt_confirmacao IS NOT NULL" : '')."
               ".(trim($args['fl_assinado']) == 'N' ? "AND pfii.dt_confirmacao IS NULL" : '')."
			   ".(trim($args['cd_plano_fiscal_indicador_area']) != '' ? "AND pfii.cd_plano_fiscal_indicador_area = ".intval($args['cd_plano_fiscal_indicador_area']) : '')."
			   ".(trim($args['fl_status_filtro']) != '' ? "AND pfii.fl_status = '".trim($args['fl_status_filtro'])."'" : '')."
             ORDER BY pfii.nr_item";
   
        $result = $this->db->query($qr_sql);
    }
    
    function carrega_item(&$result, $args=array())
    {
        $qr_sql = "
            SELECT pfii.cd_plano_fiscal_indicador_item,
                   pfii.nr_item,
                   pfii.descricao,
                   pfii.cd_gerencia,
                   pfii.cd_responsavel,
                   pfii.peso,
                   pfii.meta,
                   pfii.unidade,
                   pfii.cd_gerente,
                   pfii.cd_gerencia_gerente,
                   pfii.resultado,
                   TO_CHAR(pfii.dt_limite,'DD/MM/YYYY') AS dt_limite,
                   pfii.dt_resposta,
                   pfii.dt_confirmacao,
				   pfii.retorno,
				   pfii.fl_copiar_resultado,
				   pfii.fl_status,
				   COALESCE(pfis.ds_plano_fiscal_indicador_status,'NÃO INFORMADO') AS ds_status,
				   pfii.cd_plano_fiscal_indicador_area,
                   pfii.fl_criterio,
                   pfii.cd_plano_fiscal_indicador_periodicidade
              FROM gestao.plano_fiscal_indicador_item pfii
			  LEFT JOIN gestao.plano_fiscal_indicador_status pfis
			    ON pfis.cd_plano_fiscal_indicador_status = pfii.fl_status				  
             WHERE pfii.dt_exclusao IS NULL
               AND pfii.cd_plano_fiscal_indicador_item = ". intval($args['cd_plano_fiscal_indicador_item']);

        $result = $this->db->query($qr_sql);
    }
    
    function enviar(&$result, $args=array())
    {
		$qr_sql = "SELECT gestao.plano_fiscal_indicador_enviar(".intval($args['cd_plano_fiscal_indicador']).", ".intval($args['cd_usuario']).", ".intval($args['cd_plano_fiscal_indicador_item']).");";

        $this->db->query($qr_sql);
    }
    
    function excluir_plano_item(&$result, $args=array())
    {
        $qr_sql = "
                UPDATE gestao.plano_fiscal_indicador_item
                   SET dt_exclusao         = CURRENT_TIMESTAMP ,
                       cd_usuario_exclusao = ".(trim($args['cd_usuario']) == "" ? "DEFAULT" : intval($args['cd_usuario']))." ,
                       cd_usuario_alteracao = ".(trim($args['cd_usuario']) == "" ? "DEFAULT" : intval($args['cd_usuario'])).",
                       dt_alteracao         = CURRENT_TIMESTAMP
                 WHERE cd_plano_fiscal_indicador_item = ".intval($args['cd_plano_fiscal_indicador_item'])."";
            
        $this->db->query($qr_sql);
    }
    
    function carrega_indicador_item_resposta(&$result, $args=array())
    {
        $qr_sql = "
            SELECT pfii.cd_plano_fiscal_indicador_item,
                   pfii.nr_item,
                   pfii.descricao,
                   pfii.cd_gerencia,
                   d.nome AS gerencia,
                   uc.nome AS responsavel,
				   pfii.fl_copiar_resultado,
                   pfii.fl_status,
				   COALESCE(pfis.ds_plano_fiscal_indicador_status,'NÃO INFORMADO') AS ds_status,
                   pfii.resultado,
                   pfii.dt_encaminhamento,
                   pfii.cd_responsavel,
                   pfii.meta,
                   pfii.unidade,
                   pfii.peso,
				   pfii.cd_gerencia_gerente,
                   pfii.cd_gerente,
                   pfii.dt_resposta,
                   TO_CHAR(pfii.dt_limite, 'DD/MM/YYYY') AS dt_limite,
                   CASE WHEN pfii.dt_limite::date < CURRENT_DATE THEN 'S' 
                        ELSE 'N' 
                   END AS fl_dt_limite,
                   pfi.nr_ano || '/' || TO_CHAR(pfi.nr_mes, 'FM00') AS nr_ano_mes,
                   TO_CHAR(pfii.dt_confirmacao, 'DD/MM/YYYY HH24:MI:SS') AS dt_confirmacao,
                   pfiu.qt_decimal,
				   pfii.retorno,
                   TO_CHAR(pfi.dt_encerra, 'DD/MM/YYYY HH24:MI:SS') AS dt_encerra,
				   a.ds_plano_fiscal_indicador_area
 			  FROM gestao.plano_fiscal_indicador_item pfii
			  JOIN gestao.plano_fiscal_indicador_area a
			    ON a.cd_plano_fiscal_indicador_area = pfii.cd_plano_fiscal_indicador_area
			  LEFT JOIN gestao.plano_fiscal_indicador_status pfis
			    ON pfis.cd_plano_fiscal_indicador_status = pfii.fl_status			  
              JOIN gestao.plano_fiscal_indicador pfi
                ON pfi.cd_plano_fiscal_indicador = pfii.cd_plano_fiscal_indicador
              JOIN projetos.usuarios_controledi uc
                ON uc.codigo = pfii.cd_responsavel
              JOIN projetos.divisoes d
                ON d.codigo = cd_gerencia
              LEFT JOIN gestao.plano_fiscal_indicador_unidade pfiu 
                ON pfiu.cd_plano_fiscal_indicador_unidade = pfii.unidade
             WHERE pfi.dt_exclusao IS NULL
               AND pfii.dt_exclusao IS NULL
               AND pfii.cd_plano_fiscal_indicador_item = ".intval($args['cd_plano_fiscal_indicador_item']).";";

        $result = $this->db->query($qr_sql);
    }
    
    function salvar_resposta(&$result, $args=array())
    {
        $qr_sql = "
            UPDATE gestao.plano_fiscal_indicador_item
               SET resultado            = ".(trim($args['resultado']) == "" ? "DEFAULT" : "'".trim($args['resultado'])."'")." ,
                   fl_status            = ".(trim($args['fl_status']) == "" ? "DEFAULT" : "'".trim($args['fl_status'])."'").",
                   cd_usuario_resposta  = ".(trim($args['cd_usuario']) == "" ? "DEFAULT" : intval($args['cd_usuario'])).",
                   dt_resposta          = CURRENT_TIMESTAMP,
                   cd_usuario_alteracao = ".(trim($args['cd_usuario']) == "" ? "DEFAULT" : intval($args['cd_usuario'])).",
                   dt_alteracao         = CURRENT_TIMESTAMP
             WHERE cd_plano_fiscal_indicador_item = ".intval($args['cd_plano_fiscal_indicador_item']);

        
        $this->db->query($qr_sql);
    }
    
    function confirmar(&$result, $args=array())
    {
        $qr_sql = "
    			UPDATE gestao.plano_fiscal_indicador_item
    			   SET resultado              = ".(trim($args['resultado']) == "" ? "DEFAULT" : "'".trim($args['resultado'])."'")." ,
                 fl_status              = ".(trim($args['fl_status']) == "" ? "DEFAULT" : "'".trim($args['fl_status'])."'").",
                 dt_confirmacao         = CURRENT_TIMESTAMP,
    				     cd_usuario_confirmacao = ".(trim($args['cd_usuario']) == "" ? "DEFAULT" : intval($args['cd_usuario'])).",
    				     cd_usuario_alteracao   = ".(trim($args['cd_usuario']) == "" ? "DEFAULT" : intval($args['cd_usuario'])).",
    				     dt_alteracao           = CURRENT_TIMESTAMP
    			 WHERE cd_plano_fiscal_indicador_item = ".intval($args['cd_plano_fiscal_indicador_item'])."";
        
        $this->db->query($qr_sql);
    }
	
	function reabrir(&$result, $args=array())
    {
        $qr_sql = "
			UPDATE gestao.plano_fiscal_indicador_item
			   SET dt_confirmacao         = NULL,
				   cd_usuario_confirmacao = NULL,
				   cd_usuario_alteracao   = ".(trim($args['cd_usuario']) == "" ? "DEFAULT" : intval($args['cd_usuario'])).",
				   dt_alteracao           = CURRENT_TIMESTAMP
			 WHERE cd_plano_fiscal_indicador_item = ".intval($args['cd_plano_fiscal_indicador_item']).";

            UPDATE gestao.plano_fiscal_indicador_diretoria
               SET cd_usuario_exclusao = ".(trim($args['cd_usuario']) == "" ? "DEFAULT" : intval($args['cd_usuario'])).",
                   dt_alteracao        = CURRENT_TIMESTAMP
             WHERE cd_plano_fiscal_indicador = ".intval($args['cd_plano_fiscal_indicador']).";";

        $this->db->query($qr_sql);
    }
    
    function encaminhar(&$result, $args=array())
    {
        $qr_sql = "
                UPDATE gestao.plano_fiscal_indicador_item
                   SET dt_encaminhamento    = CURRENT_TIMESTAMP,
                       cd_usuario_alteracao = ".(trim($args['cd_usuario']) == "" ? "DEFAULT" : intval($args['cd_usuario'])).",
                       dt_alteracao         = CURRENT_TIMESTAMP
                 WHERE cd_plano_fiscal_indicador_item = ".intval($args['cd_plano_fiscal_indicador_item'])."";
        
        $this->db->query($qr_sql);
    }
    
    function get_assinatura(&$result, $args=array())
    {
        $qr_sql = "SELECT assinatura 
                     FROM projetos.usuarios_controledi
                    WHERE codigo = ". intval($args['cd_usuario']);

        $result = $this->db->query($qr_sql);
    }
    
    function get_usuarios_de(&$result, $args=array())
    {
        $qr_sql = "
					SELECT uc.codigo AS value,
						   uc.nome AS text
					  FROM projetos.usuarios_controledi uc
					 WHERE (uc.divisao = 'DE' AND uc.tipo = 'D') 
					    OR (0 < (SELECT COUNT(*) 
							       FROM gestao.plano_fiscal_indicador pfi
								  WHERE pfi.cd_plano_fiscal_indicador = ".intval($args["cd_plano_fiscal_indicador"])."
								    AND uc.codigo IN (pfi.cd_dir_financeiro,pfi.cd_dir_administrativo,pfi.cd_dir_seguridade,pfi.cd_presidente)))
					 ORDER BY text
			      ";
        
        $result = $this->db->query($qr_sql);
    }
	
	function carrega_minhas(&$result, $args=array())
    {
		$qr_sql = "
            SELECT pfii.cd_plano_fiscal_indicador_item,
				   pfi.nr_ano || '/' || TO_CHAR(pfi.nr_mes, 'FM00')  AS nr_ano_mes,
                   pfii.cd_gerencia,
                   pfii.nr_item,
                   uc.nome,
                   pfii.descricao,
                   TO_CHAR(pfii.dt_envio, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio,
                   TO_CHAR(pfii.dt_resposta, 'DD/MM/YYYY HH24:MI:SS') AS dt_resposta,
                   TO_CHAR(pfii.dt_limite, 'DD/MM/YYYY') AS dt_limite,
				   TO_CHAR(pfii.dt_confirmacao, 'DD/MM/YYYY HH24:MI:SS') AS dt_confirmacao,
                   pfii.peso,
                   pfii.meta,
                   pfii.unidade,
                   pfii.resultado,
				   pfii.fl_copiar_resultado,
                   pfii.fl_status,
				   COALESCE(pfis.ds_plano_fiscal_indicador_status,'NÃO INFORMADO') AS ds_status,
                   uc2.nome AS gerente,
				   pfii.cd_gerencia_gerente,
                   pfii.cd_gerente,
                   pfiu.qt_decimal,
				   pfii.resultado,
				   pfii.retorno,
				   pfii.cd_usuario_confirmacao,
				   ua.nome AS usuario_confirmacao,				   
				   CASE WHEN pfii.dt_resposta IS NULL THEN 'NR'
				        WHEN pfii.dt_resposta IS NOT NULL AND pfii.dt_confirmacao IS NULL THEN 'RE'
				        WHEN pfii.dt_confirmacao IS NOT NULL THEN 'AS'
						ELSE ''
				   END AS fl_situacao,
				   a.ds_plano_fiscal_indicador_area		   
              FROM gestao.plano_fiscal_indicador_item pfii
			  JOIN gestao.plano_fiscal_indicador_area a
			    ON a.cd_plano_fiscal_indicador_area = pfii.cd_plano_fiscal_indicador_area
			  LEFT JOIN gestao.plano_fiscal_indicador_status pfis
			    ON pfis.cd_plano_fiscal_indicador_status = pfii.fl_status			  
			  JOIN gestao.plano_fiscal_indicador pfi
			    ON pfi.cd_plano_fiscal_indicador = pfii.cd_plano_fiscal_indicador
              LEFT JOIN projetos.usuarios_controledi uc
                ON uc.codigo = pfii.cd_responsavel 
              LEFT JOIN projetos.usuarios_controledi uc2
                ON uc2.codigo = pfii.cd_gerente   
              LEFT JOIN gestao.plano_fiscal_indicador_unidade pfiu 
                ON pfiu.cd_plano_fiscal_indicador_unidade = pfii.unidade
              LEFT JOIN projetos.usuarios_controledi ua
                ON ua.codigo = pfii.cd_usuario_confirmacao 				
             WHERE pfi.dt_exclusao IS NULL
               AND pfii.dt_exclusao IS NULL
			   AND pfii.dt_envio IS NOT NULL
			   AND (
					--RESPONDENTE OU RESPONSAVEL
					(pfii.cd_responsavel =".intval($args['cd_usuario'])." OR pfii.cd_gerente = ".intval($args['cd_usuario']).")
					OR
					--DIRETOR 
					(pfii.cd_gerencia IN (SELECT d1.codigo 
                                            FROM projetos.usuarios_controledi uc1
                                            JOIN projetos.divisoes d1
                                              ON d1.area = uc1.diretoria
                                           WHERE uc1.divisao = 'DE'
                                             AND uc1.codigo = ".intval($args['cd_usuario'])."))
					OR
					--DIRETOR 
					(pfii.cd_gerencia_gerente IN (SELECT d1.codigo 
                                                    FROM projetos.usuarios_controledi uc1
                                                    JOIN projetos.divisoes d1
                                                      ON d1.area = uc1.diretoria
                                                   WHERE uc1.divisao = 'DE'
                                                     AND uc1.codigo = ".intval($args['cd_usuario'])."))		
					OR
					--GERENTE 
					(0 < (SELECT COUNT(*)
                            FROM projetos.usuarios_controledi ug1
                           WHERE ug1.divisao = pfii.cd_gerencia_gerente
						     AND ug1.tipo   = 'G'
                             AND ug1.codigo = ".intval($args['cd_usuario'])."))
					OR
					--GERENTE SUBSTITUTO 
					(0 < (SELECT COUNT(*)
                            FROM projetos.usuarios_controledi ug2
                           WHERE ug2.divisao = pfii.cd_gerencia_gerente
						     AND ug2.indic_01 = 'S'
                             AND ug2.codigo = ".intval($args['cd_usuario'])."))							 
				   )			   
			   
			   ".(trim($args['fl_respondido']) == 'S' ? "AND pfii.dt_resposta IS NOT NULL" : '')."
               ".(trim($args['fl_respondido']) == 'N' ? "AND pfii.dt_resposta IS NULL" : '')."
			   ".(trim($args['fl_assinado']) == 'S' ? "AND pfii.dt_confirmacao IS NOT NULL" : '')."
               ".(trim($args['fl_assinado']) == 'N' ? "AND pfii.dt_confirmacao IS NULL" : '')."
			   ".(trim($args['nr_ano_mes']) != '' ? "AND TO_CHAR(pfi.nr_ano,'FM0000') || '/' || TO_CHAR(pfi.nr_mes,'FM00') = '".trim($args['nr_ano_mes'])."'" : '')."
			   ".(trim($args['fl_status']) != '' ? "AND pfii.fl_status = '".trim($args['fl_status'])."'" : '')."
			   ".(((trim($args["dt_ini_envio"]) != "") and (trim($args["dt_fim_envio"]) != "")) ? " AND CAST(pfii.dt_envio AS DATE) BETWEEN TO_DATE('".$args["dt_ini_envio"]."','DD/MM/YYYY') AND TO_DATE('".$args["dt_fim_envio"]."','DD/MM/YYYY')" : "")."  
               ".(((trim($args["dt_ini_resp"]) != "") and (trim($args["dt_fim_resp"]) != "")) ? " AND CAST(pfii.dt_resposta AS DATE) BETWEEN TO_DATE('".$args["dt_ini_resp"]."','DD/MM/YYYY') AND TO_DATE('".$args["dt_fim_resp"]."','DD/MM/YYYY')" : "")."
			 ORDER BY nr_ano_mes DESC
                ";
		$result = $this->db->query($qr_sql);
	}
	
	function comboStatus( &$result, $args=array() )
	{
		$qr_sql = "
            SELECT cd_plano_fiscal_indicador_status AS value,
                   ds_plano_fiscal_indicador_status AS text
              FROM gestao.plano_fiscal_indicador_status
              WHERE dt_exclusao IS NULL
             ORDER BY text DESC;";

		$result = $this->db->query($qr_sql);
	}	
	
	function area( &$result, $args=array() )
	{
		$qr_sql = "
            SELECT cd_plano_fiscal_indicador_area AS value,
                   ds_plano_fiscal_indicador_area AS text
              FROM gestao.plano_fiscal_indicador_area
             WHERE dt_exclusao IS NULL
             ORDER BY ds_plano_fiscal_indicador_area ASC;";

		$result = $this->db->query($qr_sql);
	}

	function comboAnoMes( &$result, $args=array() )
	{
		$qr_sql = "
            SELECT TO_CHAR(nr_ano,'FM0000') || '/' || TO_CHAR(nr_mes,'FM00') AS value,
                   TO_CHAR(nr_ano,'FM0000') || '/' || TO_CHAR(nr_mes,'FM00') AS text
              FROM gestao.plano_fiscal_indicador
             WHERE dt_exclusao IS NULL
             ORDER BY text DESC;";

		$result = $this->db->query($qr_sql);
	}	
    
  public  function encerrar(&$result, $args=array())
  {
      $qr_sql = "
          UPDATE gestao.plano_fiscal_indicador
             SET ds_justificativa   = ".(trim($args['ds_justificativa']) != '' ? str_escape($args['ds_justificativa']) : "DEFAULT").",
                 dt_encerra         = CURRENT_TIMESTAMP,
                 cd_usuario_encerra = ".intval($args['cd_usuario'])."
           WHERE cd_plano_fiscal_indicador = ".intval($args['cd_plano_fiscal_indicador']);
            
        $this->db->query($qr_sql);
  }
	
	function lista_assinatura_diretoria_parecer(&$result, $args=array())
	{
		$qr_sql = "
			SELECT d.cd_diretoria,
			       TO_CHAR(d.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   uc.nome
			  FROM gestao.plano_fiscal_indicador_diretoria d
			  JOIN projetos.usuarios_controledi uc
			    ON uc.codigo = d.cd_usuario_assinatura
			 WHERE d.cd_plano_fiscal_indicador = ".intval($args['cd_plano_fiscal_indicador'])."
               AND d.dt_exclusao IS NULL;";
			
		$result = $this->db->query($qr_sql);
	}
	
	function salvar_limite_diretoria(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE gestao.plano_fiscal_indicador
			   SET dt_envio_diretoria         = CURRENT_TIMESTAMP, 
			       cd_usuario_envio_diretoria = ".intval($args['cd_usuario']).", 
				   dt_limite_diretoria        = ".(trim($args['dt_limite_diretoria']) != '' ? "TO_DATE('".trim($args['dt_limite_diretoria'])."', 'DD/MM/YYYY')" : "DEFAULT")."
			 WHERE cd_plano_fiscal_indicador = ".intval($args['cd_plano_fiscal_indicador']).";";
			 
		$this->db->query($qr_sql);
	}
	
	function listar_diretoria_assinar(&$result, $args=array())
	{
		$qr_sql = "
			SELECT pfi.cd_plano_fiscal_indicador, 
				   pfi.nr_ano || '/' || TO_CHAR(pfi.nr_mes, 'FM00')  AS nr_ano_mes,
				   CASE WHEN ".intval($args['cd_usuario'])." = pfi.cd_dir_seguridade THEN 'PREV'
						WHEN ".intval($args['cd_usuario'])." = pfi.cd_dir_administrativo THEN 'INFR'
						WHEN ".intval($args['cd_usuario'])." = pfi.cd_presidente THEN 'PRE'
						WHEN ".intval($args['cd_usuario'])." = pfi.cd_dir_financeiro THEN 'FIN'
				   END AS cd_diretoria,
				   funcoes.get_usuario_nome(d.cd_usuario_assinatura) AS nome,
				   TO_CHAR(d.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao
			  FROM gestao.plano_fiscal_indicador pfi
			  LEFT JOIN gestao.plano_fiscal_indicador_diretoria d
			    ON d.cd_plano_fiscal_indicador = pfi.cd_plano_fiscal_indicador
               AND d.dt_exclusao IS NULL
			   AND 0 < (CASE WHEN ".intval($args['cd_usuario'])." = pfi.cd_dir_seguridade AND d.cd_diretoria IN (SELECT * FROM (VALUES('SEG'),('PREV')) x) THEN 1
							 WHEN ".intval($args['cd_usuario'])." = pfi.cd_dir_administrativo AND d.cd_diretoria IN (SELECT * FROM (VALUES('ADM'),('INFR')) x) THEN 2
							 WHEN ".intval($args['cd_usuario'])." = pfi.cd_presidente AND d.cd_diretoria IN (SELECT * FROM (VALUES('PRE')) x) THEN 3
							 WHEN ".intval($args['cd_usuario'])." = pfi.cd_dir_financeiro AND  d.cd_diretoria IN (SELECT * FROM (VALUES('FIN')) x) THEN 4
							 ELSE 0
						 END)									 
			 WHERE pfi.dt_exclusao IS NULL
			   AND pfi.dt_encerra IS NULL
			   AND pfi.dt_envio_diretoria IS NOT NULL
			   AND ".intval($args['cd_usuario'])." IN (pfi.cd_dir_seguridade, pfi.cd_presidente, pfi.cd_dir_financeiro, pfi.cd_dir_administrativo)
			   ".(trim($args['nr_ano']) != '' ? "AND pfi.nr_ano = ".intval($args['nr_ano']) : '')."
			   ".(trim($args['nr_mes']) != '' ? "AND pfi.nr_mes = ".intval($args['nr_mes']) : '')."
			   ".(trim($args['fl_assinado']) == 'S' ? "AND d.dt_inclusao IS NOT NULL" : '')."
			   ".(trim($args['fl_assinado']) == 'N' ? "AND d.dt_inclusao IS NULL" : '')."
			UNION 

			SELECT pfi.cd_plano_fiscal_indicador, 
				   pfi.nr_ano || '/' || TO_CHAR(pfi.nr_mes, 'FM00')  AS nr_ano_mes,
				   CASE WHEN ".intval($args['cd_usuario'])." = pfi.cd_dir_seguridade_sub THEN 'PREV'
						WHEN ".intval($args['cd_usuario'])." = pfi.cd_dir_administrativo_sub THEN 'INFR'
						WHEN ".intval($args['cd_usuario'])." = pfi.cd_presidente_sub THEN 'PRE'
						WHEN ".intval($args['cd_usuario'])." = pfi.cd_dir_financeiro_sub THEN 'FIN'
				   END AS cd_diretoria,
				   funcoes.get_usuario_nome(d.cd_usuario_assinatura) AS nome,
				   TO_CHAR(d.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao
			  FROM gestao.plano_fiscal_indicador pfi
			  LEFT JOIN gestao.plano_fiscal_indicador_diretoria d
			    ON d.cd_plano_fiscal_indicador = pfi.cd_plano_fiscal_indicador
               AND d.dt_exclusao IS NULL
			   AND 0 < (CASE WHEN ".intval($args['cd_usuario'])." = pfi.cd_dir_seguridade_sub AND d.cd_diretoria IN (SELECT * FROM (VALUES('SEG'),('PREV')) x) THEN 1
							 WHEN ".intval($args['cd_usuario'])." = pfi.cd_dir_administrativo_sub AND d.cd_diretoria IN (SELECT * FROM (VALUES('ADM'),('INFR')) x) THEN 2
							 WHEN ".intval($args['cd_usuario'])." = pfi.cd_presidente_sub AND d.cd_diretoria IN (SELECT * FROM (VALUES('PRE')) x) THEN 3
							 WHEN ".intval($args['cd_usuario'])." = pfi.cd_dir_financeiro_sub AND  d.cd_diretoria IN (SELECT * FROM (VALUES('FIN')) x) THEN 4
							 ELSE 0
						 END)										 
		 	 WHERE pfi.dt_exclusao IS NULL
			   AND pfi.dt_encerra IS NULL
			   AND pfi.dt_envio_diretoria IS NOT NULL
			   AND ".intval($args['cd_usuario'])." IN (pfi.cd_dir_seguridade_sub, pfi.cd_presidente_sub, pfi.cd_dir_financeiro_sub, pfi.cd_dir_administrativo_sub)
			   ".(trim($args['nr_ano']) != '' ? "AND pfi.nr_ano = ".intval($args['nr_ano']) : '')."
			   ".(trim($args['nr_mes']) != '' ? "AND pfi.nr_mes = ".intval($args['nr_mes']) : '')."
			   ".(trim($args['fl_assinado']) == 'S' ? "AND d.dt_inclusao IS NOT NULL" : '')."
			   ".(trim($args['fl_assinado']) == 'N' ? "AND d.dt_inclusao IS NULL" : '')."
			   
             ORDER BY nr_ano_mes";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function carrega_assinar(&$result, $args=array())
	{
		$qr_sql = "
			SELECT pfi.cd_plano_fiscal_indicador,
                   pfi.nr_ano || '/' || TO_CHAR(pfi.nr_mes, 'FM00')  AS nr_ano_mes,
                   uc.nome,
				   TO_CHAR(d.dt_inclusao , 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   '".trim($args['cd_diretoria'])."' AS cd_diretoria,
				   CASE WHEN d.dt_inclusao IS NOT NULL THEN 'label'
				        WHEN pfi.dt_limite_diretoria::date <= CURRENT_DATE THEN 'label label-important'
				        ELSE 'label label-warning'
				   END AS class_dt_limite,
				   TO_CHAR(pfi.dt_limite_diretoria, 'DD/MM/YYYY') AS dt_limite_diretoria,
				   pfi.dt_encerra
              FROM gestao.plano_fiscal_indicador pfi
			  LEFT JOIN gestao.plano_fiscal_indicador_diretoria d
			    ON d.cd_plano_fiscal_indicador = pfi.cd_plano_fiscal_indicador
               AND d.dt_exclusao IS NULL
			   AND d.cd_diretoria = '".trim($args['cd_diretoria'])."'
              LEFT JOIN projetos.usuarios_controledi uc
                ON uc.codigo = d.cd_usuario_assinatura
             WHERE pfi.dt_exclusao IS NULL
			   AND pfi.dt_encerra IS NULL
			   AND pfi.cd_plano_fiscal_indicador = ".intval($args['cd_plano_fiscal_indicador'])."
			   AND (pfi.".$args['where_cd']." = ".intval($args['cd_usuario'])." OR pfi.".$args['where_cd']."_sub = ".intval($args['cd_usuario'])." )
             ORDER BY nr_ano, nr_mes";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function salvar_diretoria_assinar(&$result, $args=array())
	{
		$qr_sql = "
			INSERT INTO gestao.plano_fiscal_indicador_diretoria
			     (
                    cd_plano_fiscal_indicador, 
					cd_diretoria, 
                    cd_usuario_assinatura
			     )
			VALUES 
			    (
					".intval($args['cd_plano_fiscal_indicador']).",
					'".trim($args['cd_diretoria'])."',
					".intval($args['cd_usuario'])."
				);";
			
		$result = $this->db->query($qr_sql);
	}

  function prorrogacao(&$result, $args=array())
  {
    $qr_sql = "
      UPDATE gestao.plano_fiscal_indicador_item
          SET dt_limite            = ".(trim($args['dt_limite']) == "" ? "dt_limite" : "TO_DATE('".trim($args['dt_limite'])."','DD/MM/YYYY')").",
              cd_usuario_alteracao = ".(trim($args['cd_usuario']) == "" ? "DEFAULT" : intval($args['cd_usuario'])).",
              dt_alteracao         = CURRENT_TIMESTAMP
        WHERE cd_plano_fiscal_indicador = ".intval($args['cd_plano_fiscal_indicador'])."
          AND dt_confirmacao IS NULL
          AND dt_exclusao    IS NULL
          AND dt_envio       IS NOT NULL;";
    $result = $this->db->query($qr_sql);
  }

  public function get_periodicidade()
  {
    $qr_sql = "
      SELECT cd_plano_fiscal_indicador_periodicidade AS value,
             ds_plano_fiscal_indicador_periodicidade AS text
        FROM gestao.plano_fiscal_indicador_periodicidade
       WHERE dt_exclusao IS NULL
       ORDER BY ds_plano_fiscal_indicador_periodicidade ASC";

    return $this->db->query($qr_sql)->result_array();
  }
}
?>