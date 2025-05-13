<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	$tpl = new TemplatePower('tpl/tpl_registro_previsao_projeto_rel.html');
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
	
	#### PREVISAO ####
	$qr_select = "
				SELECT cd_previsao, 
				       cd_acomp, 
					   descricao, 
					   mes, 
					   ano, 
					   obs,
					   TO_CHAR(dt_previsao, 'DD/MM/YYYY') AS data_previsao, 
					   dt_previsao
				  FROM projetos.previsoes_projetos 
				 WHERE cd_acomp    = ".$_REQUEST['cd_acomp']." 
				   AND cd_previsao = ".$_REQUEST['cd_previsao']."
				 ";
	$ob_resul  = pg_query($db, $qr_select);
	$ar_select = pg_fetch_array($ob_resul);

	$tpl->newBlock('registro');	
	$tpl->assign('ds_pergunta', "Mês/Ano:");	
	$tpl->assign('ds_resposta', "<span style='text-transform: uppercase;'>".$ar_select['mes']."/".$ar_select['ano']."</span>");	

	$tpl->newBlock('registro');	
	$tpl->assign('ds_pergunta', "Previsão:");	
	$tpl->assign('ds_resposta', $ar_select['descricao']);	

	$tpl->newBlock('registro');	
	$tpl->assign('ds_pergunta', "Observação:");	
	$tpl->assign('ds_resposta', $ar_select['obs']);	
	
	$tpl->printToScreen();	
?>