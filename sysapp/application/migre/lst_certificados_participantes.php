<?php
	require('inc/conexao.php');
	require('inc/sessao.php');
	header("Location: ".site_url("ecrm/certificado_participante"));

exit;

	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	$tpl = new TemplatePower('tpl/tpl_lst_certificados_participantes.html');
//--------------------------------------------------------------
	$tpl->prepare();
	$tpl->assign('n', $n);
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
	

	$tpl->assign('urlCertificadoPDF', site_url("ecrm/certificado_participante/certificadoPDF"));
	$tpl->assign('emp', $_REQUEST['patrocinadora']);
	$tpl->assign('pl', $_REQUEST['plano']);
	$tpl->assign('dt_inicial', $_REQUEST['dt_inicial']);
	$tpl->assign('dt_final', $_REQUEST['dt_final']);
	
	$fl_imprimir = "disabled";
	if((trim($_REQUEST['patrocinadora']) != "") 
	  and (trim($_REQUEST['plano']) != "") 
	  and (trim($_REQUEST['dt_inicial']) != "") 
	  and (trim($_REQUEST['dt_final']) != ""))
	{
		$fl_imprimir = "";
	}
	$tpl->assign('fl_imprimir', $fl_imprimir);

	#### LISTA DE EMPRESAS ####
	$qr_sql = "
				SELECT cd_empresa, sigla AS nome_reduz  
				  FROM public.patrocinadoras 
				 ORDER BY cd_empresa
		      ";
	$ob_resul = pg_query($db, $qr_sql);
	$tpl->newBlock('patrocinadora');
	$tpl->assign('cd_empresa', "");
	$tpl->assign('nome_empresa', 'Selecione');	
	while ($ar_reg = pg_fetch_array($ob_resul)) 
	{
		$tpl->newBlock('patrocinadora');
		$tpl->assign('cd_empresa', $ar_reg['cd_empresa']);
		$tpl->assign('nome_empresa', $ar_reg['nome_reduz']);
		$tpl->assign('chk_empresa', ($ar_reg['cd_empresa'] == trim($_REQUEST['patrocinadora']) ? ' selected' : ''));
	}
	
	#### LISTA DE PLANOS ####
	$qr_sql = "
				SELECT p.cd_plano, 
				       p.descricao  
				  FROM public.planos p
				  ".(trim($_REQUEST['patrocinadora']) != "" ? "JOIN public.planos_patrocinadoras pp ON pp.cd_plano = p.cd_plano AND pp.cd_empresa = ".intval(trim($_REQUEST['patrocinadora'])) : "")."
				 WHERE p.cd_plano > 0
				 ORDER BY p.descricao
			  ";
	$ob_resul = pg_query($db, $qr_sql);
	$tpl->newBlock('plano');
	$tpl->assign('cd_plano', "");
	$tpl->assign('nome_plano', 'Selecione');
	while ($reg = pg_fetch_array($ob_resul)) 
	{
		$tpl->newBlock('plano');
		$tpl->assign('cd_plano', $reg['cd_plano']);
		$tpl->assign('nome_plano', $reg['descricao']);
		$tpl->assign('chk_plano', ($reg['cd_plano'] == trim($_REQUEST['plano']) ? ' selected' : ''));
	}

	#### LISTA PARTICIPANTES ####
	$tpl->newBlock('lst_participantes');
	$tpl->assign('mun', $mun);
	$qr_sql = " 
				SELECT p.cd_empresa, 
				       p.cd_registro_empregado, 
					   p.seq_dependencia, 
					   p.nome, 
					   TO_CHAR(t.dt_ingresso_eletro,'DD/MM/YYYY') AS dt_ingresso 
	              FROM public.participantes p
				  JOIN public.titulares t 
				    ON t.cd_empresa            = p.cd_empresa 
	               AND t.cd_registro_empregado = p.cd_registro_empregado 
	               AND t.seq_dependencia       = p.seq_dependencia 
	             WHERE p.dt_envio_certificado IS NULL 
				   AND p.dt_obito             IS NULL  
				   AND CAST(t.dt_ingresso_eletro AS DATE) BETWEEN ".(trim($_REQUEST['dt_inicial']) != "" ? "TO_DATE('".trim($_REQUEST['dt_inicial'])."','DD/MM/YYYY')" : "CURRENT_DATE")." AND ".(trim($_REQUEST['dt_final']) != "" ? "TO_DATE('".trim($_REQUEST['dt_final'])."','DD/MM/YYYY')" : "CURRENT_DATE")."
				".(trim($_REQUEST['patrocinadora']) != "" ? "AND p.cd_empresa = ".intval(trim($_REQUEST['patrocinadora'])) : "")."
				".(trim($_REQUEST['plano']) != "" ? "AND p.cd_plano = ".intval(trim($_REQUEST['plano'])) : "")."
				  ORDER BY p.nome
			  ";
	#echo "<PRE>$qr_sql</PRE>"; #exit;
	
	$ob_resul = pg_query($db, $qr_sql);
	$cont = 0;
	while ($ar_reg = pg_fetch_array($ob_resul)) 
	{
		$tpl->newBlock('participante');
		$cont = $cont + 1;
		if (($cont % 2) <> 0) {
			$tpl->assign('cor_fundo', $v_cor_fundo1);
		}
		else {
			$tpl->assign('cor_fundo', $v_cor_fundo2);
		}
		$tpl->assign('cd_empresa', $ar_reg['cd_empresa']);
		$tpl->assign('cd_registro_empregado', $ar_reg['cd_registro_empregado']);
		$tpl->assign('seq_dependencia', $ar_reg['seq_dependencia']);
		$tpl->assign('nome', $ar_reg['nome']);
		$tpl->assign('dt_ingresso', $ar_reg['dt_ingresso']);	
		$total = $total + 1;
	}
	$tpl->newBlock('total');
	$tpl->assign('total', $cont);
	$tpl->printToScreen();
?>