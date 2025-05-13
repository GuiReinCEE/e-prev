<?php
	include_once('inc/sessao.php');
   	include_once('inc/conexao.php');
   	include_once('inc/jpgraph.php');
	include_once('inc/jpgraph_bar.php');
	include_once('inc/class.TemplatePower.inc.php');
	
	$_REQUEST['dt_mes'] = (trim($_REQUEST['dt_mes']) == "" ? "CURRENT_DATE" : "TO_DATE('01/".$_REQUEST['dt_mes']."','DD/MM/YYYY')");

	$datax = array();
	$datay = array();

	$qr_select = "					
					SELECT TO_CHAR(ap.dt_entrada,'DD') AS dt_dia,
					       COUNT(*)  AS qt_total
					  FROM projetos.visitantes ap
					 WHERE DATE_TRUNC('day', ap.dt_entrada) BETWEEN DATE_TRUNC('month', ".$_REQUEST['dt_mes'].") AND (CAST(DATE_TRUNC('month', ".$_REQUEST['dt_mes'].") + '1 month' AS date) - 1)
					 GROUP BY TO_CHAR(ap.dt_entrada,'DD') 
					 ORDER BY TO_CHAR(ap.dt_entrada,'DD')
				 ";	
	$ob_result = pg_query($db, $qr_select);	
	$qt_total = 0;
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