<?php
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	
    $tpl = new TemplatePower('tpl/tpl_edt_chaves_movimento.html');
	$tpl->prepare();
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
	
	
	$tpl->assign('cd_chave_movimento', $_REQUEST['cd_chave_movimento']);
	
	if(trim($_REQUEST['cd_chave_movimento']) == "")
	{
		echo "<script> window.close(); </script>";
	}
	else 
	{
		$qr_select = "
						SELECT cm.cd_chave_movimento,
						       cm.cd_chave,
						       cm.ds_nome,
							   cm.ds_nome_retorno,
							   TO_CHAR(cm.dt_saida,'DD/MM/YYYY') AS dt_saida,
							   TO_CHAR(cm.dt_saida,'HH24:MI') AS hr_saida,
							   TO_CHAR(cm.dt_retorno,'DD/MM/YYYY') AS dt_retorno,
							   TO_CHAR(cm.dt_retorno,'HH24:MI') AS hr_retorno,						   
							   c.cd_sala,
							   c.ds_chave,
							   (CASE WHEN cm.dt_retorno IS NULL
							         THEN 'N'
								 	 ELSE 'S'
							   END) AS fl_retorno						   
						  FROM projetos.chaves c,
						       projetos.chaves_movimento cm
						 WHERE cm.cd_chave_movimento = ".$_REQUEST['cd_chave_movimento']."
				     ";
		$ob_result = pg_query($db, $qr_select);	
		$ar_reg = pg_fetch_array($ob_result);
		
		$tpl->assign('ds_nome',         $ar_reg['ds_nome']);
		$tpl->assign('ds_nome_retorno', $ar_reg['ds_nome_retorno']);
		$tpl->assign('ds_chave',        $ar_reg['cd_sala']." - ".$ar_reg['ds_chave']);
		
		$tpl->assign('dt_saida',   $ar_reg['dt_saida']);
		$tpl->assign('hr_saida',   $ar_reg['hr_saida']);		
		$tpl->assign('dt_retorno', $ar_reg['dt_retorno']);
		$tpl->assign('hr_retorno', $ar_reg['hr_retorno']);
		
		#### DEFINE PERMISSAO PARA LIMPAR DATA DE RETORNO ####
		$tpl->assign('fl_retorno', $ar_reg['fl_retorno']);		
	}
	
	if(trim($_REQUEST['fl_gravado']) == "OK")
	{
		echo "<script> 
						try { opener.location.href = opener.location.href; } catch(e){ }
						window.close();
			  </script>";
	}
	
	$tpl->printToScreen();
	pg_close($db);
?>