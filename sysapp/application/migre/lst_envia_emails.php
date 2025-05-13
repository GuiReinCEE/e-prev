<?PHP
	include_once("inc/sessao.php");
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	$tpl = new TemplatePower('tpl/tpl_lst_emails.htm');
	$tpl->assignInclude('menu', 'inc/menu_noticias.htm');
	$tpl->prepare();
   
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
//	if ($ofset < 50) { $ofset = 50; }
   	if (($D <> 'GAP') and ($D <> 'GI') and ($D <> 'GF') and ($D <> 'GRI')) {
   		header('location: acesso_restrito.php?IMG=banner_emails_enviados');
	}
	$sql = " 
			SELECT ee.cd_email, 
			       TO_CHAR(ee.dt_envio, 'DD/MM/YYYY') AS dt_envio, 
			       ee.de, 
			       ee.para, 
			       ee.assunto, 
			       TO_CHAR(ee.dt_email_enviado, 'DD/MM/YYYY') AS dt_email_enviado,
			       ee.cd_empresa,
			       ee.cd_registro_empregado,
			       ee.seq_dependencia,
				   ee.fl_retornou
			  FROM projetos.envia_emails ee

           ";
	
	$tpl->assign("aba_style_visibility",           ($divulg != '')?"visible":"hidden");
	$tpl->assign("espaco_entre_abas_e_conteudo",   ($divulg != '')?"<br><br><br>":"");
	
	if ($divulg != '') 
	{
   		$sql2 = " WHERE ee.cd_divulgacao = ".$divulg;
   		$tpl->assign("cd_divulgacao", $divulg);

		/*
   		#### PÚBLICO ALVO SELECIONADO ####
		$qr_sql = "
					SELECT COUNT(*) AS qt_email
					  FROM projetos.envia_emails_seleciona
					 WHERE cd_divulgacao = " . $divulg . "
			      ";
		$ob_resul = pg_query($db, $qr_sql);
		$ar_reg_env = pg_fetch_array($ob_resul);
		$tpl->assign('qt_email_selecionado', $ar_reg_env['qt_email']);
		*/
	}
	elseif ($e == 'SNG') 
	{
   		$sql2 =  " WHERE (ee.de = 'Senge Previdência') or (ee.de like 'Senge Previdência%') or (ee.de = 'Senge Previdencia') ";
	}
	elseif ($e == 'EMP') 
	{
   		$sql2 =  " WHERE ee.de like '%Sistema de Empréstimos%' ";
	}
	elseif ($e == 'CON') 
	{
   		$sql2 =  " WHERE ee.de like '%Atendimento Fundação CEEE%' ";
	}
	elseif ($e == 'GRI') 
	{
   		$sql2 =  " WHERE ee.div_solicitante = 'GRI' ";
	}
	
	#### EMAIL ENVIADOS ####
	if($_REQUEST['fl_retornado'] == "N")
	{
		if($sql2 != "")
		{
			$sql2.= " AND ee.fl_retornou <> 'S'";
		}
		else
		{
			$sql2.= " WHERE ee.fl_retornou <> 'S'";
		}
		$tpl->assign("css_emails_enviados", "class='abaSelecionada'");
	}	
	
	#### EMAIL RETORNADOS ####
	if($_REQUEST['fl_retornado'] == "S")
	{
		if($sql2 != "")
		{
			$sql2.= " AND ee.fl_retornou = 'S'";
		}
		else
		{
			$sql2.= " WHERE ee.fl_retornou = 'S'";
		}
		$tpl->assign("css_emails_retornados", "class='abaSelecionada'");
	}
	
	$sqlc = "
		        SELECT COUNT(*) AS num_regs 
				  FROM projetos.envia_emails ee
				       ".$sql2." 
				 LIMIT 50";
	//echo $sqlc;exit;
	
	if ($ofset > 49) {
		$sql = $sql . $sql2 . " LIMIT 50 OFFSET " . $ofset;
	}
	else {
		$sql = $sql . $sql2 . " LIMIT 50";
	}
	
	#echo "<PRE>$sqlc<PRE>";exit;
	
	$rs = pg_exec($db, $sqlc);
	$reg = pg_fetch_array($rs);
	$tpl->assign("count_emails", $reg['num_regs']);
	if (($reg['num_regs'] - $ofset) > 49) {
		$ofset = $ofset + 50;
		$tpl->assign('ofset', $ofset);
		$tpl->assign('e', $e);
	}
	if ($ofset > 100) {
		$tpl->assign('ofsetant', $ofset - 100);
	} 
	
	
	#echo "<PRE>$sql<PRE>";exit;
	$tpl->assign('divulg', $divulg);
	$rs = pg_exec($db, $sql);
	$nr_conta = 0;
	while ($r = pg_fetch_array($rs)) 
	{
		if(($nr_conta % 2) != 0)
		{
			$bg_color = '#F7F7F7';
		}
		else
		{
			$bg_color = '#FFFFFF';		
		}      	
		
		$tpl->newBlock('email');
		$tpl->assign('bg_color', $bg_color);
      	$tpl->assign('cd_email', $r['cd_email']);
      	$tpl->assign('data', $r['dt_envio']);
      	$tpl->assign('e', $e);
		$tpl->assign('de', $r['de']);
	  	$tpl->assign('para', $r['para']);
	  	$tpl->assign('assunto', $r['assunto']);
		$tpl->assign('dt_envio', $r['dt_email_enviado']);

		if(trim($r['fl_retornou']) == "S")
		{
			$tpl->assign('img_retorno', '<img src="img/exclamation.png" border="0">');
		}

		
		$nr_conta++;
   }
   $tpl->printToScreen();
   pg_close($db);      
?>
