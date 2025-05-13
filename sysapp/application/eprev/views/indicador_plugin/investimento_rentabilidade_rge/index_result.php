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

$nr_valor_1 = 0;
$nr_valor_2 = 0;
$nr_valor_3 = 0;
$nr_valor_4 = 0;
$nr_valor_5 = 0;
$nr_valor_6 = 0;

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
		$link = anchor("indicador_plugin/investimento_rentabilidade_rge/cadastro/".$item["cd_investimento_rentabilidade_rge"], "editar");

		$referencia = $item['mes_referencia'];
	}

	$nr_valor_1 = $item['nr_valor_1'];
	$nr_valor_2 = $item['nr_valor_2'];
	$nr_valor_3 = $item['nr_valor_3'];
	$nr_valor_4 = $item['nr_valor_4'];
	$nr_valor_5 = $item['nr_valor_5'];
	$nr_valor_6 = $item['nr_valor_6'];

	if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && trim($item['fl_media']) != 'S')
	{
		$contador_ano_atual++;
		$media_ano[] = $item["nr_valor_2"];
	}
	
	$body[] = array(
		$contador--,
		$referencia,
		(trim($item["nr_valor_1"]) != '' ? number_format($item["nr_valor_1"], 4, ',', '.').' %' : ''),
		(trim($item["nr_valor_2"]) != '' ? number_format($item["nr_valor_2"], 4, ',', '.').' %' : ''),
		(trim($item["nr_valor_3"]) != '' ? number_format($item["nr_valor_3"], 4, ',', '.').' %' : ''),
		(trim($item["nr_valor_4"]) != '' ? number_format($item["nr_valor_4"], 4, ',', '.').' %' : ''),
		(trim($item["nr_valor_5"]) != '' ? number_format($item["nr_valor_5"], 4, ',', '.').' %' : ''),
		(trim($item["nr_valor_6"]) != '' ? number_format($item["nr_valor_6"], 4, ',', '.').' %' : ''),
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
		(trim($nr_valor_2) != '' ? number_format($nr_valor_2, 4, ',', '.').' %' : ''),
		(trim($nr_valor_3) != '' ? number_format($nr_valor_3, 4, ',', '.').' %' : ''),
		(trim($nr_valor_4) != '' ? number_format($nr_valor_4, 4, ',', '.').' %' : ''),
		(trim($nr_valor_5) != '' ? number_format($nr_valor_5, 4, ',', '.').' %' : ''),
		(trim($nr_valor_6) != '' ? number_format($nr_valor_6, 4, ',', '.').' %' : ''),
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