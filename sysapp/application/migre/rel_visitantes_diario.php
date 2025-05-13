<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	require_once('inc/ajaxobject.php'); 
	include_once('inc/class.TemplatePower.inc.php');
	
    $tpl = new TemplatePower('tpl/tpl_rel_visitantes_diario.html');
	$tpl->prepare();
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
	
	if(trim($_POST['dt_dia']) == "")
	{
		$tpl->assign('dt_dia', date('d/m/Y'));
	}
	else
	{
		$tpl->assign('dt_dia', $_POST['dt_dia']);
	}
	
	$_POST['dt_dia'] = (trim($_POST['dt_dia']) == "" ? "CURRENT_DATE" : "TO_DATE('".$_POST['dt_dia']."','DD/MM/YYYY')");
	
	#### DIARIO #####
	$qr_select = "					
					SELECT MAX(ap.dt_saida - ap.dt_entrada) AS hr_tempo_max,
                           MIN(ap.dt_saida - ap.dt_entrada) AS hr_tempo_min,					
                           AVG(ap.dt_saida - ap.dt_entrada) AS hr_tempo_medio
					  FROM projetos.visitantes ap
					 WHERE DATE_TRUNC('day',ap.dt_saida) = ".$_POST['dt_dia']."
				 ";
	$ob_result = pg_query($db, $qr_select);	
	$ar_reg    = pg_fetch_array($ob_result);
	$tpl->newBlock('tempo_medio');
	$tpl->assign('hr_tempo_max', $ar_reg['hr_tempo_max']);
	$tpl->assign('hr_tempo_min', $ar_reg['hr_tempo_min']);
	$ar_tmp =  explode(".",$ar_reg['hr_tempo_medio']);
	$tpl->assign('hr_tempo_medio',$ar_tmp[0]);	

	$qr_select = "					
		SELECT l.codigo,
               l.descricao,
               (SELECT COUNT(*)
				  FROM projetos.visitantes ap
				 WHERE DATE_TRUNC('day',ap.dt_entrada) = ".$_POST['dt_dia']."
				   AND ap.cd_tipo_visita = l.codigo) AS qt_total
		  FROM public.listas l
		 WHERE l.categoria = 'TACE'
		   AND l.divisao   = 'GAD'	
	 ";
	$ob_result = pg_query($db, $qr_select);	
	$nr_conta = 0;
	$qt_total = 0;
	while($ar_reg = pg_fetch_array($ob_result))
	{
		$tpl->newBlock('lst_movimento_diario');
		$tpl->assign('descricao', $ar_reg['descricao']);
		$tpl->assign('qt_total',  $ar_reg['qt_total']);
		$qt_total += $ar_reg['qt_total'];
		if(($nr_conta % 2) != 0)
		{
			$tpl->assign('bg_color', '#F4F4F4');
		}
		else
		{
			$tpl->assign('bg_color', '#FFFFFF');		
		}
		$nr_conta++;
	}	
	$tpl->newBlock('lst_movimento_diario');
	$tpl->assign('descricao', "TOTAL");
	$tpl->assign('qt_total',  $qt_total);
	$tpl->assign('bg_color', '#dae9f7');

	$tpl->printToScreen();
	pg_close($db);
?>