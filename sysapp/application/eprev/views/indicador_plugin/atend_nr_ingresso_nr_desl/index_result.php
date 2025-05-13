<?php
$body = array();
$head = array( 
	'#', $label_0, $label_1, $label_2, $label_3, /*$label_4,*/ $label_5, $label_7, ''
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
$nr_valor_3_total = 0;

foreach($collection as $item)
{
	$a_data     = explode("/", $item['mes_referencia']);
	$observacao = $item["observacao"];
	
	if(trim($item['fl_media']) == 'S')
	{
		$link = '';

		$referencia = " Total de " . $item['ano_referencia'];
	}
	else
	{
		$link = anchor("indicador_plugin/atend_nr_ingresso_nr_desl/cadastro/" . $item["cd_atend_nr_ingresso_nr_desl"], "editar");

		$referencia = $item['mes_referencia'];
	}
	
	$nr_valor_1      = $item["nr_valor_1"];
	$nr_valor_2      = $item["nr_valor_2"];
	$nr_valor_3      = $item["nr_valor_3"];
	$nr_meta         = $item["nr_meta"];
	$nr_percentual_f = $item['nr_percentual_f'];
	
	if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && trim($item['fl_media']) != 'S')
	{
		$contador_ano_atual++;
		$media_ano[] = $nr_percentual_f;
		
		$nr_meta_media += $nr_meta;

		$nr_valor_1_total += $item["nr_valor_1"];
		$nr_valor_2_total += $item["nr_valor_2"];
		$nr_valor_3_total += $item["nr_valor_3"];
	}
	
	$body[] = array(
		$contador--,
		$referencia,
		(trim($nr_valor_1) != '' ? number_format($nr_valor_1, 0, ',', '.') : ''),
		(trim($nr_valor_3) != '' ? number_format($nr_valor_3, 0, ',', '.') : ''),
		(trim($nr_valor_2) != '' ? number_format($nr_valor_2, 0, ',', '.') : ''),
		//(trim($nr_percentual_f) != '' ? number_format($nr_percentual_f, 2, ',', '.').' %' : ''),
		$nr_meta,
		array(nl2br($observacao), 'text-align : justify'), 
		$link 
	);
}

if(sizeof($media_ano) >0)
{
	
	foreach($media_ano as $valor)
	{
		$media += $valor;
	}

	$body[] = array(
		0, 
		'<b>Total de '.intval($tabela[0]['nr_ano_referencia']).'</b>', 
		number_format($nr_valor_1_total, 0, ',', '.'),
		number_format($nr_valor_3_total, 0, ',', '.'),
		number_format($nr_valor_2_total, 0, ',', '.'), 
		//'<big><b>'.app_decimal_para_php(number_format((($nr_valor_2_total*100) / ($nr_valor_1_total > 0 ? $nr_valor_1_total : 1)), 2)).' %</b></big>', 
		$nr_meta_media, 
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