<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	$tpl = new TemplatePower('tpl/tpl_registro_escopo_projeto_rel.html');
	$tpl->prepare();
	
	#### INF IMPRESSAO ####
	$tpl->newBlock('dt_impressao');
	$tpl->assign('dt_impressao', date("d/m/Y"));
	$tpl->assign('ds_usuario', $N);	
	
	#### NOME DO PROJETO ####
	$sql = " 
			SELECT p.nome
			  FROM projetos.projetos p,
			       projetos.acompanhamento_projetos ap
			 WHERE ap.cd_acomp   = ".$_REQUEST['cd_acomp']."
			   AND ap.cd_projeto = p.codigo	
		   ";
	$rs  = pg_query($db, $sql);
	$reg = pg_fetch_array($rs);
	$tpl->newBlock('projeto');
	$tpl->assign('ds_projeto', $reg['nome']);
	
	#### ESCOPO ####
	$qr_select = "
					SELECT dt_cadastro, 
	                       ds_objetivos, 
						   ds_regras, 
						   ds_impacto, 
						   ds_responsaveis, 
						   ds_solucao, 
	                       ds_recurso, 
						   ds_viabilidade, 
						   ds_modelagem, 
						   ds_produtos, 
						   dt_exclusao
					  FROM projetos.acompanhamento_escopos ae			  
					 WHERE ae.cd_acomp                  = ".$_REQUEST['cd_acomp']." 
					   AND ae.cd_acompanhamento_escopos = ".$_REQUEST['cd_escopo']."
				 ";
	$ob_resul  = pg_query($db, $qr_select);
	$ar_select = pg_fetch_array($ob_resul);

	#### 1) ####
	$tpl->newBlock('registro');	
	$tpl->assign('ds_pergunta', "1) Objetivo");	
	$tpl->assign('ds_resposta', nl2br($ar_select['ds_objetivos']));	

	#### 2) ####
	$tpl->newBlock('registro');	
	$tpl->assign('ds_pergunta', "2) Regras de Negуcio/Funcionalidas");	
	$tpl->assign('ds_resposta', nl2br($ar_select['ds_regras']));	

	#### 3) ####
	$tpl->newBlock('registro');	
	$tpl->assign('ds_pergunta', "3) Impacto");	
	$tpl->assign('ds_resposta', nl2br($ar_select['ds_impacto']));	
	
	#### 4) ####
	$tpl->newBlock('registro');	
	$tpl->assign('ds_pergunta', "4) Responsбveis");	
	$tpl->assign('ds_resposta', nl2br($ar_select['ds_responsaveis']));	
	
	#### 5) ####
	$tpl->newBlock('registro');	
	$tpl->assign('ds_pergunta', "5) Soluзгo Imediata (opcional)");	
	$tpl->assign('ds_resposta', nl2br($ar_select['ds_solucao']));

	#### 6) ####
	$tpl->newBlock('registro');	
	$tpl->assign('ds_pergunta', "6) Recurso/Custo");	
	$tpl->assign('ds_resposta', nl2br($ar_select['ds_recurso']));

	#### 7) ####
	$tpl->newBlock('registro');	
	$tpl->assign('ds_pergunta', "7) Viabilidade/Sugestгo (opcional)");	
	$tpl->assign('ds_resposta', nl2br($ar_select['ds_viabilidade']));

	#### 8) ####
	$tpl->newBlock('registro');	
	$tpl->assign('ds_pergunta', "8) Modelagem de Dados");	
	$tpl->assign('ds_resposta', nl2br($ar_select['ds_modelagem']));

	#### 9) ####
	$tpl->newBlock('registro');	
	$tpl->assign('ds_pergunta', "9) Produtos");	
	$tpl->assign('ds_resposta', nl2br($ar_select['ds_produtos']));	
	
	
	$tpl->printToScreen();	
	
?>