<?php
	#"e7a9e3f647dd33941430647118aaf2b7";"WEB - Autoatendimento"
	$_APP_ID  = "e7a9e3f647dd33941430647118aaf2b7";
	
	$_REQUEST['cd_secao'] = 'AACT'; #REMOVER
	$_REQUEST['cd_artigo'] = 204;   #REMOVER
	
	include_once('inc/sessao_auto_atendimento.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	
	$tpl = new TemplatePower('tpl/tpl_auto_atendimento.html');
	$tpl->prepare();
	include_once('auto_atendimento_monta_sessao.php');
	
	$ds_arq   = "tpl/tpl_auto_atendimento_instituidor_forma_pagamento.html";
	$ob_arq   = fopen($ds_arq, 'r');
	$conteudo = fread($ob_arq, filesize($ds_arq));
	fclose($ob_arq);		
	
	$qr_sql = "
				INSERT INTO public.log_acessos_usuario 
					 (
					   sid,
					   hora,
					   pagina
					 ) 
				VALUES
					 (
					   ".$_SESSION['SID'].",
					   CURRENT_TIMESTAMP,
					   'INSTITUIDOR_FORMA_PAGAMENTO' 
					 )
		      ";
	@pg_query($db,$qr_sql);  	
	
	#echo "<PRE>"; print_r($_SESSION); echo "</PRE>"; exit;
	
	if(intval($_SESSION['PLANO']) != 9)
	{
		$conteudo = "<BR>
						<DIV style='font-family: Calibri, Arial; width:100%; text-align:center;'>
							<H2>Somente para o PLANO FAMILIA PREVIDENCIA ASSOCIATIVO</H2>
						</DIV>
					<BR><BR>";		
		
		
		echo $conteudo;
		exit;	
	}
	
	
	#### FORMA DE PAGAMENTO ATUAL ####
	$tp_forma_de_pagamento = "";
	$ds_forma_de_pagamento = "";
	$vl_contrib_contratada = "";
	$dt_inicio_opcao       = "";
	$nome_representante    = "";
	$cpf_representante     = "";
	$email_representante   = "";
	$celular_representante = "";
	$agencia_representante = "";
	$conta_representante   = "";
	$cd_emp_representante  = "";	
	$cd_re_representante   = "";	
	$cd_seq_representante  = "";	
	
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, APP_SRV."/srvautoatendimento/index.php/get_forma_pagamento");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,"id_app=".$_APP_ID."&re_cripto=".$_SESSION['RE_CRIPTO']);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$_RETORNO = curl_exec($ch);
	curl_close ($ch);

	#print_r($_RETORNO); echo "<HR>";
	
	$FL_RETORNO = TRUE;
	$_RETORNO = json_decode($_RETORNO, TRUE);
	if (!(json_last_error() === JSON_ERROR_NONE))
	{
		switch (json_last_error()) 
		{
			case JSON_ERROR_NONE:
				$FL_RETORNO = TRUE;
			break;
				default:
				$FL_RETORNO = FALSE;
			break;
		}
	}
	#echo "X".$FL_RETORNO; echo "<HR>";
	#echo "<PRE>"; print_r($_RETORNO);  echo "<HR>";
	#exit;

	if($FL_RETORNO)
	{
		#echo $_RETORNO['error']['status'];echo "<HR>";
		if(intval($_RETORNO['error']['status']) == 0)
		{
			$tp_forma_de_pagamento = $_RETORNO['result']['forma_pagamento']['forma_pagamento'];
			$ds_forma_de_pagamento = $_RETORNO['result']['forma_pagamento']['forma_pagamento']." - ".$_RETORNO['result']['forma_pagamento']['desc_forma_pagamento'];
			$vl_contrib_contratada = $_RETORNO['result']['forma_pagamento']['valor'];
			$dt_inicio_opcao = $_RETORNO['result']['forma_pagamento']['dt_inicio_opcao'];
			$nome_representante = $_RETORNO['result']['forma_pagamento']['nome_representante'];
			$cpf_representante = $_RETORNO['result']['forma_pagamento']['cpf_representante'];
			$cd_emp_representante = $_RETORNO['result']['forma_pagamento']['cd_emp_representante'];
			$cd_re_representante  = $_RETORNO['result']['forma_pagamento']['cd_re_representante'];
			$cd_seq_representante = $_RETORNO['result']['forma_pagamento']['cd_seq_representante'];
		}
	}

	if(intval($cpf_representante) > 0)
	{
		$qr_sql = "
					SELECT funcoes.format_cpf(".intval($cpf_representante)."::bigint) AS cpf
		          ";
		$ob_resul = pg_query($db, $qr_sql);
		$ar_reg = pg_fetch_array($ob_resul);	
		$cpf_representante = $ar_reg['cpf'];
	}
	
	if(intval($cd_re_representante) > 0)
	{
		$qr_sql = "
					SELECT p.nome,
					       funcoes.format_cpf(p.cpf_mf) AS cpf,
						   (p.ddd_celular::TEXT || p.celular::TEXT) AS celular,
				           LOWER(COALESCE(COALESCE(p.email,p.email_profissional),'')) AS email,
						   CASE WHEN COALESCE(p.cd_instituicao,0) = 41 THEN p.cd_agencia ELSE NULL END AS cd_agencia,
						   CASE WHEN COALESCE(p.cd_instituicao,0) = 41 THEN p.conta_folha ELSE NULL END AS nr_conta
					  FROM public.participantes p
					 WHERE p.dt_obito IS NULL
					   AND p.cd_plano > 0
					   AND p.cd_empresa            = ".(trim($cd_emp_representante) == "" ? "-1" : intval($cd_emp_representante))."
					   AND p.cd_registro_empregado = ".(trim($cd_re_representante) == ""  ? "-1" : intval($cd_re_representante))."
					   AND p.seq_dependencia       = ".(trim($cd_seq_representante) == "" ? "-1" : intval($cd_seq_representante))."
		          ";
		$ob_resul = pg_query($db, $qr_sql);
		
		
		$ar_representante = pg_fetch_array($ob_resul);
		$cpf_representante     = $ar_representante['cpf'];
		$email_representante   = $ar_representante['email'];
		$celular_representante = $ar_representante['celular'];
		$agencia_representante = $ar_representante['cd_agencia'];
		$conta_representante   = $ar_representante['nr_conta'];
	}
	
	$fl_terceiro = TRUE;
	if($cpf_representante == $_SESSION['CPF'])
	{
		$fl_terceiro = FALSE;
		$tp_forma_de_pagamento = "";
		$nome_representante    = "";
		$cpf_representante     = "";
		$email_representante   = "";
		$celular_representante = "";	
	}
	
	/*
	$conteudo = str_replace('{FL_BCO}', ((in_array($tp_forma_de_pagamento, array("FLT","BDL","FOL"))) ? '
									<option value="BCO" selected>Debito em conta (Somente Banrisul)</option>
									<option value="BDL">Boleto</option>	
	' : '<option value="BDL" selected>Boleto</option>'), $conteudo);	
	*/
	
$conteudo = str_replace('{FL_BCO}', '
									<option value="BCO" selected>Debito em conta (Somente Banrisul)</option>
									<option value="BDL">Boleto</option>	
	', $conteudo);	
	
	$conteudo = str_replace('{FL_REPRESENTANTE}', ((in_array($tp_forma_de_pagamento, array("FLT","BCO","FOL"))) ? "" : "display:none;"), $conteudo);		
	$conteudo = str_replace('{DS_FORMA_DE_PAGAMENTO}', utf8_decode($ds_forma_de_pagamento), $conteudo);		
	$conteudo = str_replace('{VL_CONTRIB_CONTRATADA}', $vl_contrib_contratada, $conteudo);		
	$conteudo = str_replace('{DT_INICIO_OPCAO}', $dt_inicio_opcao, $conteudo);		
	$conteudo = str_replace('{FL_REPRESENTANTE_PARTICIPANTE}', ($fl_terceiro == TRUE ? "" : "selected"), $conteudo);		
	$conteudo = str_replace('{FL_REPRESENTANTE_TERCEIRO}', ($fl_terceiro == FALSE ? "" : "selected"), $conteudo);		
	$conteudo = str_replace('{NOME_REPRESENTANTE}', $nome_representante, $conteudo);		
	$conteudo = str_replace('{CPF_REPRESENTANTE}', $cpf_representante, $conteudo);		
	$conteudo = str_replace('{EMAIL_REPRESENTANTE}', $email_representante, $conteudo);		
	$conteudo = str_replace('{CELULAR_REPRESENTANTE}', $celular_representante, $conteudo);		
	$conteudo = str_replace('{CONTA_REPRESENTANTE}', $conta_representante, $conteudo);		

	#### AGENCIAS BANRISUL ####
	$qr_sql = "
				SELECT cd_agencia::INTEGER AS cd_agencia, razao_social_nome
				  FROM instituicao_financeiras
				 WHERE cd_instituicao = 41 -- SOMENTE BANRISUL
				   AND status <> 'I' 
				   AND cd_agencia <> '0'
				ORDER BY cd_agencia::INTEGER 
		      ";
	$ob_resul = pg_query($db, $qr_sql);
	$agencia = "";
	while ($ar_reg = pg_fetch_array($ob_resul)) 
	{
		$agencia.= "<option value='".$ar_reg['cd_agencia']."' ".(intval($ar_reg['cd_agencia']) == intval($agencia_representante) ? "selected" : "").">".$ar_reg['cd_agencia'].' - '.$ar_reg['razao_social_nome']."</option>";

	}
	$conteudo = str_replace('{OPTION_AGENCIA}', $agencia, $conteudo);	
	$conteudo = str_replace('{NOME_PARTICIPANTE}', $_SESSION['NOME'], $conteudo);
	
	
	#### VERIFICA SE O PROCESSO DE ASSINATURA DE DEBITO EM CONTA EM ANDAMENTO ####
	$qr_select = "
					SELECT CASE WHEN (SELECT COUNT(*)
									    FROM clicksign.contrato_digital cd
									   WHERE cd.cd_doc                = 293
									     AND cd.cd_empresa            = p.cd_empresa
									     AND cd.cd_registro_empregado = p.cd_registro_empregado 
									     AND cd.seq_dependencia       = p.seq_dependencia
									     AND cd.dt_limite             >= CURRENT_TIMESTAMP 
									     AND cd.dt_concluido          IS NULL
									     AND cd.dt_cancelado          IS NULL
									     AND cd.dt_finalizado         IS NULL) = 0 
								THEN 'S'
								ELSE 'N'
						   END AS fl_autorizacao,
						   CASE WHEN (SELECT COUNT(*)
									    FROM clicksign.contrato_digital cd
										JOIN clicksign.contrato_digital_assinatura cda
										  ON cda.cd_contrato_digital = cd.cd_contrato_digital
										 AND cda.tp_assinatura       IN ('P','T1')
									   WHERE cd.cd_doc                = 293
									     AND cd.cd_empresa            = p.cd_empresa
									     AND cd.cd_registro_empregado = p.cd_registro_empregado 
									     AND cd.seq_dependencia       = p.seq_dependencia
										 AND cda.dt_assinatura        IS NULL
									     AND cd.dt_concluido          IS NULL
									     AND cd.dt_cancelado          IS NULL
									     AND cd.dt_finalizado         IS NULL										 
									     AND cd.dt_limite             >= CURRENT_TIMESTAMP) > 0 
								THEN 'N'
								ELSE 'S'
						   END AS fl_autorizacao_assinada						   
					  FROM public.participantes p
					 WHERE p.cd_empresa            = ".$_SESSION['EMP']." 
					   AND p.cd_registro_empregado = ".$_SESSION['RE']." 
					   AND p.seq_dependencia       = ".$_SESSION['SEQ']."
				 ";
	$ob_res = pg_query($db, $qr_select);
	$ar_reg = pg_fetch_array($ob_res);
	$_FL_AUTORIZACAO          = $ar_reg['fl_autorizacao'];
	$_FL_AUTORIZACAO_ASSINADO = $ar_reg['fl_autorizacao_assinada'];	
	
	$_DS_ASSINAR = "";
	if($_FL_AUTORIZACAO_ASSINADO == "N")
	{
		$qr_select = "
						SELECT cd.cd_contrato_digital,
							   cd.id_doc,
							   TO_CHAR(cd.dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_solicitacao,
							   TO_CHAR(cd.dt_limite,'DD/MM/YYYY HH24:MI:SS') AS dt_limite,
							   TO_CHAR(cd.dt_concluido,'DD/MM/YYYY HH24:MI:SS') AS dt_concluido,
							   TO_CHAR(cda.dt_assinatura,'DD/MM/YYYY HH24:MI:SS') AS dt_assinatura,
							   TO_CHAR(cd.dt_cancelado,'DD/MM/YYYY HH24:MI:SS') AS dt_cancelado,
							   TO_CHAR(cd.dt_finalizado,'DD/MM/YYYY HH24:MI:SS') AS dt_finalizado,
							   cda.ds_url_assinatura
						  FROM clicksign.contrato_digital cd
						  JOIN clicksign.contrato_digital_assinatura cda
							ON cda.cd_contrato_digital = cd.cd_contrato_digital
						   AND cda.tp_assinatura       IN ('P','T1')
						 WHERE cd.cd_doc                = 293
						   AND cd.cd_empresa            = ".$_SESSION['EMP']." 
						   AND cd.cd_registro_empregado = ".$_SESSION['RE']." 
						   AND cd.seq_dependencia       = ".$_SESSION['SEQ']."
						   AND cd.dt_concluido          IS NULL
						   AND cd.dt_cancelado          IS NULL
						   AND cd.dt_finalizado         IS NULL
						   AND (SELECT COUNT(*)
							      FROM clicksign.contrato_digital cd1
							      JOIN clicksign.contrato_digital_assinatura cda
							    	ON cda.cd_contrato_digital = cd.cd_contrato_digital
							       AND cda.tp_assinatura       IN ('P','T1')										
							     WHERE cd1.cd_doc                = 293
							       AND cd1.cd_empresa            = cd.cd_empresa
							       AND cd1.cd_registro_empregado = cd.cd_registro_empregado 
							       AND cd1.seq_dependencia       = cd.seq_dependencia
							       AND cda.dt_assinatura        IS NULL
							       AND cd1.dt_concluido          IS NULL
							       AND cd1.dt_cancelado          IS NULL
							       AND cd1.dt_finalizado         IS NULL										 
							       AND cd1.dt_limite             >= CURRENT_TIMESTAMP) > 0
					 ";
		$ob_res = pg_query($db, $qr_select);
		$ar_reg = pg_fetch_array($ob_res);
		
		$_DS_ASSINAR = '
						<table>
							<tr>
								<td>Você solicitou a alteração da forma de pagamento, porém sua solicitação está pendente da sua assinatura ou da assinatura do correntista, para dar andamento na sua autorização para débito em conta, clique no botão [ASSINAR] abaixo.</td>
							</tr>
							<tr>
								<td>
									<BR>
									<input type="button" value="ASSINAR" style="width: 200px; font-weight: bold;" onclick="document.location.href=\''.$ar_reg['ds_url_assinatura'].'\'">
								</td>
							</tr>					
						</table>
			           ';
	}
	elseif($_FL_AUTORIZACAO == "N")
	{
		$_DS_ASSINAR = '
						<form method="post" action="">
						<table>
							<tr>
								<td>Sua solicitação está em andamento, aguarde nossa confirmação (o prazo é de até 5 dias úteis após a sua assinatura).</td>
							</tr>
						</table>
						</form>
			           ';		
	}	
	

	$conteudo = str_replace('{FL_ASSINAR}', ((($_FL_AUTORIZACAO_ASSINADO == "N") OR ($_FL_AUTORIZACAO == "N")) ? "S" : "N"), $conteudo);
	$conteudo = str_replace('{DS_ASSINAR}', utf8_decode($_DS_ASSINAR), $conteudo);
	
	$conteudo = str_replace('{DASH_MENU}', $menu, $conteudo);


	$tpl->assign('conteudo',$conteudo);
	$tpl->printToScreen();
	
	
	
?>