<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	
    $tpl = new TemplatePower('tpl/tpl_lst_chaves_movimento.html');
	$tpl->prepare();
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
	
	if(trim($_REQUEST['dt_ini']) == "")
	{
		$_REQUEST['dt_ini'] = date('d/m/Y');
		$_REQUEST['dt_fim'] = date('d/m/Y');
	}
	
	$tpl->assign('dt_ini',  $_REQUEST['dt_ini']);
	$tpl->assign('dt_fim',  $_REQUEST['dt_fim']);

	$_REQUEST['dt_ini']   = (trim($_REQUEST['dt_ini'])   == "" ? "CURRENT_DATE" : "TO_DATE('".$_REQUEST['dt_ini']."','DD/MM/YYYY')");
	$_REQUEST['dt_fim']   = (trim($_REQUEST['dt_fim'])   == "" ? "CURRENT_DATE" : "TO_DATE('".$_REQUEST['dt_fim']."','DD/MM/YYYY')");
	$_REQUEST['ds_ordem'] = (trim($_REQUEST['ds_ordem']) == "" ? "ds_nome"   : $_REQUEST['ds_ordem']);
	
	$tpl->assign('ds_ordem', $_REQUEST['cm.ds_ordem']);
	
	$qr_select = "
					SELECT TO_CHAR(cm.dt_saida,'DD/MM/YYYY HH24:MI:SS') AS dt_saida_formatada,
						   UPPER(cm.ds_nome) AS ds_nome,
						   UPPER(cm.ds_nome_retorno) AS ds_nome_retorno,
						   REPLACE(cast((cm.dt_retorno-cm.dt_saida) as text),'day','dia') AS hr_tempo,
						   cm.cd_chave_movimento,
						   c.cd_chave,
						   c.ds_chave,
						   c.cd_sala
					  FROM projetos.chaves_movimento cm,
					       projetos.chaves c
			         WHERE CAST(cm.dt_saida AS DATE) BETWEEN ".$_REQUEST['dt_ini']." AND ".$_REQUEST['dt_fim']."
					   AND cm.cd_chave = c.cd_chave
				     ORDER BY ".$_REQUEST['ds_ordem']."	
				 ";
	$ob_result = pg_query($db, $qr_select);	
	$nr_conta  = 0;
	while ($ar_reg = pg_fetch_array($ob_result)) 
	{
		if(($nr_conta % 2) != 0)
		{
			$bg_color = '#F4F4F4';
		}
		else
		{
			$bg_color = '#FFFFFF';		
		}
		$nr_conta++;
		
		$tpl->newBlock('lst_movimento');		
		$tpl->assign('bg_color',              $bg_color);

		$tpl->assign('cd_chave_movimento',  $ar_reg['cd_chave_movimento']);
		$tpl->assign('ds_chave',            $ar_reg['cd_sala']." - ".$ar_reg['ds_chave']);
		$tpl->assign('ds_nome',             $ar_reg['ds_nome']);
		$tpl->assign('ds_nome_retorno',     $ar_reg['ds_nome_retorno']);
		$tpl->assign('dt_saida',            $ar_reg['dt_saida_formatada']);
		$tpl->assign('hr_tempo',            $ar_reg['hr_tempo']);
	}
	
	$tpl->printToScreen();
	pg_close($db);
?>