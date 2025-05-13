<?php
	include_once('inc/sessao.php');
   	include_once('inc/conexao.php');
   	include_once('inc/jpgraph.php');
	include_once('inc/jpgraph_bar.php');
	include_once('inc/class.TemplatePower.inc.php');
	
	if(trim($_REQUEST['dt_ano']) == "")
	{	
		$_REQUEST['dt_ano'] = "DATE_TRUNC('month', DATE_TRUNC('year', CURRENT_DATE)) AND (CAST(DATE_TRUNC('month', DATE_TRUNC('year', CURRENT_DATE)) + '12 month' AS date) - 1)";
    }
    else
	{
		$_REQUEST['dt_ano'] = "DATE_TRUNC('month', DATE_TRUNC('year', TO_DATE('01/01/".$_REQUEST['dt_ano']."','DD/MM/YYYY'))) AND (CAST(DATE_TRUNC('month', DATE_TRUNC('year', TO_DATE('01/01/".$_REQUEST['dt_ano']."','DD/MM/YYYY'))) + '12 month' AS date) - 1)";
	}

	$datax = array();
	$datay = array();

	$qr_select = "					
					SELECT DISTINCT(TO_CHAR(ap1.dt_entrada,'MM/YYYY')) AS dt_dia,
					       COUNT(*) AS qt_total
					  FROM projetos.visitantes ap1
					 WHERE DATE_TRUNC('day', ap1.dt_entrada) BETWEEN ".$_REQUEST['dt_ano']."
					 GROUP BY (TO_CHAR(ap1.dt_entrada,'MM/YYYY'))					 
				 ";	
	$ob_result = pg_query($db, $qr_select);	
	$qt_total  = 0;
	while($ar_reg = pg_fetch_array($ob_result))
	{
		$datax[]  = $ar_reg['dt_dia'];
		$datay[]  = $ar_reg['qt_total'];
		$qt_total += $ar_reg['qt_total'];
	}
	
	if($qt_total > 0)
	{
		$graph = new Graph(600,270,"auto");	
		$graph->SetBackgroundImage('img/img_fundo2.gif', BGIMG_FILLFRAME);
	    $graph->img->SetAntiAliasing();
		$graph->SetScale("textlin");
		$graph->SetMarginColor("lightblue@0.3");
		$graph->xaxis->SetTickLabels($datax);
		
		$bplot = new BarPlot($datay);
		$bplot->SetWidth(0.6);
		$bplot->SetFillGradient("gold","gold@0.5",GRAD_HOR);
		$bplot->SetFillColor("deeppink");
		$bplot->SetValuePos('top');
		$bplot->value->SetFormat( "%0.0f");
		$bplot->value->show();	
				
		$graph->Add($bplot);
		$graph->Stroke();
	}
	
	
?>