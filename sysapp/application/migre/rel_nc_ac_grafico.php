<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/jpgraph.php');
	include_once('inc/jpgraph_pie.php');
	include_once('inc/jpgraph_pie3d.php');

	$graph = new PieGraph(600, 300, "auto");
	$graph->img->SetAntiAliasing();
	$graph->title->Set($_REQUEST['titulo']);
	$tam = 0.5;

	$valores_cr[0] = $_REQUEST['qt_item1'];
	$valores_cr[1] = $_REQUEST['qt_item2'];
	$pz_c = new PiePlot3D($valores_cr);
	$pz_c->SetCenter(0.41, 0.5);
	$pz_c->SetSize($tam);
	$pz_c->SetAngle(50);
	$pz_c->ExplodeSlice (1);
	$pz_c->SetTheme("water");
	$pz_c->SetLegends(array($_REQUEST['lb_item1'].": ".$_REQUEST['qt_item1'], $_REQUEST['lb_item2'].": ".$_REQUEST['qt_item2']));
    	
	$graph->Add($pz_c);
	$graph->Stroke();

?>