<?php
$body = array();
$head = array( 
	'#', $label_0, $label_1, $label_2, $label_3, $label_4, $label_6, ''
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

		$referencia = " Total de ".$item['ano_referencia'];
	}
	else
	{
		$link = anchor("indicador_plugin/financeiro_originais_retificacoes_dctf/cadastro/".$item["cd_financeiro_originais_retificacoes_dctf"], "editar");

		$referencia = $item['mes_referencia'];
	}
	
	
	$nr_meta         = $item["nr_meta"];
	$nr_percentual_f = $item['nr_percentual_f'];
	
	if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && trim($item['fl_media']) != 'S')
	{
		$contador_ano_atual++;
		$media_ano[] = $nr_percentual_f;
		
		$nr_meta_media = $nr_meta;

		$nr_valor_1_total      += $item["nr_valor_1"];
		$nr_valor_2_total      += $item["nr_valor_2"];
	}
	
	$body[] = array(
		$contador--,
		$referencia,
		(trim($item["nr_valor_1"]) != '' ? $item["nr_valor_1"] : ''),
		(trim($item["nr_valor_2"]) != '' ? $item["nr_valor_2"] : ''),
		(trim($nr_percentual_f) != '' ? number_format($nr_percentual_f, 2, ',', '.').' %' : ''),
		number_format($nr_meta, 2, ',', '.').' %',
		array($observacao, 'text-align:"left"'), 
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
		(trim($nr_valor_1_total) != '' ? $nr_valor_1_total : ''),
		(trim($nr_valor_2_total) != '' ? $nr_valor_2_total : ''),
		'<big><b>'.(trim($nr_valor_2_total) != '' ? number_format(($nr_valor_1_total/$nr_valor_2_total) * 100, 2, ',', '.').' %' : '').' </b></big>', 
		number_format($nr_meta_media, 2, ',', '.').' %', 
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