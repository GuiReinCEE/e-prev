<?php
$this->load->helper('grid');
$this->load->plugin('pchart');
#print_r($ar_reg);

$ar_tipo    = array("barra", "barra");
$ar_legenda = array("Aberta", "Atendida");

######################## SUPORTE ########################
$ar_rotulo    = Array();
$ar_valor     = Array();
$ar_aberta    = Array();
$ar_concluida = Array();

$body=array();
$head = array( 
	'Gerência',
	'Qt Aberta',
	'Qt Cancelada',	
	'Qt Concluída',
	'Qt Suspensa',
	'Qt Atendida',
	'% Atendida'
);

foreach($ar_reg as $ar_item)
{
	$qt_suporte_atendida = (intval($ar_item["qt_suporte_concluida"]) + intval($ar_item["qt_suporte_cancelada"]));
	$p_suporte_atendida  = ($qt_suporte_atendida == 0 ? 0 : (($qt_suporte_atendida * 100) / intval($ar_item["qt_suporte_aberta"])));
	
	$body[] = array(
		array($ar_item["cd_gerencia"],"text-align:left; font-weight: bold;"),

		array(number_format(intval($ar_item["qt_suporte_aberta"]),0,",","."),'text-align:right; color: green; font-weight: bold;','int'),
		array(number_format(intval($ar_item["qt_suporte_cancelada"]),0,",","."),'text-align:right; color: gray; font-weight: bold;','int'),
		array(number_format(intval($ar_item["qt_suporte_concluida"]),0,",","."),'text-align:right; color: gray; font-weight: bold;','int'),
		array(number_format(intval($ar_item["qt_suporte_suspensa"]),0,",","."),'text-align:right; color: gray; font-weight: bold;','int'),
		array(number_format($qt_suporte_atendida,0,",","."),'text-align:right; color: blue; font-weight: bold;','int'),
		array(number_format($p_suporte_atendida,2,",","."),'text-align:right;')
	);
	
	$ar_rotulo[]    = $ar_item["cd_gerencia"];
	$ar_aberta[]    = intval($ar_item["qt_suporte_aberta"]);
	$ar_concluida[] = $qt_suporte_atendida;	
}
$ar_valor[] = $ar_aberta;
$ar_valor[] = $ar_concluida;

$grid = new grid();
$grid->id_tabela  = "tbSuporte";
$grid->view_count = false;
$grid->head       = $head;
$grid->body       = $body;
echo form_start_box("boxSuporte","SUPORTE",FALSE);
	echo $grid->render();
	echo img(group_barchart($ar_rotulo, $ar_valor, $ar_tipo, $ar_legenda, 600, 400));	
echo form_end_box("boxSuporte");
echo br(2);

#$grafico = group_barchart($rotulo,$valores,$tipo,$legenda,$this->nr_largura, $this->nr_altura,$ar_referencia);

######################## SISTEMAS ########################
$ar_rotulo    = Array();
$ar_valor     = Array();
$ar_aberta    = Array();
$ar_concluida = Array();

$body=array();
$head = array( 
	'Gerência',

	'Qt Aberta',
	'Qt Cancelada',	
	'Qt Concluída',
	'Qt Suspensa',
	'Qt Atendida',
	'% Atendida'
);

foreach($ar_reg as $ar_item)
{
	$qt_desenv_atendida = (intval($ar_item["qt_desenv_concluida"]) + intval($ar_item["qt_desenv_cancelada"]));
	$p_desenv_atendida  = ($qt_desenv_atendida == 0 ? 0 : (($qt_desenv_atendida * 100) / intval($ar_item["qt_desenv_aberta"])));
	
	$body[] = array(
		array($ar_item["cd_gerencia"],"text-align:left; font-weight: bold;"),
		
		array(number_format(intval($ar_item["qt_desenv_aberta"]),0,",","."),'text-align:right; color: green; font-weight: bold;','int'),
		array(number_format(intval($ar_item["qt_desenv_cancelada"]),0,",","."),'text-align:right; color: gray; font-weight: bold;','int'),
		array(number_format(intval($ar_item["qt_desenv_concluida"]),0,",","."),'text-align:right; color: gray; font-weight: bold;','int'),
		array(number_format(intval($ar_item["qt_desenv_suspensa"]),0,",","."),'text-align:right; color: gray; font-weight: bold;','int'),
		array(number_format($qt_desenv_atendida,0,",","."),'text-align:right; color: blue; font-weight: bold;','int'),
		array(number_format($p_desenv_atendida,2,",","."),'text-align:right;')
	);
	
	$ar_rotulo[]    = $ar_item["cd_gerencia"];
	$ar_aberta[]    = intval($ar_item["qt_desenv_aberta"]);
	$ar_concluida[] = $qt_desenv_atendida;	
}
$ar_valor[] = $ar_aberta;
$ar_valor[] = $ar_concluida;

$grid = new grid();
$grid->id_tabela = "tbSistemas";
$grid->view_count = false;
$grid->head       = $head;
$grid->body       = $body;
echo form_start_box("boxSistemas","SISTEMAS",FALSE);
	echo $grid->render();
	echo img(group_barchart($ar_rotulo, $ar_valor, $ar_tipo, $ar_legenda, 600, 400));	
echo form_end_box("boxSistemas");
echo br(2);

######################## TOTAL ########################
$ar_rotulo    = Array();
$ar_valor     = Array();
$ar_aberta    = Array();
$ar_concluida = Array();

$body=array();
$head = array( 
	'Gerência',
	
	'Qt Total Aberta',
	'Qt Total Concluída',
	'Qt Total Cancelada',
	'Qt Total Suspensa',
	'Qt Total Atendida',
	'% Total Atendida'
);

foreach($ar_reg as $ar_item)
{
	$qt_suporte_atendida = (intval($ar_item["qt_suporte_concluida"]) + intval($ar_item["qt_suporte_cancelada"]));
	$qt_desenv_atendida  = (intval($ar_item["qt_desenv_concluida"]) + intval($ar_item["qt_desenv_cancelada"]));
	$qt_total_atendida   = $qt_suporte_atendida + $qt_desenv_atendida;
	
	$p_total_atendida =  ($qt_total_atendida == 0 ? 0 : (($qt_total_atendida * 100) / (intval($ar_item["qt_suporte_aberta"]) + intval($ar_item["qt_desenv_aberta"]))));
	
	$body[] = array(
		array($ar_item["cd_gerencia"],"text-align:left; font-weight: bold;"),
		
		array(number_format(intval($ar_item["qt_suporte_aberta"]) + intval($ar_item["qt_desenv_aberta"]),0,",","."),'text-align:right; color: green; font-weight: bold;','int'),
		array(number_format(intval($ar_item["qt_suporte_cancelada"]) + intval($ar_item["qt_desenv_cancelada"]),0,",","."),'text-align:right; color: gray; font-weight: bold;','int'),
		array(number_format(intval($ar_item["qt_suporte_concluida"]) + intval($ar_item["qt_desenv_concluida"]),0,",","."),'text-align:right; color: gray; font-weight: bold;','int'),
		array(number_format(intval($ar_item["qt_suporte_suspensa"]) + intval($ar_item["qt_desenv_suspensa"]),0,",","."),'text-align:right; color: gray; font-weight: bold;','int'),
		array(number_format($qt_total_atendida,0,",","."),'text-align:right; color: blue; font-weight: bold;','int'),
		array(number_format($p_total_atendida,2,",","."),'text-align:right;')
	);
	
	$ar_rotulo[]    = $ar_item["cd_gerencia"];
	$ar_aberta[]    = intval($ar_item["qt_suporte_aberta"]) + intval($ar_item["qt_desenv_aberta"]);
	$ar_concluida[] = $qt_total_atendida;	
}
$ar_valor[] = $ar_aberta;
$ar_valor[] = $ar_concluida;

$grid = new grid();
$grid->id_tabela = "tbTotal";
$grid->view_count = false;
$grid->head       = $head;
$grid->body       = $body;
echo form_start_box("boxTotal","TOTAL",FALSE);
	echo $grid->render();
	echo img(group_barchart($ar_rotulo, $ar_valor, $ar_tipo, $ar_legenda, 600, 400));	
echo form_end_box("boxTotal");
echo br(5);
?>