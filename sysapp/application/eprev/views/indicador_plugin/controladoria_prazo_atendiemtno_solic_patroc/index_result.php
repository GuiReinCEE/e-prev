<?php
$body = array();
$head = array( 
	'#', $label_0, $label_1, $label_2, $label_3, $label_4, $label_5, ''
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

$nr_valor_1_total = 0;
$nr_valor_2_total = 0;

foreach($collection as $item)
{
	$a_data     = explode("/", $item['mes_referencia']);
	$observacao = $item["observacao"];
	
	if(trim($item['fl_media']) == 'S')
	{
		$link = '';

		$referencia = " Média de " . $item['ano_referencia'];
	}
	else
	{
		$link = anchor("indicador_plugin/controladoria_prazo_atendiemtno_solic_patroc/cadastro/" . $item["cd_controladoria_prazo_atendiemtno_solic_patroc"], "editar");

		$referencia = $item['mes_referencia'];
	}
	
	$nr_valor_1   = $item["nr_valor_1"];
	$nr_valor_2   = $item["nr_valor_2"];
	$nr_meta      = $item["nr_meta"];
	$nr_resultado = $item["nr_resultado"];
	
	if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && trim($item['fl_media']) != 'S')
	{
		$contador_ano_atual++;
		$media_ano[] = $item["nr_valor_1"];

		$nr_valor_1_total += $item["nr_valor_1"];
		$nr_valor_2_total += $item["nr_valor_2"];
	}
	else
	{
		$nr_valor_1 = '';
		$nr_valor_2 = '';
		
	}
	
	$body[] = array(
		$contador--,
		($item["ds_evento"] != ''? $item["ds_evento"] : $referencia),
		(trim($nr_valor_1) != '' ? number_format($nr_valor_1, 0) : ''),
		(trim($nr_valor_2) != '' ? number_format($nr_valor_2, 0) : ''),
		(trim($nr_resultado) != '' ? number_format($nr_resultado, 2,',', '.'). ' %' : ''),
		(trim($nr_meta) != '' ? number_format($nr_meta, 2, ',', '.'). ' %' : ''),
		array($observacao, 'text-align:"left"'), 
		$link 
	);
}

if(sizeof($media_ano) > 0)
{
	foreach($media_ano as $valor)
	{
		$media += $valor;
	}

	$nr_valor_1_total = $nr_valor_1_total / $contador_ano_atual;
	$nr_valor_2_total = $nr_valor_2_total / $contador_ano_atual;

	$body[] = array(
		0, 
		'<b>Média de '.intval($tabela[0]['nr_ano_referencia']).'</b>', 
		'', 
		'', 
		'<big><b>'.app_decimal_para_php(number_format(((0.3*$nr_valor_1_total)+(0.7*$nr_valor_2_total))*100, 2,',', '.')).' %</b></big>', 
		app_decimal_para_php(number_format($nr_meta, 2,',', '.')).' %', 
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