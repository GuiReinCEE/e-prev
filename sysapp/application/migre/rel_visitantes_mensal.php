<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	
    $tpl = new TemplatePower('tpl/tpl_rel_visitantes_mensal.html');
	$tpl->prepare();
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
	
	if(trim($_POST['dt_mes']) == "")
	{
		$tpl->assign('dt_mes', date('m/Y'));
	}
	else
	{
		$tpl->assign('dt_mes', $_POST['dt_mes']);
	}
	
	$_POST['dt_mes'] = (trim($_POST['dt_mes']) == "" ? "CURRENT_DATE" : "TO_DATE('01/".$_POST['dt_mes']."','DD/MM/YYYY')");
	
	#### TEMPOS ####
	$qr_select = "					
					SELECT MAX(ap.dt_saida - ap.dt_entrada) AS hr_tempo_max,
                           MIN(ap.dt_saida - ap.dt_entrada) AS hr_tempo_min,					
                           AVG(ap.dt_saida - ap.dt_entrada) AS hr_tempo_medio
					  FROM projetos.visitantes ap
					 WHERE DATE_TRUNC('day',ap.dt_saida) BETWEEN DATE_TRUNC('month', ".$_POST['dt_mes'].") AND (CAST(DATE_TRUNC('month', ".$_POST['dt_mes'].") + '1 month' AS date) - 1)
				 ";
	$ob_result = pg_query($db, $qr_select);	
	$ar_reg    = pg_fetch_array($ob_result);
	$tpl->newBlock('tempo_medio_mensal');
	$tpl->assign('hr_tempo_max', $ar_reg['hr_tempo_max']);
	$tpl->assign('hr_tempo_min', $ar_reg['hr_tempo_min']);
	$ar_tmp =  explode(".",$ar_reg['hr_tempo_medio']);
	$tpl->assign('hr_tempo_medio',$ar_tmp[0]);		
	
	#### CABEÇALHO ####
	$qr_select = "					
					SELECT DISTINCT(TO_CHAR(ap1.dt_entrada,'DD')) AS dt_dia
					  FROM projetos.visitantes ap1
					 WHERE DATE_TRUNC('day',ap1.dt_entrada) BETWEEN DATE_TRUNC('month', ".$_POST['dt_mes'].") AND (CAST(DATE_TRUNC('month', ".$_POST['dt_mes'].") + '1 month' AS date) - 1)
				 ";
	$ob_result = pg_query($db, $qr_select);	
	while($ar_reg = pg_fetch_array($ob_result))
	{
		$tpl->newBlock('lst_movimento_mensal_dia_titu');
		$tpl->assign('dt_dia',  $ar_reg['dt_dia']);	
	}	
	
	#### QUANTIDADES ####
	$qr_select = "					
					SELECT dt.descricao,
					       dt.dt_entrada,
					       COALESCE(de.qt_total,0) AS qt_total
					  FROM (SELECT l.codigo AS cd_tipo_visita,
					               l.descricao,
					               dt.dt_entrada
					         FROM (SELECT DISTINCT(DATE_TRUNC('day',ap1.dt_entrada)) AS dt_entrada
						         FROM projetos.visitantes ap1
					                WHERE DATE_TRUNC('day',ap1.dt_entrada) BETWEEN DATE_TRUNC('month', ".$_POST['dt_mes'].") AND (CAST(DATE_TRUNC('month', ".$_POST['dt_mes'].") + '1 month' AS date) - 1)
					                GROUP BY DATE_TRUNC('day',ap1.dt_entrada)) dt,
					              public.listas l
					        WHERE l.categoria = 'TACE'
					          AND l.divisao   = 'GAD') dt
					  LEFT JOIN (SELECT ap.cd_tipo_visita,
					                    DATE_TRUNC('day',ap.dt_entrada) AS dt_entrada,
					                    COUNT(*) AS qt_total
					               FROM projetos.visitantes ap
					              WHERE DATE_TRUNC('day',ap.dt_entrada) BETWEEN DATE_TRUNC('month', ".$_POST['dt_mes'].") AND (CAST(DATE_TRUNC('month', ".$_POST['dt_mes'].") + '1 month' AS date) - 1)
					              GROUP BY ap.cd_tipo_visita,
					                       DATE_TRUNC('day',ap.dt_entrada)) de
					    ON de.dt_entrada = dt.dt_entrada
					   AND de.cd_tipo_visita = dt.cd_tipo_visita 	
					 ORDER BY dt.descricao,
					          dt.dt_entrada
				 ";
				 
				 //echo "<PRE><!-- 
				 //";
				 //echo $qr_select;
				 //echo "
				 //--></PRE>";
	$ob_result = pg_query($db, $qr_select);	
	$nr_conta = 0;
	$nr_conta_media = 0;
	$ds_descricao_atual = "";
	$dt_dia_atual = "";
	$qt_total_dia = -1;
	while($ar_reg = pg_fetch_array($ob_result))
	{
		if($ds_descricao_atual != $ar_reg['descricao'])
		{
			if($qt_total_dia > -1)
			{
				$tpl->newBlock('lst_movimento_mensal_dia');
				$tpl->assign('qt_total',  $qt_total_dia); //$ar_reg['qt_total']);
				
				$tpl->newBlock('lst_movimento_mensal_media');
				if($qt_total_dia != 0)
				{
					$tpl->assign('nr_media',  number_format($qt_total_dia/$nr_conta_media,2));					
				}
			}
			$qt_total_dia = 0;
			$nr_conta_media = 0;

			$tpl->newBlock('lst_movimento_mensal');
			$tpl->assign('descricao', $ar_reg['descricao']);
			$ds_descricao_atual = $ar_reg['descricao'];

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

		//if($dt_dia_atual != $ar_reg['dt_dia'])
		//{
			$tpl->newBlock('lst_movimento_mensal_dia');
			$tpl->assign('qt_total',  $ar_reg['qt_total']);
			$dt_dia_atual = $ar_reg['dt_dia'];
			$qt_total_dia += $ar_reg['qt_total'];
			$nr_conta_media++;
		//}
		
	}	
	$tpl->newBlock('lst_movimento_mensal_dia');
	$tpl->assign('qt_total',  $qt_total_dia);
	$tpl->newBlock('lst_movimento_mensal_media');
	if($qt_total_dia != 0)
	{
		$tpl->assign('nr_media',  number_format($qt_total_dia/$nr_conta_media,2));	
	}
 	
	#### TOTALIZADOR ####
	$qr_select = "					
					SELECT DISTINCT(TO_CHAR(ap1.dt_entrada,'DD')) AS dt_dia,
					       COUNT(*) AS qt_total
					  FROM projetos.visitantes ap1
					 WHERE DATE_TRUNC('day',ap1.dt_entrada) BETWEEN DATE_TRUNC('month', ".$_POST['dt_mes'].") AND (CAST(DATE_TRUNC('month', ".$_POST['dt_mes'].") + '1 month' AS date) - 1)
					 GROUP BY (TO_CHAR(ap1.dt_entrada,'DD'))
				 ";
	$ob_result = pg_query($db, $qr_select);	
	$qt_total = 0;
	$nr_conta_media = 0;
	while($ar_reg = pg_fetch_array($ob_result))
	{
		$tpl->newBlock('lst_movimento_mensal_dia_total');
		$tpl->assign('qt_total',  $ar_reg['qt_total']);	
		$qt_total += $ar_reg['qt_total'];
		$nr_conta_media++;
	}	
	$tpl->newBlock('lst_movimento_mensal_dia_total');
	$tpl->assign('qt_total',  $qt_total);	
	$tpl->newBlock('lst_movimento_mensal_media_total');
	if($qt_total != 0)
	{	
		$tpl->assign('nr_media',  number_format($qt_total/$nr_conta_media,2));	
	}
	
	$tpl->printToScreen();
	pg_close($db);
?>