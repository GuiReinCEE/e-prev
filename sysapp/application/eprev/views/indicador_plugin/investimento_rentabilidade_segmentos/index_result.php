<?php
$body = array();
$head = array( 
	'#', $label_0, $label_1, /*$label_2,*/ $label_3, $label_4, $label_5, $label_6, $label_7, $label_8, $label_9, 
	$label_11, /*$label_12,*/ $label_13, $label_14, $label_15, $label_16, $label_17, $label_18, $label_10, $label_19,   ''
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

$nr_valor_1 = 0;
$nr_valor_2 = 0;
$nr_valor_3 = 0;
$nr_valor_4 = 0;
$nr_valor_5 = 0;
$nr_valor_6 = 0;
$nr_valor_7 = 0;
$nr_valor_8 = 0;
$nr_valor_9 = 0;
$nr_valor_10 = 0;
$nr_valor_11 = 0;
$nr_valor_12 = 0;
$nr_valor_13 = 0;
$nr_valor_14 = 0;
$nr_valor_15 = 0;
$nr_valor_16 = 0;
$nr_valor_17 = 0;
$nr_valor_18 = 0;

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
		$link = anchor("indicador_plugin/investimento_rentabilidade_segmentos/cadastro/".$item["cd_investimento_rentabilidade_segmentos"], "editar");

		$referencia = $item['mes_referencia'];
	}

	$nr_valor_1 = $item['nr_valor_1'];
	$nr_valor_2 = $item['nr_valor_2'];
	$nr_valor_3 = $item['nr_valor_3'];
	$nr_valor_4 = $item['nr_valor_4'];
	$nr_valor_5 = $item['nr_valor_5'];
	$nr_valor_6 = $item['nr_valor_6'];
	$nr_valor_7 = $item['nr_valor_7'];
	$nr_valor_8 = $item['nr_valor_8'];
	$nr_valor_9 = $item['nr_valor_9'];
	$nr_valor_10 = $item['nr_valor_10'];
	$nr_valor_11 = $item['nr_valor_11'];
	$nr_valor_12 = $item['nr_valor_12'];
	$nr_valor_13 = $item['nr_valor_13'];
	$nr_valor_14 = $item['nr_valor_14'];
	$nr_valor_15 = $item['nr_valor_15'];
	$nr_valor_16 = $item['nr_valor_16'];
	$nr_valor_17 = $item['nr_valor_17'];
	$nr_valor_18 = $item['nr_valor_18'];
	
	if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && trim($item['fl_media']) != 'S')
	{
		$contador_ano_atual++;
	}
	
	$body[] = array(
		$contador--,
		$referencia,
		(trim($item["nr_valor_1"]) != '' ? number_format($item["nr_valor_1"], 4, ',', '.').' %' : ''),
		//(trim($item["nr_valor_2"]) != '' ? number_format($item["nr_valor_2"], 4, ',', '.').' %' : ''),
		(trim($item["nr_valor_3"]) != '' ? number_format($item["nr_valor_3"], 4, ',', '.').' %' : ''),
		(trim($item["nr_valor_4"]) != '' ? number_format($item["nr_valor_4"], 4, ',', '.').' %' : ''),
		(trim($item["nr_valor_5"]) != '' ? number_format($item["nr_valor_5"], 4, ',', '.').' %' : ''),
		(trim($item["nr_valor_6"]) != '' ? number_format($item["nr_valor_6"], 4, ',', '.').' %' : ''),
		(trim($item["nr_valor_7"]) != '' ? number_format($item["nr_valor_7"], 4, ',', '.').' %' : ''),
		(trim($item["nr_valor_8"]) != '' ? number_format($item["nr_valor_8"], 4, ',', '.').' %' : ''),
		(trim($item["nr_valor_9"]) != '' ? number_format($item["nr_valor_9"], 4, ',', '.').' %' : ''),
		(trim($item["nr_valor_11"]) != '' ? number_format($item["nr_valor_11"], 4, ',', '.').' %' : ''),
		//(trim($item["nr_valor_12"]) != '' ? number_format($item["nr_valor_12"], 4, ',', '.').' %' : ''),
		(trim($item["nr_valor_13"]) != '' ? number_format($item["nr_valor_13"], 4, ',', '.').' %' : ''),
		(trim($item["nr_valor_14"]) != '' ? number_format($item["nr_valor_14"], 4, ',', '.').' %' : ''),
		(trim($item["nr_valor_15"]) != '' ? number_format($item["nr_valor_15"], 4, ',', '.').' %' : ''),
		(trim($item["nr_valor_16"]) != '' ? number_format($item["nr_valor_16"], 4, ',', '.').' %' : ''),
		(trim($item["nr_valor_17"]) != '' ? number_format($item["nr_valor_17"], 4, ',', '.').' %' : ''),
		(trim($item["nr_valor_18"]) != '' ? number_format($item["nr_valor_18"], 4, ',', '.').' %' : ''),
		(trim($item["nr_valor_10"]) != '' ? number_format($item["nr_valor_10"], 4, ',', '.').' %' : ''),
		array($observacao, 'text-align:"left"'), 
		(trim($item["fl_editar"]) == 'S' ? $link : '')
	);
}

if(sizeof($media_ano) >0)
{
	$body[] = array(
		0, 
		'<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>', 
		(trim($nr_valor_1) != '' ? number_format($nr_valor_1, 4, ',', '.').' %' : ''),
		//(trim($nr_valor_2) != '' ? number_format($nr_valor_2, 4, ',', '.').' %' : ''),
		(trim($nr_valor_3) != '' ? number_format($nr_valor_3, 4, ',', '.').' %' : ''),
		(trim($nr_valor_4) != '' ? number_format($nr_valor_4, 4, ',', '.').' %' : ''),
		(trim($nr_valor_5) != '' ? number_format($nr_valor_5, 4, ',', '.').' %' : ''),
		(trim($nr_valor_6) != '' ? number_format($nr_valor_6, 4, ',', '.').' %' : ''),
		(trim($nr_valor_7) != '' ? number_format($nr_valor_7, 4, ',', '.').' %' : ''),
		(trim($nr_valor_8) != '' ? number_format($nr_valor_8, 4, ',', '.').' %' : ''),
		(trim($nr_valor_9) != '' ? number_format($nr_valor_9, 4, ',', '.').' %' : ''),
		(trim($nr_valor_11) != '' ? number_format($nr_valor_11, 4, ',', '.').' %' : ''),
		//(trim($nr_valor_12) != '' ? number_format($nr_valor_12, 4, ',', '.').' %' : ''),
		(trim($nr_valor_13) != '' ? number_format($nr_valor_13, 4, ',', '.').' %' : ''),
		(trim($nr_valor_14) != '' ? number_format($nr_valor_14, 4, ',', '.').' %' : ''),
		(trim($nr_valor_15) != '' ? number_format($nr_valor_15, 4, ',', '.').' %' : ''),
		(trim($nr_valor_16) != '' ? number_format($nr_valor_16, 4, ',', '.').' %' : ''),
		(trim($nr_valor_17) != '' ? number_format($nr_valor_17, 4, ',', '.').' %' : ''),
		(trim($nr_valor_18) != '' ? number_format($nr_valor_18, 4, ',', '.').' %' : ''),
		(trim($nr_valor_10) != '' ? number_format($nr_valor_10, 4, ',', '.').' %' : ''),
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