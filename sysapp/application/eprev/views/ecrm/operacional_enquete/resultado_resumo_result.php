<?php
$body = Array();
$head = array('Item','');

$qt_agrupamento        = $ar_resumo["vl_peso_1"] + 1;
$vl_media_geral        = (($ar_resumo["vl_media_1"] * $ar_resumo["vl_peso_1"]) + $ar_resumo["vl_media_2"]) / $qt_agrupamento;
$vl_media_geral_agrupa = (($ar_resumo["vl_media_1"] + $ar_resumo["vl_media_2"]) / 2);  

$body[] = array(
				array("Público total", 'text-align:left'),
				number_format($ar_resumo["nr_publico_total"],0,",","."),
			 );

$body[] = array(
				array("Número de respondentes", 'text-align:left'),
				number_format($ar_resumo["qt_respondente"],0,",","."),
			 );

$body[] = array(
				array("Média geral entre todos os agrupamentos, trazidos à base 10 / núm. agrupamentos: (".$qt_agrupamento.")", 'text-align:left'),
				number_format($vl_media_geral,2,",","."),
			 );		

$body[] = array(
				array("Média geral entre agrupamentos de respostas(".number_format($ar_resumo["vl_media_1"],2,",",".").") e percepção da área (".number_format($ar_resumo["vl_media_2"],2,",",".").")", 'text-align:left'),
				number_format($vl_media_geral_agrupa,2,",","."),
			 );			 
			 

$this->load->helper('grid');
$grid = new grid();
$grid->id_tabela  = "tbResumo";
$grid->view_count = false;
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>
