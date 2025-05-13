<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	require_once('inc/ajaxobject.php');
	include_once('inc/class.TemplatePower.inc.php');
	
	$tpl = new TemplatePower('tpl/tpl_senge_rel_acompanhamento_libera.html');
	$tpl->prepare();
	
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');

	$tpl->assign('n', $n);
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
	
	if (($_SESSION['D'] != 'GC') AND ($_SESSION['D'] != 'GI'))
	{
   		header('location: acesso_restrito.php?IMG=banner_prevnet');
	}
	
	$nr_conta = 2007;
	$nr_fim = date('Y');
	while ($nr_conta <= $nr_fim) 
	{
		$tpl->newBlock('lista_ano');
		$tpl->assign('nr_ano', $nr_conta);
		$nr_conta++;
	}	
	
	
	#### BUSCA LISTA ####
	$qr_sql = " 
				SELECT rap.cd_rel_acompanhamento_plano,
				       rap.nr_ano,
					   TRIM(TO_CHAR(rap.nr_mes,'00')) AS nr_mes,
					   TO_CHAR(rap.dt_libera,'DD/MM/YYYY HH24:MI:SS') AS dt_libera,
					   ucl.usuario AS ds_libera,
					   TO_CHAR(rap.dt_bloqueia,'DD/MM/YYYY HH24:MI:SS') AS dt_bloqueia,
					   ucb.usuario AS ds_bloqueia,
					   CASE WHEN rap.dt_bloqueia IS NOT NULL 
					        THEN 'Liberar'
							ELSE 'Bloquear'
					   END AS ds_acao,
					   CASE WHEN rap.dt_bloqueia IS NOT NULL 
					        THEN 'L'
							ELSE 'B'
					   END AS fl_acao					   
				  FROM projetos.rel_acompanhamento_plano rap
				  LEFT JOIN projetos.usuarios_controledi ucl
				    ON ucl.codigo = rap.cd_usuario_libera
				  LEFT JOIN projetos.usuarios_controledi ucb
				    ON ucb.codigo = rap.cd_usuario_bloqueia
				 WHERE rap.dt_exclusao IS NULL
				 ORDER BY rap.nr_ano DESC,
				          rap.nr_mes ASC
	           ";
	$ob_resul = pg_query($db, $qr_sql);
	while ($ar_reg=pg_fetch_array($ob_resul)) 
	{
		$tpl->newBlock('lista');
		$tpl->assign('cd_rel_acompanhamento_plano', $ar_reg['cd_rel_acompanhamento_plano']);
		$tpl->assign('dt_mes', $ar_reg['nr_ano']."-".$ar_reg['nr_mes']);
		
		$tpl->assign('dt_libera', $ar_reg['dt_libera']);
		$tpl->assign('ds_libera', $ar_reg['ds_libera']);
		
		
		
		$tpl->assign('dt_bloqueia', $ar_reg['dt_bloqueia']);
		$tpl->assign('ds_bloqueia', $ar_reg['ds_bloqueia']);
		
		$tpl->assign('ds_acao', $ar_reg['ds_acao']);
		$tpl->assign('fl_acao', $ar_reg['fl_acao']);
		$nr_conta++;
	}	

	$tpl->printToScreen();
	pg_close($db);
?>