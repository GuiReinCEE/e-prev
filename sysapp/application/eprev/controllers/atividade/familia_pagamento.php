<?php
	if (($_SERVER['SERVER_ADDR'] == '10.63.255.5') and ($_SERVER['HTTPS'] != 'on'))
	{
		#### REDIRECIONA PARA HTTPS ####
		$ir_para_https = $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
		$ir_para_https = str_replace('http://','',$ir_para_https);
		$ir_para_https = str_replace('https://','',$ir_para_https);
		$ir_para_https = 'https://'.$ir_para_https;
		header("location: ".$ir_para_https); 
		exit;
	}
	
    $ar_meses = array("Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro");	
	
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	$tpl = new TemplatePower('tpl/tpl_padrao.html');
	$tpl->prepare();	
	$tpl->newBlock('titulo');
	$tpl->assign('titulo',"Área do Participante");

	$_REQUEST['cd_secao']  = 'SERV';
	$_REQUEST['cd_artigo'] = 136;
	include_once('monta_menu.php');
	$tpl->newBlock('conteudo');	
	
	
	#### VERIFICA SE RE VEIO NO LINK ####
	if(trim($_REQUEST['re']) == "")
	{
		$conteudo = '
				<body style="margin: 0px; text-align:center; padding: 0px;">
					<table width="695" border="0" align="center">
						<tr>
							<td valign="top" style="margin:0px; width: 695px; height: 490px;padding-top: 0px; padding-left: 50px; padding-right: 50px; padding-bottom: 0px;">
								<h1 style="margin-top: 100px; font-family: Calibri, Arial; font-size: 22pt; width:100%; text-align:center; color:red">
									ERRO (SP1) - Participante não encontrado.
									<BR><BR>
									Tente novamente clicando no link que você recebeu ou entre em contato através do 0800512596.
								</h1>
							</td>
						</tr>
					</table>
				</body>		
				<BR>
				<BR>
			 ';
		$tpl->assign('conteudo',$conteudo);
		$tpl->printToScreen();			 
		exit;
	}	

	#### VERIFICA SE TEM RISCO ####
	$qr_sql = "
				SELECT COUNT(*) AS fl_risco 
				  FROM boleto.boleto_instituidor('".$_REQUEST['re']."') x
				 WHERE COALESCE(x.fl_risco,'N') = 'S';
              ";         
	$ob_resul = pg_query($db,$qr_sql);
	$ar_risco = pg_fetch_array($ob_resul);	
	if(intval($ar_risco['fl_risco']) > 0)
	{
		echo '<META HTTP-EQUIV="Refresh" CONTENT="0;URL=familia_pagamento_risco.php?re='.$_REQUEST['re'].'">';
		exit;
	}


	#### BUSCA PARTICIPANTE ####
	$qr_sql = "
				SELECT p.*
 				  FROM public.participantes p
				 WHERE funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) = '".$_REQUEST['re']."'
				   AND p.cd_plano = 9
              "; 
	
	#echo "<PRE>$qr_sql</PRE>"; exit;
	$ob_resul = pg_query($db,$qr_sql);
	$ar_participante = pg_fetch_array($ob_resul);
	if(pg_num_rows($ob_resul) == 0)
	{
		$conteudo = '
				<body style="margin: 0px; text-align:center; padding: 0px;">
					<table width="695" border="0" align="center">
						<tr>
							<td valign="top" style="margin:0px; width: 695px; height: 490px;padding-top: 0px; padding-left: 50px; padding-right: 50px; padding-bottom: 0px;">
								<h1 style="margin-top: 100px; font-family: Calibri, Arial; font-size: 22pt; width:100%; text-align:center; color:red">
									ERRO (SP3) - Participante não encontrado.
									<BR><BR>
									Tente novamente clicando no link que você recebeu ou entre em contato através do 0800512596
								</h1>
							</td>
						</tr>
					</table>
				</body>		
				<BR>
				<BR>
			 ';
		$tpl->assign('conteudo',$conteudo);
		$tpl->printToScreen();			 
		exit;
	}	
	
	#### VERIFICA SE INSCRIÇÃO FOI CANCELADA ####
	$qr_sql = "
				SELECT COUNT(*) AS fl_inscricao
 				  FROM public.titulares t
				 WHERE funcoes.cripto_re(t.cd_empresa, t.cd_registro_empregado, t.seq_dependencia) = '".$_REQUEST['re']."'
				   AND t.dt_cancela_inscricao IS NOT NULL
              ";         
	$ob_resul = pg_query($db,$qr_sql);
	$ar_inscricao = pg_fetch_array($ob_resul);	
	if(intval($ar_inscricao['fl_inscricao']) > 0)
	{
		$conteudo = '
				<body style="margin: 0px; text-align:center; padding: 0px;">
					<table width="695" border="0" align="center">
						<tr>
							<td valign="top" style="margin:0px; width: 695px; height: 490px;padding-top: 0px; padding-left: 50px; padding-right: 50px; padding-bottom: 0px;">
								<h1 style="margin-top: 100px; font-family: Calibri, Arial; font-size: 22pt; width:100%; text-align:center; color:red">
									Inscrição cancelada.
									<BR><BR>
									Tente novamente clicando no link que você recebeu ou entre em contato através do 0800512596.
								</h1>
							</td>
						</tr>
					</table>
				</body>		
				<BR>
				<BR>
			 ';
		$tpl->assign('conteudo',$conteudo);
		$tpl->printToScreen();			 
		exit;
	}
	
	#### VERIFICA SE FOI DESLIGADO ####
	$qr_sql = "
				SELECT COUNT(*) AS fl_desligado
 				  FROM public.titulares t
				 WHERE funcoes.cripto_re(t.cd_empresa, t.cd_registro_empregado, t.seq_dependencia) = '".$_REQUEST['re']."'
				   AND t.dt_desligamento_eletro IS NOT NULL
              ";         
	$ob_resul = pg_query($db,$qr_sql);
	$ar_inscricao = pg_fetch_array($ob_resul);	
	if(intval($ar_inscricao['fl_desligado']) > 0)
	{
		$conteudo = '
				<body style="margin: 0px; text-align:center; padding: 0px;">
					<table width="695" border="0" align="center">
						<tr>
							<td valign="top" style="margin:0px; width: 695px; height: 490px;padding-top: 0px; padding-left: 50px; padding-right: 50px; padding-bottom: 0px;">
								<h1 style="margin-top: 100px; font-family: Calibri, Arial; font-size: 22pt; width:100%; text-align:center; color:red">
									Participante desligado.
									<BR><BR>
									Entre em contato através do 0800512596.
								</h1>
							</td>
						</tr>
					</table>
				</body>		
				<BR>
				<BR>
			 ';
		$tpl->assign('conteudo',$conteudo);
		$tpl->printToScreen();			 
		exit;
	}	

	$qr_sql = "
				SELECT ano_competencia, 
					   mes_competencia,
                       ds_boleto,
					   nr_competencia,
					   id_suspensao_presumida,
					   funcoes.cripto_mes_ano(mes_competencia,ano_competencia) AS cripto_mes_ano 
				  FROM boleto.boleto_instituidor('".$_REQUEST['re']."')
                 ORDER BY nr_ordem, ano_competencia DESC, mes_competencia DESC
	          ";
	#echo "<PRE>$qr_sql</PRE>"; exit;
	$ob_resul = pg_query($db,$qr_sql);
	
	if(pg_num_rows($ob_resul) == 0)
	{
		#### VERIFICA SE PRIMEIRO PAGAMENTO ####
		$qr_sql = " 
					SELECT CASE WHEN COUNT(*) = 0
					            THEN 'S' --PRIMEIRO PAGAMENTO
								ELSE 'N'
						   END AS fl_primeiro_pagamento
					  FROM public.titulares_planos tp 
					  JOIN public.participantes p
					    ON p.cd_empresa            = tp.cd_empresa
					   AND p.cd_registro_empregado = tp.cd_registro_empregado
					   AND p.seq_dependencia       = tp.seq_dependencia
					 WHERE funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) = '".$_REQUEST['re']."'
					   AND tp.dt_ingresso_plano = (SELECT max(tp1.dt_ingresso_plano)
								                     FROM public.titulares_planos tp1 
								                    WHERE tp1.cd_empresa            = tp.cd_empresa 
								                      AND tp1.cd_registro_empregado = tp.cd_registro_empregado 
								                      AND tp1.seq_dependencia       = tp.seq_dependencia)
					   AND tp.dt_ingresso_plano IS NOT NULL	
				  ";		
		$ob_resul = pg_query($db,$qr_sql);
		$ar_reg   = pg_fetch_array($ob_resul);
		if($ar_reg['fl_primeiro_pagamento'] == 'S')
		{
			#### PRIMEIRO PAGAMENTO ####
			echo '<META HTTP-EQUIV="Refresh" CONTENT="0;URL=familia_pagamento_valor.php?re='.$_REQUEST['re'].'&comp='.md5('000000').'">';
			exit;
		}
		else
		{
			#### PAGAMENTO ADICIONAL ####
			echo '<META HTTP-EQUIV="Refresh" CONTENT="0;URL=familia_pagamento_valor.php?re='.$_REQUEST['re'].'&comp='.md5('999999').'">';
			exit;
		}
	}
	else
	{
		#### PAGAMENTO MENSAL ####
		if(pg_num_rows($ob_resul) == 1)
		{
			#### SOMENTE 1 BOLETO ABERTO ####
			$ar_reg = pg_fetch_array($ob_resul);
			echo '<META HTTP-EQUIV="Refresh" CONTENT="0;URL=familia_pagamento_valor.php?re='.$_REQUEST['re'].'&comp='.$ar_reg['cripto_mes_ano'].'">';
			exit;
		}
		else
		{
			#### MAIS DE 1 BOLETO ABERTO ####
			$ds_arq   = "tpl/tpl_familia_pagamento.html";
			$ob_arq   = fopen($ds_arq, 'r');
			$conteudo = fread($ob_arq, filesize($ds_arq));
			fclose($ob_arq);	
			$conteudo = str_replace("{cd_empresa}", $ar_participante['cd_empresa'], $conteudo);
			$conteudo = str_replace("{cd_registro_empregado}", $ar_participante['cd_registro_empregado'], $conteudo);
			$conteudo = str_replace("{seq_dependencia}", $ar_participante['seq_dependencia'], $conteudo);
			$conteudo = str_replace("{nome}", $ar_participante['nome'], $conteudo);

			#### CONTRIBUICAO NORMAL (MES, ATRASADA E APORTE) #####
			$qr_sql = "
						SELECT ano_competencia, 
							   mes_competencia,
							   ds_boleto,
							   nr_competencia,
							   id_suspensao_presumida,
							   funcoes.cripto_mes_ano(mes_competencia,ano_competencia) AS cripto_mes_ano 
						  FROM boleto.boleto_instituidor('".$_REQUEST['re']."')
					     WHERE COALESCE(id_suspensao_presumida,'') NOT IN ('S')
					     ORDER BY nr_ordem, ano_competencia DESC, mes_competencia DESC
					  ";
			#echo "<PRE>$qr_sql</PRE>"; exit;
			$ob_resul_normal = pg_query($db,$qr_sql);		
			
			$comp_modelo = "<tr class='{classe}'>
								<td>
									<b>{descricao_boleto}</b>
								</td>
								<td align='center'>
									<a href='familia_pagamento_valor.php?re=".$_REQUEST['re']."&comp={comp}'>[Clique aqui para emitir o boleto]</a>
								</td>
							</tr>";		
			$competencia = "";
			$nr_conta = 0;
			while($ar_reg = pg_fetch_array($ob_resul_normal))
			{
				$competencia.= $comp_modelo;
				
				$descricao_boleto = $ar_reg['ds_boleto']." de ".$ar_meses[$ar_reg['mes_competencia'] - 1]." de ".$ar_reg['ano_competencia'];
				if($ar_reg['id_suspensao_presumida'] == "APORTE")
				{
					$descricao_boleto = $ar_reg['ds_boleto']." (".$ar_reg['nr_competencia'].")";
				}
				
				$competencia = str_replace("{classe}", (($nr_conta % 2) ? 'sort-impar' : 'sort-par') , $competencia);
				$competencia = str_replace("{comp}", $ar_reg['cripto_mes_ano'], $competencia);
				$competencia = str_replace("{descricao_boleto}", $descricao_boleto, $competencia );
				
				$nr_conta++;
			}
			
			$conteudo = str_replace("{competencia}", $competencia, $conteudo);
			
			
			#### CONTRIBUICAO SUSPENSA PRESUMIDA #####
			$qr_sql = "
						SELECT ano_competencia, 
							   mes_competencia,
							   ds_boleto,
							   nr_competencia,
							   id_suspensao_presumida,
							   funcoes.cripto_mes_ano(mes_competencia,ano_competencia) AS cripto_mes_ano 
						  FROM boleto.boleto_instituidor('".$_REQUEST['re']."')
						 WHERE id_suspensao_presumida IN ('S')
						 ORDER BY nr_ordem, ano_competencia DESC, mes_competencia DESC
						 ".(($_REQUEST['re'] == "915df08a71bf3a4900120a29347624b1") ? "" : "LIMIT 3")."
					  ";
			#echo "<PRE>$qr_sql</PRE>"; exit;
			$ob_resul_suspensa = pg_query($db,$qr_sql);		

			$conteudo = str_replace("{FL_CONTRIB_SUSPENSA}", ((pg_num_rows($ob_resul_suspensa) > 0) ? "" : "display:none;"), $conteudo);
			
			$comp_modelo = "<tr class='{classe}'>
								<td>
									<b>{descricao_boleto}</b>
								</td>
								<td align='center'>
									<a href='familia_pagamento_valor.php?re=".$_REQUEST['re']."&comp={comp}'>[Clique aqui para emitir o boleto]</a>
								</td>
							</tr>";		
			$competencia = "";
			$nr_conta = 0;
			while($ar_reg = pg_fetch_array($ob_resul_suspensa))
			{
				$competencia.= $comp_modelo;
				
				$descricao_boleto = "Suspensa de ".$ar_meses[$ar_reg['mes_competencia'] - 1]." de ".$ar_reg['ano_competencia'];
				
				$competencia = str_replace("{classe}", (($nr_conta % 2) ? 'sort-impar' : 'sort-par') , $competencia);
				$competencia = str_replace("{comp}", $ar_reg['cripto_mes_ano'], $competencia);
				$competencia = str_replace("{descricao_boleto}", $descricao_boleto, $competencia );
				
				$nr_conta++;
			}
			
			$conteudo = str_replace("{competencia_suspensa}", $competencia, $conteudo);			
			
			$tpl->assign('conteudo',$conteudo);
			$tpl->printToScreen();	
			exit;			
		}
	}
?>