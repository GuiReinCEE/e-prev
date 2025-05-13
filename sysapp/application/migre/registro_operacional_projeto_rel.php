<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	$tpl = new TemplatePower('tpl/tpl_registro_operacional_projeto_rel.html');
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
	
	#### REGISTRO OPERACIONAL ####
	$qr_select = "
					SELECT 
						   aro.ds_nome,
						   aro.ds_processo_faz,
						   aro.ds_processo_faz_complemento,
						   aro.ds_processo_executado,
						   aro.ds_processo_executado_complemento,
						   aro.ds_calculo,
						   aro.ds_calculo_complemento,
						   aro.ds_responsaveis,
						   aro.ds_requesito,
						   aro.ds_requesito_complemento,
						   aro.ds_necessario,
						   aro.ds_necessario_complemento,
						   aro.ds_integridade,
						   aro.ds_integridade_complemento,
						   aro.ds_resultado,
						   aro.ds_resultado_complemento,
						   aro.ds_local,
						   aro.dt_finalizado,
						   aro.cd_usuario,
						   aro.ds_arquivo_fisico,
						   uc.nome AS ds_usuario
					  FROM projetos.acompanhamento_registro_operacional aro,
						   projetos.usuarios_controledi uc						  
					 WHERE aro.cd_acompanhamento_registro_operacional = ".$_REQUEST['cd_operacional']."
					   AND aro.cd_acomp                               = ".$_REQUEST['cd_acomp']." 
					   AND aro.cd_usuario                             = uc.codigo
				 ";
	$ob_resul  = pg_query($db, $qr_select);
	$ar_select = pg_fetch_array($ob_resul);


	$tpl->newBlock('registro');	
	$tpl->assign('ds_pergunta', "Autor");	
	$tpl->assign('ds_resposta', $ar_select['ds_usuario']);		

	$tpl->newBlock('registro');	
	$tpl->assign('ds_pergunta', "Nome Processo");	
	$tpl->assign('ds_resposta', $ar_select['ds_nome']);	
	
	#### 1) ####
	$tpl->newBlock('registro');	
	$tpl->assign('ds_pergunta', "1) O que o processo faz?");	
	$tpl->assign('ds_resposta', $ar_select['ds_processo_faz']);	
	if(trim($ar_select['ds_processo_faz_complemento']) != "")
	{
		$tpl->newBlock('complemento');
		$tpl->assign('ds_complemento', $ar_select['ds_processo_faz_complemento']);	
	}

	#### 2) ####
	$tpl->newBlock('registro');	
	$tpl->assign('ds_pergunta', "2) De que maneira й executado o processo?");	
	$tpl->assign('ds_resposta', $ar_select['ds_processo_executado']);	
	if(trim($ar_select['ds_processo_executado_complemento']) != "")
	{
		$tpl->newBlock('complemento');
		$tpl->assign('ds_complemento', $ar_select['ds_processo_executado_complemento']);	
	}

	#### 3) ####	
	$tpl->newBlock('registro');	
	$tpl->assign('ds_pergunta', "3) Cбlculos");	
	$tpl->assign('ds_resposta', $ar_select['ds_calculo']);	
	if(trim($ar_select['ds_calculo_complemento']) != "")
	{
		$tpl->newBlock('complemento');
		$tpl->assign('ds_complemento', $ar_select['ds_calculo_complemento']);	
	}

	#### 4) ####
	$tpl->newBlock('registro');	
	$tpl->assign('ds_pergunta', "4) Responsбveis");	
	$tpl->assign('ds_resposta', $ar_select['ds_responsaveis']);	

	#### 5) ####
	$tpl->newBlock('registro');	
	$tpl->assign('ds_pergunta', "5) O que й necessбrio para que este processo possa ocontecer?");	
	$tpl->assign('ds_resposta', $ar_select['ds_requesito']);	
	if(trim($ar_select['ds_requesito_complemento']) != "")
	{
		$tpl->newBlock('complemento');
		$tpl->assign('ds_complemento', $ar_select['ds_requesito_complemento']);	
	}	
	
	#### 6) ####
	$tpl->newBlock('registro');	
	$tpl->assign('ds_pergunta', "6) Este processo й necessбrio para qual(is) outro(s) processo(s)?");	
	$tpl->assign('ds_resposta', $ar_select['ds_necessario']);	
	if(trim($ar_select['ds_necessario_complemento']) != "")
	{
		$tpl->newBlock('complemento');
		$tpl->assign('ds_complemento', $ar_select['ds_necessario_complemento']);	
	}	
	
	#### 7) ####
	$tpl->newBlock('registro');	
	$tpl->assign('ds_pergunta', "7) Integraзгo com outros sistemas ");	
	$tpl->assign('ds_resposta', $ar_select['ds_integridade']);	
	if(trim($ar_select['ds_integridade_complemento']) != "")
	{
		$tpl->newBlock('complemento');
		$tpl->assign('ds_complemento', $ar_select['ds_integridade_complemento']);	
	}	
	
	#### 8) ####
	$tpl->newBlock('registro');	
	$tpl->assign('ds_pergunta', "8) Resultados");	
	$tpl->assign('ds_resposta', $ar_select['ds_resultado']);	
	if(trim($ar_select['ds_resultado_complemento']) != "")
	{
		$tpl->newBlock('complemento');
		$tpl->assign('ds_complemento', $ar_select['ds_resultado_complemento']);	
	}	

	#### 9) ####
	$tpl->newBlock('registro');	
	$tpl->assign('ds_pergunta', "9) Telas / Relatуrios / Planilhas ");	
	$tpl->assign('ds_resposta', $ar_select['ds_local']);	

	#### Anexo ####
	$tpl->newBlock('registro');	
	$tpl->assign('ds_pergunta', "Anexo:");	
	$tpl->assign('ds_resposta', $ar_select['ds_arquivo_fisico']);
	
	$tpl->printToScreen();	
?>