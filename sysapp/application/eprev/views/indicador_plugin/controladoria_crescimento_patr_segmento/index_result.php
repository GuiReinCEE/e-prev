<?php
$body = array();
$head = array( 
	'#', $label_0, $label_1, $label_2, $label_3, $label_4, $label_5, $label_6, $label_7, ''
);

$ar_janela = array(
	'width'      => '700',
	'height'     => '500',
	'scrollbars' => 'yes',
	'status'     => 'yes',
	'resizable'  => 'yes',
	'screenx'    => '0',
	'screeny'    => '0'
);

echo '<div style="text-align:center;">'.br().anchor_popup("indicador/apresentacao/detalhe/".intval( $tabela[0]['cd_indicador_tabela'] ), '[Visualizar Apresentação]', $ar_janela)."</div>";

$contador_ano_atual   = 0;
$contador             = sizeof($collection);
$a_data               = array(0, 0);
$media_ano            = array();
$nr_meta              = 0;
$media                = 0;
$nr_meta_media        = 0;

$nr_valor_1_total        = 0;
$nr_valor_2_total        = 0;
$nr_segmento_total       = 0;
$nr_fceee_total          = 0;
$nr_fceee_segmento_total = 0;
$nr_meta_total           = 0;

foreach($collection as $item)
{
	$a_data     = explode("/", $item['mes_referencia']);
	$observacao = $item["observacao"];
	
	if(trim($item['fl_media']) == 'S')
	{
		$link = '';

		$referencia = " Resultado de ".$item['ano_referencia'];
	}
	else
	{
		$link = anchor("indicador_plugin/controladoria_crescimento_patr_segmento/cadastro/".$item["cd_controladoria_crescimento_patr_segmento"], "editar");

		$referencia = $item['mes_referencia'];
	}
	
	if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && trim($item['fl_media']) != 'S')
	{
		$contador_ano_atual++;

		$nr_valor_1_total = $item["nr_valor_1"];
		$nr_valor_2_total = $item["nr_valor_2"];
		$nr_meta_total    = $item["nr_meta"];

		$nr_segmento_total       = $item['nr_segmento'];
		$nr_fceee_total          = $item['nr_fceee'];
		$nr_fceee_segmento_total = $item['nr_fceee_segmento'];
	}
	
	$body[] = array(
		$contador--,
		$referencia,
		(trim($item["nr_valor_1"]) != '' ? number_format($item["nr_valor_1"], 2, ',', '.') : ''),
		(trim($item["nr_valor_2"]) != '' ? number_format($item["nr_valor_2"], 2, ',', '.') : ''),
		(trim($item['nr_segmento']) != '' ? number_format($item['nr_segmento'], 2, ',', '.').' %' : ''),
		(trim($item['nr_fceee']) != '' ? number_format($item['nr_fceee'], 2, ',', '.').' %' : ''),
		(trim($item['nr_fceee_segmento']) != '' ? number_format($item['nr_fceee_segmento'], 2, ',', '.').' %' : ''),
		number_format($item["nr_meta"], 2, ',', '.').' %',
		array($observacao, 'text-align:"left"'), 
		$link 
	);
}

if($contador_ano_atual >0)
{
	/*
	$nr_segmento_total       = (($nr_valor_1_total / $row["nr_valor_1"]) - 1) * 100;
	$nr_fceee_total          = (($nr_valor_2_total / $row["nr_valor_2"]) - 1) * 100;
	$nr_fceee_segmento_total = (((1+$nr_fceee_total)/(1+$nr_segmento_total))-1);
	*/
	$body[] = array(
		0, 
		'<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>', 
		(trim($nr_valor_1_total) != '' ? number_format($nr_valor_1_total, 2, ',', '.') : ''),
		(trim($nr_valor_2_total) != '' ? number_format($nr_valor_2_total, 2, ',', '.') : ''),
		number_format($nr_segmento_total, 2, ',', '.').' %', 
		number_format($nr_fceee_total, 2, ',', '.').' %', 
		number_format($nr_fceee_segmento_total, 2, ',', '.').' %', 
		number_format($nr_meta_total, 2, ',', '.').' %', 
		'', 
		''
	);
}

echo "<input type='hidden' id='mes_input' name='mes_input' value='".$a_data[0]."' />";
echo "<input type='hidden' id='contador_input' name='contador_input' value='".$contador_ano_atual."' />";

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();

?>