<?php
   include_once('inc/sessao.php');
   include_once('inc/class.TemplatePower.inc.php');
      include_once('inc/conexao.php');

   header( 'location:'.base_url().'index.php/gestao/relatorio_acoes_corretivas');
   if ($t=='prn') {
      $tpl = new TemplatePower('tpl/tpl_perc_itens_no_prazo_imp.html');
   }
   else {
      $tpl = new TemplatePower('tpl/tpl_rel_nc_ac.html');
   }
   $tpl->assignInclude('mn_sup', 'menu/menu_projetos.htm');
   $tpl->prepare();
   
   $PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
   $tpl->assign('lst', $l);
   $tpl->assign('flt', $f);
   
   	###### 2009 #######
	#### QUADRO RESUMO NÃO CONFORMIDADES ####
	$qr_sql = " 
				SELECT SUM(t.qt_aberta) AS qt_aberta,
				       SUM(t.qt_nao_implementada_prazo) AS qt_nao_implementada_prazo,
				       SUM(t.qt_nao_implementada_fora) AS qt_nao_implementada_fora,
				       SUM(t.qt_implementada_prazo) AS qt_implementada_prazo,
				       SUM(t.qt_implementada_fora) AS qt_implementada_fora
				FROM (SELECT COUNT(*) AS qt_aberta,
				             0 AS qt_nao_implementada_prazo,
				             0 AS qt_nao_implementada_fora,
				             0 AS qt_implementada_prazo,
                             0 AS qt_implementada_fora
				        FROM projetos.nao_conformidade nc

					   UNION

					  SELECT 0 AS qt_aberta,
				             COUNT(*) AS qt_nao_implementada_prazo,
				             0 AS qt_nao_implementada_fora,
				             0 AS qt_implementada_prazo,
                             0 AS qt_implementada_fora
				        FROM projetos.nao_conformidade nc
				        LEFT JOIN projetos.acao_corretiva ac
				          ON ac.cd_nao_conformidade = nc.cd_nao_conformidade 
                       WHERE COALESCE(COALESCE(ac.dt_prorrogada,ac.dt_prop_imp),(nc.dt_cadastro + '15 days'::interval)) > CURRENT_DATE
				         AND ac.dt_efe_imp                             IS NULL				   			 

                       UNION
                                 
				      SELECT 0 AS qt_aberta,
				             0 AS qt_nao_implementada_prazo,
				             COUNT(*) AS qt_nao_implementada_fora,
				             0 AS qt_implementada_prazo,
                             0 AS qt_implementada_fora
				        FROM projetos.nao_conformidade nc
				        LEFT JOIN projetos.acao_corretiva ac
				          ON ac.cd_nao_conformidade = nc.cd_nao_conformidade 
                       WHERE COALESCE(COALESCE(ac.dt_prorrogada,ac.dt_prop_imp),(nc.dt_cadastro + '15 days'::interval)) < CURRENT_DATE
				         AND ac.dt_efe_imp                             IS NULL
				   
                       UNION
                                 
				      SELECT 0 AS qt_aberta,
				             0 AS qt_nao_implementada_prazo,
				             0 AS qt_implementada_prazo,
				             COUNT(*) AS qt_implementada_prazo,
                             0 AS qt_implementada_fora
				        FROM projetos.nao_conformidade nc
				        JOIN projetos.acao_corretiva ac
				          ON ac.cd_nao_conformidade = nc.cd_nao_conformidade 
				       WHERE COALESCE(ac.dt_prorrogada,ac.dt_prop_imp) >= ac.dt_efe_imp
				 
				       UNION
				 
				      SELECT 0 AS qt_aberta,
				             0 AS qt_nao_implementada_prazo,
				             0 AS qt_nao_implementada_fora,
				             0 AS qt_implementada_prazo,
                             COUNT(*) AS qt_implementada_fora
				        FROM projetos.nao_conformidade nc
				        JOIN projetos.acao_corretiva ac
				          ON ac.cd_nao_conformidade = nc.cd_nao_conformidade 
				       WHERE COALESCE(ac.dt_prorrogada,ac.dt_prop_imp) < ac.dt_efe_imp) t
	           ";
	$ob_resul = pg_query($db, $qr_sql);	
	$ar_reg = pg_fetch_array($ob_resul);

	$tpl->newBlock('lista_quadro_nc');
	$tpl->assign('cor_fundo',  'quadro-impar');
	$tpl->assign('ds_item',  'Não Implementada com Prazo Futuro');
	$tpl->assign('qt_item',  $ar_reg['qt_nao_implementada_prazo']);	
	
	$tpl->newBlock('lista_quadro_nc');
	$tpl->assign('cor_fundo',  'quadro-par');
	$tpl->assign('ds_item',  'Não Implementada com Prazo Vencido');
	$tpl->assign('qt_item',  $ar_reg['qt_nao_implementada_fora']);		
	
	$tpl->newBlock('lista_quadro_nc');
	$tpl->assign('cor_fundo',  'quadro-impar');
	$tpl->assign('ds_item',  'Implementada no Prazo');
	$tpl->assign('qt_item',  $ar_reg['qt_implementada_prazo']);		
	
	$tpl->newBlock('lista_quadro_nc');
	$tpl->assign('cor_fundo',  'quadro-par');
	$tpl->assign('ds_item',  'Implementada Fora do Prazo');
	$tpl->assign('qt_item',  $ar_reg['qt_implementada_fora']);			

	$tpl->newBlock('lista_quadro_nc');
	$tpl->assign('cor_fundo',  'quadro-impar');
	$tpl->assign('ds_item',  'Total Aberta');
	$tpl->assign('qt_item',  $ar_reg['qt_aberta']);	
	
	#### GRAFICOS ####
	$tpl->assignGlobal('qt_nao_implementada_fora',  $ar_reg['qt_nao_implementada_fora']);	
	$tpl->assignGlobal('qt_implementada',  $ar_reg['qt_implementada_prazo']+$ar_reg['qt_implementada_fora']);		
	
	$tpl->assignGlobal('qt_implementada_prazo',  $ar_reg['qt_implementada_prazo']);	
	$tpl->assignGlobal('qt_implementada_fora',  $ar_reg['qt_implementada_fora']);		
	
	
	#### QUADRO RESUMO AÇÕES CORRETIVAS ####
	$qr_sql = " 
				SELECT SUM(t.qt_ac_apresentada_prazo) AS qt_ac_apresentada_prazo,
				       SUM(t.qt_ac_apresentada_fora) AS qt_ac_apresentada_fora,
				       SUM(t.qt_ac_nao_apresentada_prazo) AS qt_ac_nao_apresentada_prazo,
				       SUM(t.qt_ac_nao_apresentada_fora) AS qt_ac_nao_apresentada_fora,
                       (SUM(t.qt_ac_apresentada_prazo)+SUM(t.qt_ac_apresentada_fora)+SUM(t.qt_ac_nao_apresentada_prazo)+SUM(t.qt_ac_nao_apresentada_fora)) AS qt_ac_total
				  FROM (SELECT COUNT(*) AS qt_ac_apresentada_prazo, -- COM NO PRAZO
                               0 AS qt_ac_apresentada_fora,
                               0 AS qt_ac_nao_apresentada_prazo,
                               0 AS qt_ac_nao_apresentada_fora
				          FROM projetos.nao_conformidade nc
				          JOIN projetos.acao_corretiva ac
				            ON ac.cd_nao_conformidade = nc.cd_nao_conformidade 
                         WHERE ac.dt_apres <= ac.dt_limite_apres 			   

                         UNION

				        SELECT 0 AS qt_ac_apresentada_prazo,
                               COUNT(*) AS qt_ac_apresentada_fora, -- COM FORA DO PRAZO 
                               0 AS qt_ac_nao_apresentada_prazo,
                               0 AS qt_ac_nao_apresentada_fora
				          FROM projetos.nao_conformidade nc
				          JOIN projetos.acao_corretiva ac
				            ON ac.cd_nao_conformidade = nc.cd_nao_conformidade 
                         WHERE ac.dt_limite_apres < ac.dt_apres

                         UNION

				        SELECT 0 AS qt_ac_apresentada_prazo,
                               0 AS qt_ac_apresentada_fora,
                               COUNT(*) AS qt_ac_nao_apresentada_prazo, -- SEM FUTURO
                               0 AS qt_ac_nao_apresentada_fora
				          FROM projetos.nao_conformidade nc
				          LEFT JOIN projetos.acao_corretiva ac
				            ON ac.cd_nao_conformidade = nc.cd_nao_conformidade 
                         WHERE COALESCE(ac.dt_limite_apres, (nc.dt_cadastro + '15 days'::interval)) >= CURRENT_DATE
                           AND ac.dt_apres IS NULL  

                         UNION

				        SELECT 0 AS qt_ac_apresentada_prazo,
                               0 AS qt_ac_apresentada_fora,
                               0 AS qt_ac_nao_apresentada_prazo,
                               COUNT(*) AS qt_ac_nao_apresentada_fora -- SEM VENCIDO
				          FROM projetos.nao_conformidade nc
				          LEFT JOIN projetos.acao_corretiva ac
				            ON ac.cd_nao_conformidade = nc.cd_nao_conformidade 
                         WHERE COALESCE(ac.dt_limite_apres, (nc.dt_cadastro + '15 days'::interval)) < CURRENT_DATE
                           AND ac.dt_apres IS NULL) t  
	           ";
	$ob_resul = pg_query($db, $qr_sql);	
	$ar_reg = pg_fetch_array($ob_resul);

	$tpl->newBlock('lista_quadro_ac');
	$tpl->assign('cor_fundo',  'quadro-impar');
	$tpl->assign('ds_item',  'Não Apresentada com Prazo Futuro');
	$tpl->assign('qt_item',  $ar_reg['qt_ac_nao_apresentada_prazo']);		
	
	$tpl->newBlock('lista_quadro_ac');
	$tpl->assign('cor_fundo',  'quadro-par');
	$tpl->assign('ds_item',  'Não Apresentada com Prazo Vencido');
	$tpl->assign('qt_item',  $ar_reg['qt_ac_nao_apresentada_fora']);		
	
	$tpl->newBlock('lista_quadro_ac');
	$tpl->assign('cor_fundo',  'quadro-impar');
	$tpl->assign('ds_item',  'Apresentada no Prazo');
	$tpl->assign('qt_item',  $ar_reg['qt_ac_apresentada_prazo']);	
	
	$tpl->newBlock('lista_quadro_ac');
	$tpl->assign('cor_fundo',  'quadro-par');
	$tpl->assign('ds_item',  'Apresentada Fora do Prazo');
	$tpl->assign('qt_item',  $ar_reg['qt_ac_apresentada_fora']);		
	
	$tpl->newBlock('lista_quadro_ac');
	$tpl->assign('cor_fundo',  'quadro-impar');
	$tpl->assign('ds_item',  'Total');
	$tpl->assign('qt_item',  $ar_reg['qt_ac_total']);	
	
	#### GRAFICOS ####
	$tpl->assignGlobal('qt_ac_nao_apresentada_fora',  $ar_reg['qt_ac_nao_apresentada_fora']);	
	$tpl->assignGlobal('qt_apresentada',  $ar_reg['qt_ac_apresentada_prazo']+$ar_reg['qt_ac_apresentada_fora']);
	
	$tpl->assignGlobal('qt_ac_apresentada_prazo',  $ar_reg['qt_ac_apresentada_prazo']);	
	$tpl->assignGlobal('qt_ac_apresentada_fora',  $ar_reg['qt_ac_apresentada_fora']);		

	
	
	#### LISTA ACAO CORRETIVA NÃO APRESENTADA NO PRAZO####
	$qr_sql = " 
				SELECT nc.cd_nao_conformidade, 
				       TO_CHAR(nc.dt_cadastro,'DD/MM/YYYY') AS dt_abertura,
				       TO_CHAR(ac.dt_apres,'DD/MM/YYYY') AS dt_apresenta,
				       TO_CHAR(COALESCE(ac.dt_limite_apres, (nc.dt_cadastro + '15 days'::interval)),'DD/MM/YYYY') AS dt_apresenta_limite,
				       uc.nome AS ds_responsavel,
				       p.cod_responsavel AS area_responsavel,
				       (nc.dt_cadastro + '15 days'::interval) , ac.dt_apres
				  FROM projetos.nao_conformidade nc
				  LEFT JOIN projetos.acao_corretiva ac
				    ON ac.cd_nao_conformidade = nc.cd_nao_conformidade 
				  JOIN projetos.usuarios_controledi uc
				    ON uc.codigo = nc.cd_responsavel
				  JOIN projetos.processos p
				    ON nc.cd_processo = p.cd_processo
				 WHERE COALESCE(ac.dt_limite_apres, (nc.dt_cadastro + '15 days'::interval)) < CURRENT_DATE
				   AND ac.dt_apres        IS NULL
				 ORDER BY nc.cd_nao_conformidade DESC
	           ";
	$ob_resul = pg_query($db, $qr_sql);
	$tpl->assignGlobal('qt_ac_nao_apresentada', pg_num_rows($ob_resul));
	while ($ar_reg=pg_fetch_array($ob_resul)) 
	{
		$tpl->newBlock('lista_ac_nao_apresentada');
		$tpl->assign('cd_nao_conformidade',  $ar_reg['cd_nao_conformidade']);
		$tpl->assign('dt_abertura',  $ar_reg['dt_abertura']);
		$tpl->assign('dt_apresenta_limite',  $ar_reg['dt_apresenta_limite']);
		$tpl->assign('area_responsavel',  $ar_reg['area_responsavel']);
		$tpl->assign('ds_responsavel',  $ar_reg['ds_responsavel']);
	} 	
	
	#### LISTA ACAO CORRETIVA APRESENTA FORA DO PRAZO ####
	$qr_sql = " 
				SELECT nc.cd_nao_conformidade, 
				       TO_CHAR(nc.dt_cadastro,'DD/MM/YYYY') AS dt_abertura,
				       TO_CHAR(ac.dt_apres,'DD/MM/YYYY') AS dt_apresenta,
				       TO_CHAR(ac.dt_limite_apres,'DD/MM/YYYY') AS dt_apresenta_limite,
				       TO_CHAR(ac.dt_prop_imp,'DD/MM/YYYY') AS dt_proposta,
				       TO_CHAR(ac.dt_prorrogada,'DD/MM/YYYY') AS dt_prorrogada,
				       TO_CHAR(ac.dt_efe_imp,'DD/MM/YYYY') AS dt_implementacao,
				       uc.nome AS ds_responsavel,
				       p.cod_responsavel AS area_responsavel,
				       (nc.dt_cadastro + '15 days'::interval) , ac.dt_apres
				  FROM projetos.nao_conformidade nc
				  JOIN projetos.acao_corretiva ac
				    ON ac.cd_nao_conformidade = nc.cd_nao_conformidade 
				  JOIN projetos.usuarios_controledi uc
				    ON uc.codigo = nc.cd_responsavel
				  JOIN projetos.processos p
				    ON nc.cd_processo = p.cd_processo
				 WHERE ac.dt_limite_apres < ac.dt_apres
				 ORDER BY nc.cd_nao_conformidade DESC
	           ";
	$ob_resul = pg_query($db, $qr_sql);
	$tpl->assignGlobal('qt_ac_apresentada_atrasada', pg_num_rows($ob_resul));
	while ($ar_reg=pg_fetch_array($ob_resul)) 
	{
		$tpl->newBlock('lista_ac_apresentada_atrasada');
		$tpl->assign('cd_nao_conformidade',  $ar_reg['cd_nao_conformidade']);
		$tpl->assign('dt_abertura',  $ar_reg['dt_abertura']);
		$tpl->assign('dt_apresenta_limite',  $ar_reg['dt_apresenta_limite']);
		$tpl->assign('dt_apresenta',  $ar_reg['dt_apresenta']);
		$tpl->assign('area_responsavel',  $ar_reg['area_responsavel']);
		$tpl->assign('ds_responsavel',  $ar_reg['ds_responsavel']);
	} 
	
	#### LISTA NÃO CONFORMIDADE COM A IMPLEMENTAÇÃO ATRASADA ####
	$qr_sql = " 
				SELECT nc.cd_nao_conformidade, 
				       TO_CHAR(nc.dt_cadastro,'DD/MM/YYYY') AS dt_abertura,
				       TO_CHAR(ac.dt_prop_imp,'DD/MM/YYYY') AS dt_proposta,
				       TO_CHAR(ac.dt_prorrogada,'DD/MM/YYYY') AS dt_prorrogada,
				       TO_CHAR(ac.dt_efe_imp,'DD/MM/YYYY') AS dt_implementacao,
				       uc.nome AS ds_responsavel,
				       p.cod_responsavel AS area_responsavel
				  FROM projetos.nao_conformidade nc
				  JOIN projetos.acao_corretiva ac
				    ON ac.cd_nao_conformidade = nc.cd_nao_conformidade 
				  JOIN projetos.usuarios_controledi uc
				    ON uc.codigo = nc.cd_responsavel
				  JOIN projetos.processos p
				    ON nc.cd_processo = p.cd_processo
				 WHERE COALESCE(ac.dt_prorrogada,ac.dt_prop_imp) < CURRENT_DATE
				   AND ac.dt_efe_imp                             IS NULL
				 ORDER BY nc.cd_nao_conformidade DESC
	           ";
	$ob_resul = pg_query($db, $qr_sql);
	$tpl->assignGlobal('qt_implementacao_atrasada', pg_num_rows($ob_resul));
	while ($ar_reg=pg_fetch_array($ob_resul)) 
	{
		$tpl->newBlock('lista_implementacao_atrasada');
		$tpl->assign('cd_nao_conformidade',  $ar_reg['cd_nao_conformidade']);
		$tpl->assign('dt_abertura',  $ar_reg['dt_abertura']);
		$tpl->assign('dt_proposta',  $ar_reg['dt_proposta']);
		$tpl->assign('dt_prorrogada',  $ar_reg['dt_prorrogada']);
		$tpl->assign('dt_implementacao',  $ar_reg['dt_implementacao']);
		$tpl->assign('area_responsavel',  $ar_reg['area_responsavel']);
		$tpl->assign('ds_responsavel',  $ar_reg['ds_responsavel']);
	} 	
	
	#### LISTA NÃO CONFORMIDADE IMPLEMENTADAS FORA DO PRAZO ####
	$qr_sql = " 
				SELECT nc.cd_nao_conformidade, 
				       TO_CHAR(nc.dt_cadastro,'DD/MM/YYYY') AS dt_abertura,
				       TO_CHAR(ac.dt_prop_imp,'DD/MM/YYYY') AS dt_proposta,
				       TO_CHAR(ac.dt_prorrogada,'DD/MM/YYYY') AS dt_prorrogada,
				       TO_CHAR(ac.dt_efe_imp,'DD/MM/YYYY') AS dt_implementacao,
				       uc.nome AS ds_responsavel,
				       p.cod_responsavel AS area_responsavel
				  FROM projetos.nao_conformidade nc
				  JOIN projetos.acao_corretiva ac
				    ON ac.cd_nao_conformidade = nc.cd_nao_conformidade 
				  JOIN projetos.usuarios_controledi uc
				    ON uc.codigo = nc.cd_responsavel
				  JOIN projetos.processos p
				    ON nc.cd_processo = p.cd_processo
				 WHERE COALESCE(ac.dt_prorrogada,ac.dt_prop_imp) < ac.dt_efe_imp
				 ORDER BY nc.cd_nao_conformidade DESC
	           ";
	$ob_resul = pg_query($db, $qr_sql);
	$tpl->assignGlobal('qt_implementada_atrasada', pg_num_rows($ob_resul));
	while ($ar_reg=pg_fetch_array($ob_resul)) 
	{
		$tpl->newBlock('lista_implementada_atrasada');
		$tpl->assign('cd_nao_conformidade',  $ar_reg['cd_nao_conformidade']);
		$tpl->assign('dt_abertura',  $ar_reg['dt_abertura']);
		$tpl->assign('dt_proposta',  $ar_reg['dt_proposta']);
		$tpl->assign('dt_prorrogada',  $ar_reg['dt_prorrogada']);
		$tpl->assign('dt_implementacao',  $ar_reg['dt_implementacao']);
		$tpl->assign('area_responsavel',  $ar_reg['area_responsavel']);
		$tpl->assign('ds_responsavel',  $ar_reg['ds_responsavel']);
	}   
   
   
   
   
   
   
   
   
   
   
   
   
   
//-------------------------------------------------------------------   G1
	$sql =   " ";
	$sql = $sql . "  select to_char(nc.dt_cadastro,'dd/mm/yyyy') as dt_inclusao, ";
	$sql = $sql . "         nc.cd_nao_conformidade as cod_nao_conf,              ";
	$sql = $sql . "         nc.numero_cad_nc       as numero_cad_nc,             ";
	$sql = $sql . "			p.cod_responsavel, ";
	$sql = $sql . "         (select puc1.nome                                    ";
	$sql = $sql . "             from 	projetos.usuarios_controledi puc1,          ";
	$sql = $sql . "                  	projetos.nao_conformidade pnc               ";
	$sql = $sql . "             where 	nc.cd_responsavel = puc1.codigo           ";
	$sql = $sql . "                   	and pnc.cd_nao_conformidade = nc.cd_nao_conformidade ) as nome_aberto_por ";
	$sql = $sql . "  from 	projetos.nao_conformidade    nc, projetos.processos p                       ";
	$sql = $sql . "	where 	nc.cd_nao_conformidade not in ";
	$sql = $sql . " (select cd_nao_conformidade from projetos.acao_corretiva) and nc.dt_cadastro < (current_date - 15)  ";
	$sql = $sql . "		and	nc.cd_processo = p.cd_processo ";
	$sql = $sql . "  order 	by nc.cd_nao_conformidade  desc    ";
//  echo $sql;
	$rs = pg_exec($sql);
	while ($reg=pg_fetch_array($rs))
	{
//		Se tiver sido mandado mensagem EV5 e mesmo assim, não existir na tabela AC
		$tpl->newBlock('G1');
		$tpl->assign('nce', conv_num_nc($reg['cod_nao_conf']));
		$tpl->assign('nc', $reg['cod_nao_conf']);
		$tpl->assign('data', $reg['dt_inclusao']);
		$tpl->assign('nome', $reg['nome_aberto_por']);
		$tpl->assign('div', $reg['cod_responsavel']);
	}
//-------------------------------------------------------------------	G2
	$sql =   " ";
	$sql = $sql . "  select to_char(nc.dt_cadastro,'dd/mm/yyyy') as dt_inclusao, ";
	$sql = $sql . "         nc.cd_nao_conformidade as cod_nao_conf,              ";
	$sql = $sql . "         nc.numero_cad_nc       as numero_cad_nc,             ";
	$sql = $sql . "			puc1.nome				as nome, 					";
	$sql = $sql . "			p.cod_responsavel ";	
	$sql = $sql . "  from 	projetos.nao_conformidade    nc, projetos.acao_corretiva ac, 	projetos.usuarios_controledi puc1, projetos.processos p                       ";
	$sql = $sql . "	where 	(ac.dt_apres > ac.dt_limite_apres or ((ac.dt_apres is null) and (ac.dt_limite_apres < current_date)))  ";
	$sql = $sql . " and 	(nc.cd_nao_conformidade = ac.cd_nao_conformidade and ac.cd_processo = nc.cd_processo and 	nc.cd_nao_conformidade = ac.cd_acao) ";
	$sql = $sql . " and 	(nc.cd_responsavel = puc1.codigo)";
	$sql = $sql . "	and		(nc.cd_processo = p.cd_processo) ";	
	$sql = $sql . "  order by nc.cd_nao_conformidade  desc    ";
//  echo $sql;
	$rs = pg_exec($sql);
	while ($reg=pg_fetch_array($rs))
	{
//		
		$tpl->newBlock('G2');
		$tpl->assign('nce', conv_num_nc($reg['cod_nao_conf']));
		$tpl->assign('nc', $reg['cod_nao_conf']);
		$tpl->assign('data', $reg['dt_inclusao']);
		$tpl->assign('nome', $reg['nome']);
		$tpl->assign('div', $reg['cod_responsavel']);
	}
//-------------------------------------------------------------------	G3
	$sql =   " ";
	$sql = $sql . "  select to_char(nc.dt_cadastro,'dd/mm/yyyy') as dt_inclusao, ";
	$sql = $sql . "         nc.cd_nao_conformidade 	as cod_nao_conf,              ";
	$sql = $sql . "         nc.numero_cad_nc       	as numero_cad_nc,             ";
	$sql = $sql . "			puc1.nome				as nome, 					";
	$sql = $sql . "			p.cod_responsavel ";
	$sql = $sql . "  from 	projetos.nao_conformidade    nc, projetos.acao_corretiva ac, projetos.usuarios_controledi puc1, projetos.processos p ";
//	$sql = $sql . "	where (ac.dt_efe_imp is null and (ac.dt_prop_imp < current_date))  ";
	$sql = $sql . " where (ac.dt_efe_imp is null and (((ac.dt_prop_imp < current_date) and (dt_prorrogada is null)) or ((ac.dt_prorrogada < current_date) and (dt_prorrogada is not null)))) ";
	$sql = $sql . " and 	(nc.cd_nao_conformidade = ac.cd_nao_conformidade and ac.cd_processo = nc.cd_processo and 	nc.cd_nao_conformidade = ac.cd_acao) ";
	$sql = $sql . " and 	(nc.cd_responsavel = puc1.codigo)";
	$sql = $sql . "	and		(nc.cd_processo = p.cd_processo) ";	
	$sql = $sql . "  order by nc.cd_nao_conformidade  desc    ";
//  echo $sql;
	$rs = pg_exec($sql);
	while ($reg=pg_fetch_array($rs))
	{
		$tpl->newBlock('G3');
		$tpl->assign('nce', conv_num_nc($reg['cod_nao_conf']));
		$tpl->assign('nc', $reg['cod_nao_conf']);
		$tpl->assign('data', $reg['dt_inclusao']);
		$tpl->assign('nome', $reg['nome']);
		$tpl->assign('div', $reg['cod_responsavel']);
	}
//-------------------------------------------------------------------	G4
	$sql =   " ";
	$sql = $sql . "  select to_char(nc.dt_cadastro,'dd/mm/yyyy') as dt_inclusao, ";
	$sql = $sql . "         nc.cd_nao_conformidade 	as cod_nao_conf,              ";
	$sql = $sql . "         nc.numero_cad_nc       	as numero_cad_nc,             ";
	$sql = $sql . "			puc1.nome				as nome,					";
	$sql = $sql . "			p.cod_responsavel ";
	$sql = $sql . "  from 	projetos.nao_conformidade    nc, projetos.acao_corretiva ac, 	projetos.usuarios_controledi puc1, projetos.processos p ";
//	$sql = $sql . "	where (ac.dt_efe_imp > ac.dt_prop_imp or (ac.dt_efe_imp is not null and ac.dt_prop_imp is null))  ";
	$sql = $sql . "	where 	(((ac.dt_efe_imp > ac.dt_prop_imp) and (dt_prorrogada is null)) or ((dt_prorrogada < dt_prop_imp) and (dt_prorrogada is not null))) ";
	$sql = $sql . " and 	(nc.cd_nao_conformidade = ac.cd_nao_conformidade and ac.cd_processo = nc.cd_processo and 	nc.cd_nao_conformidade = ac.cd_acao) ";
	$sql = $sql . " and 	(nc.cd_responsavel = puc1.codigo)";
	$sql = $sql . "	and		(nc.cd_processo = p.cd_processo) ";		
	$sql = $sql . "  order by nc.cd_nao_conformidade  desc    ";
//  echo $sql;
	$rs = pg_exec($sql);
	while ($reg=pg_fetch_array($rs))
	{
//		Se tiver sido mandado mensagem EV5 e mesmo assim, não existir na tabela AC
		$tpl->newBlock('G4');
		$tpl->assign('nce', conv_num_nc($reg['cod_nao_conf']));
		$tpl->assign('nc', $reg['cod_nao_conf']);
		$tpl->assign('data', $reg['dt_inclusao']);
		$tpl->assign('nome', $reg['nome']);
		$tpl->assign('div', $reg['cod_responsavel']);
	}
//-------------------------------------------------------------------
   



   


	
	
	
	$tpl->printToScreen();
	pg_close($db);

 function conv_num_nc($n) {
// Pressupõe que o num esteja no formato AAAANNN
		$aaaa = substr($n, 0, 4);
		$nc = substr($n, 4, 3);
		return $nc.'/'.$aaaa;
	}
?>