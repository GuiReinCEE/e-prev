<?php
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/jpgraph.php');
	include_once('inc/jpgraph_pie.php');
	include_once('inc/jpgraph_pie3d.php');
	include_once('inc/class.TemplatePower.inc.php');

	$_REQUEST['dt_dia'] = (trim($_REQUEST['dt_dia']) == "" ? "CURRENT_DATE" : "TO_DATE('".$_REQUEST['dt_dia']."','DD/MM/YYYY')");

	$ar_valores = array();
	$ar_legenda = array();

	$qr_select = "
		SELECT l.descricao,
               (SELECT COUNT(*)
				  FROM projetos.visitantes ap
				 WHERE DATE_TRUNC( 'day', ap.dt_entrada) = ".$_REQUEST['dt_dia']."
				   AND ap.cd_tipo_visita = l.codigo) AS qt_total
		  FROM public.listas l
		 WHERE l.categoria = 'TACE'
		   AND l.divisao = 'GAD'	
	";

	$ob_result = pg_query($db, $qr_select);
	$qt_total = 0;
	while($ar_reg = pg_fetch_array($ob_result))
	{
		$ar_legenda[] = $ar_reg['descricao'];
		$ar_valores[] = $ar_reg['qt_total'];
		$qt_total    += $ar_reg['qt_total'];
	}

	if($qt_total > 0)
	{
		$graph = new PieGraph(600, 270, "auto");
		$graph->img->SetAntiAliasing();
		$pz_c = new PiePlot3D($ar_valores);
		$pz_c->SetLegends($ar_legenda);
		$pz_c->SetCenter(0.35,0.45);
		$pz_c->SetSize(0.5);
		$pz_c->SetAngle(50);
		$pz_c->ExplodeSlice(1);
		$pz_c->SetTheme("water");
		$graph->Add($pz_c);
		$graph->Stroke();
	}
?>