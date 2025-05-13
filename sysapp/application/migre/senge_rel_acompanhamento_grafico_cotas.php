<?
   	include_once('inc/sessao.php');
   	include_once('inc/conexao.php');
   	include_once('inc/jpgraph/jpgraph-2.1.2/src/jpgraph.php');
   	include_once('inc/jpgraph/jpgraph-2.1.2/src/jpgraph_line.php');
	include_once('inc/jpgraph/jpgraph-2.1.2/src/jpgraph_bar.php');
   	include_once('inc/jpgraph/jpgraph-2.1.2/src/jpgraph_canvas.php');
	include_once('inc/jpgraph/jpgraph-2.1.2/src/jpgraph_canvtools.php');
	$meses = array("Janeiro","Fevereiro","Março","Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
	
	#### BUSCA CODIGOS DA POLICENTRO ####
	$sql = "
			SELECT p.cd_plano,
				   pp.cd_plano_financ, 
				   pp.cd_empresa_financ			
			  FROM participantes p,
				   planos_patrocinadoras pp
			 WHERE p.cd_empresa = 7
			   AND p.cd_empresa = pp.cd_empresa
			   AND p.cd_plano   = pp.cd_plano
			 GROUP BY p.cd_plano,
					  pp.cd_plano_financ, 
					  pp.cd_empresa_financ
		   ";
	$ob_resul = pg_query($db,$sql);
	$ar_reg   = pg_fetch_array($ob_resul);
	$qr_sql = "
				SELECT vl_cota, 
					   dt_ref_sld_cotas AS dt_cota,  
					   TO_CHAR(dt_ref_sld_cotas, 'DD/MM') AS dt_dia,  
					   TO_CHAR(dt_ref_sld_cotas, 'MM') AS dt_mes 
				  FROM qt_razao_cota
				 WHERE DATE_TRUNC('day',dt_ref_sld_cotas) BETWEEN TO_DATE('01/12/".($_REQUEST['ano']-1)."','DD/MM/YYYY') AND ((TO_DATE('".$DT_ATUAL."','YYYY-MM-DD') + '1 month'::interval) - '1 day'::interval)
				   AND dt_ref_sld_cotas IN (SELECT MAX(dt_ref_sld_cotas) 
											  FROM qt_razao_cota 
											 WHERE cod_tp_aplic = '00000' 
											   AND cod_plano = ".$ar_reg['cd_plano_financ']."
											   AND cod_empresa = ".$ar_reg['cd_empresa_financ']."
											 GROUP BY DATE_TRUNC('month', dt_ref_sld_cotas))
				   AND dt_ref_sld_cotas <= DATE_TRUNC('month',CURRENT_DATE) - '1 days'::interval -- MES ANTERIOR
				   AND cod_tp_aplic = '00000' 
				   AND cod_plano = ".$ar_reg['cd_plano_financ']."
				   AND cod_empresa = ".$ar_reg['cd_empresa_financ']."
				 ORDER BY dt_ref_sld_cotas
			  ";	
	$ob_resul = pg_query($db, $qr_sql);
	$nr_conta = 0;
	$ar_titulo = array();
	$ar_cota_mes = array();
	$ar_cota_acumulada = array();
	
	while ($ar_reg = pg_fetch_array($ob_resul)) 
	{
		if($nr_conta == 0)
		{
			$nr_anterior = $ar_reg['vl_cota'];
			$nr_conta_acumulada_anterior = 0;
		}
		else
		{
			$nr_cota_mes = (($ar_reg['vl_cota']/$nr_anterior) - 1) * 100;
			$nr_conta_acumulada = (((($nr_conta_acumulada_anterior / 100) + 1) * (($nr_cota_mes / 100) + 1)) - 1) * 100;
			//$ar_valor[] = array('mes' => $ar_reg['dt_mes'],'cota' => $ar_reg['vl_cota'], '% mes' => round($nr_cota_mes,2), '% acum' => round($nr_conta_acumulada,2));

			$ar_cota_mes[] = round($nr_cota_mes,2);
			$ar_cota_acumulada[] = round($nr_conta_acumulada,2);
			$ar_titulo[] = trim(substr($meses[($ar_reg['dt_mes'] - 1)],0,3));
			
			$nr_anterior = $ar_reg['vl_cota'];
			$nr_conta_acumulada_anterior = $nr_conta_acumulada;
		}
		
		$nr_conta++;
	}
	
	if(count($ar_cota_mes) < 12)
	{
		$nr_conta = 12 - (12 - count($ar_cota_mes));
		while($nr_conta < 12)	
		{
			$ar_cota_mes[] = 0;
			$ar_titulo[] = trim(substr($meses[$nr_conta],0,3));		
			$nr_conta++;
			
		}
	}
	
	//echo "<PRE>"; print_r($ar_titulo); exit;
	// Create the graph. 
	$graph = new Graph(650,400,"auto");       
	$graph->SetScale("textlin");
	$graph->img->SetMargin(70,30,40,50);
	$graph->SetShadow();
	$graph->title->SetFont(FF_ARIAL,FS_NORMAL,14);
	$graph->title->Set('% de Rentabilidade do Plano - '.$_REQUEST['mes']."/".$_REQUEST['ano']);
	$graph->legend->Pos(0.12,0.1,"left","top");

	
	#$graph->yaxis->SetFont(FF_ARIAL, FS_NORMAL, 9);
	$graph->yaxis->SetFont(FF_FONT1, FS_BOLD, 8);
	#$graph->yaxis->SetColor("darkgray");
	$graph->yaxis->scale->SetGrace(15); 
	$graph->yaxis->SetLabelFormatString("%0.2f %%");  
	
	$graph->xaxis->SetFont(FF_FONT1, FS_BOLD, 12);
	#$graph->xaxis->SetColor("darkgreen");
	$graph->xaxis->SetPos('min');
	$graph->xaxis->SetTickLabels($ar_titulo);
	$graph->xaxis->SetLabelAngle(90);

	// Create the linear error plot
	$l1plot=new LinePlot($ar_cota_acumulada);
	$l1plot->SetBarCenter(); 
	
	$l1plot->SetLegend("Acumulada");
	$l1plot->mark->SetColor('darkgreen'); 
	$l1plot->mark->SetFillColor('darkseagreen'); 
	$l1plot->mark->SetType(MARK_FILLEDCIRCLE);
	$l1plot->mark->SetSize(4); 
	
	$l1plot->SetColor("darkseagreen");
	$l1plot->SetWeight(3);
	#$l1plot->value->SetAngle(30);
	$l1plot->value->SetColor("darkgreen");
	$l1plot->value->SetFont(FF_FONT1, FS_BOLD, 10);
	$l1plot->value->SetFormat("%0.2f");
	$l1plot->value->show();

	// Create the bar plot
	$bplot = new BarPlot($ar_cota_mes);
	$bplot->SetLegend("Mensal");
	#$bplot->SetFillColor("orange");
	$bplot->SetFillGradient("orange","orange@.8",GRAD_WIDE_MIDVER);
	$bplot->SetWidth(0.8);
	$bplot->SetColor("orange");
	$bplot->SetValuePos('center');
	$bplot->value->SetColor('black'); 	
	//$bplot->value->SetShadow();
	#$bplot->value->SetFont(FF_ARIAL, FS_NORMAL, 8.5);
	$bplot->value->SetFont(FF_FONT1, FS_BOLD, 10);
	$bplot->value->SetFormat( "%0.2f");
	$bplot->value->show();	

	// Add the plots to t'he graph
	$graph->Add($bplot);
	$graph->Add($l1plot);

	// Display the graph
	if($fl_gera_arquivo)
	{
		$arquivo = "/u/www/upload/".substr(__FILE__, strrpos(__FILE__, '/')+1, strlen(__FILE__)).".png";
		$graph-> Stroke($arquivo);
		$fl_gera_arquivo = false;
	}
	else
	{
		$graph->Stroke();	
	}	
?>