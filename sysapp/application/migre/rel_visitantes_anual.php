<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	
    $tpl = new TemplatePower('tpl/tpl_rel_visitantes_anual.html');
	$tpl->prepare();
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
	
	if(trim($_REQUEST['dt_ano']) == "")
	{
		$tpl->assign('dt_ano', date('Y'));
	}
	else
	{
		$tpl->assign('dt_ano', $_REQUEST['dt_ano']);
	}	
	
	if(trim($_REQUEST['dt_ano']) == "")
	{	
		$_REQUEST['dt_ano'] = "DATE_TRUNC('month', DATE_TRUNC('year', CURRENT_DATE)) AND (CAST(DATE_TRUNC('month', DATE_TRUNC('year', CURRENT_DATE)) + '12 month' AS date) - 1)";
    }
    else
	{
		$_REQUEST['dt_ano'] = "DATE_TRUNC('month', DATE_TRUNC('year', TO_DATE('01/01/".$_REQUEST['dt_ano']."','YYYY-MM-DD'))) AND (CAST(DATE_TRUNC('month', DATE_TRUNC('year', TO_DATE('01/01/".$_REQUEST['dt_ano']."','DD/MM/YYYY'))) + '12 month' AS date) - 1)";
	}

	#### TEMPOS ####
	$qr_select = "					
					SELECT MAX(ap.dt_saida - ap.dt_entrada) AS hr_tempo_max,
                           MIN(ap.dt_saida - ap.dt_entrada) AS hr_tempo_min,					
                           AVG(ap.dt_saida - ap.dt_entrada) AS hr_tempo_medio
					  FROM projetos.visitantes ap
					 WHERE DATE_TRUNC('day',ap.dt_saida) BETWEEN ".$_REQUEST['dt_ano']."
				 ";

	$ob_result = pg_query($db, $qr_select);	
	$ar_reg    = pg_fetch_array($ob_result);
	$tpl->newBlock('tempo_medio_anual');
	$tpl->assign('hr_tempo_max', $ar_reg['hr_tempo_max']);
	$tpl->assign('hr_tempo_min', $ar_reg['hr_tempo_min']);
	$ar_tmp =  explode(".",$ar_reg['hr_tempo_medio']);
	$tpl->assign('hr_tempo_medio',$ar_tmp[0]);		
	
	#### CABEÇALHO ####
	$qr_select = "					
					SELECT DISTINCT(TO_CHAR(ap1.dt_entrada,'MM/YYYY')) AS dt_dia
					  FROM projetos.visitantes ap1
					 WHERE DATE_TRUNC('day',ap1.dt_entrada) BETWEEN ".$_REQUEST['dt_ano']."
				 ";
	$ob_result = pg_query($db, $qr_select);	
	while($ar_reg = pg_fetch_array($ob_result))
	{
		$tpl->newBlock('lst_movimento_anual_mes_titu');
		$tpl->assign('dt_dia',  $ar_reg['dt_dia']);	
	}

	#### QUANTIDADES ####
	$qr_select = "					
					SELECT DISTINCT(l.descricao) AS descricao,
					       TO_CHAR(ap1.dt_entrada,'MM/YYYY') AS dt_dia,
                           c.qt_total
					  FROM projetos.visitantes ap1
                      LEFT JOIN public.listas l
                        ON l.codigo = ap1.cd_tipo_visita
                      LEFT JOIN (SELECT ap.cd_tipo_visita,
                                        TO_CHAR(ap.dt_entrada,'YYYY-MM') AS dt_mes,
                                        COUNT(*) AS qt_total 
                                   FROM projetos.visitantes ap 
                                  WHERE DATE_TRUNC('day',ap.dt_entrada) BETWEEN ".$_REQUEST['dt_ano']."
                                  GROUP BY ap.cd_tipo_visita,
                                           TO_CHAR(ap.dt_entrada,'YYYY-MM')) c 
					    ON c.cd_tipo_visita = ap1.cd_tipo_visita 
					   AND c.dt_mes = TO_CHAR(ap1.dt_entrada,'YYYY-MM') 
					 WHERE DATE_TRUNC('day',ap1.dt_entrada) BETWEEN ".$_REQUEST['dt_ano']."
					   AND categoria = 'TACE'
					   AND divisao   = 'GAD'
				 ";
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
				$tpl->newBlock('lst_movimento_anual_mes');
				$tpl->assign('qt_total',  $qt_total_dia);
				
				$tpl->newBlock('lst_movimento_anual_media');
				if($qt_total_dia != 0)
				{
					$tpl->assign('nr_media',  number_format($qt_total_dia/$nr_conta_media,2));				
				}
			}
			$qt_total_dia = 0;
			$nr_conta_media = 0;

			$tpl->newBlock('lst_movimento_anual');
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
			$tpl->newBlock('lst_movimento_anual_mes');
			$tpl->assign('qt_total',  $ar_reg['qt_total']);
			$dt_dia_atual = $ar_reg['dt_dia'];
			$qt_total_dia += $ar_reg['qt_total'];
			$nr_conta_media++;
		//}
	}	
	$tpl->newBlock('lst_movimento_anual_mes');
	$tpl->assign('qt_total',  $qt_total_dia);
	$tpl->newBlock('lst_movimento_anual_media');
	if($qt_total_dia != 0)
	{
		$tpl->assign('nr_media',  number_format($qt_total_dia/$nr_conta_media,2));		
	}

	
	#### TOTALIZADOR ####
	$qr_select = "					
					SELECT DISTINCT(TO_CHAR(ap1.dt_entrada,'YYYY-MM')) AS dt_dia,
					       COUNT(*) AS qt_total
					  FROM projetos.visitantes ap1
					 WHERE DATE_TRUNC('day',ap1.dt_entrada) BETWEEN ".$_REQUEST['dt_ano']."
					 GROUP BY (TO_CHAR(ap1.dt_entrada,'YYYY-MM'))
				 ";
	$ob_result = pg_query($db, $qr_select);	
	$qt_total = 0;
	$nr_conta_media = 0;
	while($ar_reg = pg_fetch_array($ob_result))
	{
		$tpl->newBlock('lst_movimento_anual_mes_total');
		$tpl->assign('qt_total',  $ar_reg['qt_total']);	
		$qt_total += $ar_reg['qt_total'];
		$nr_conta_media++;
	}	
	$tpl->newBlock('lst_movimento_anual_mes_total');
	$tpl->assign('qt_total',  $qt_total);	
	$tpl->newBlock('lst_movimento_anual_media_total');
	if($qt_total != 0)
	{
		$tpl->assign('nr_media',  str_replace(",","",number_format($qt_total/$nr_conta_media,2)));
	}

	
	
	$tpl->printToScreen();
	pg_close($db);
?>