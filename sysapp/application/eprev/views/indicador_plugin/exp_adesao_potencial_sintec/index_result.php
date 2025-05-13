<?php
$body = array();
$head = array( 
	'#', $label_0, $label_1, $label_2, $label_3, $label_4, $label_5, $label_6,  ''
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

$nr_valor_1_resultado    = 0;
$nr_valor_2_resultado    = 0;
$nr_valor_3_resultado    = 0;
$nr_meta_resultado       = 0;
$nr_percentual_resultado = 0;

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
		$link = anchor("indicador_plugin/exp_adesao_potencial_sintec/cadastro/".$item["cd_exp_adesao_potencial_sintec"], "editar");

		$referencia = $item['ano_referencia'];
	}
	
	$nr_meta         = $item["nr_meta"];
	$nr_percentual_f = $item['nr_percentual_f'];
	
	if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && trim($item['fl_media']) != 'S')
	{
		$contador_ano_atual++;
		$media_ano[] = $nr_percentual_f;

		$nr_valor_1_resultado    = $item["nr_valor_1"];
		$nr_valor_2_resultado    = $item["nr_valor_2"];
		$nr_valor_3_resultado    = $item["nr_valor_3"];
		$nr_meta_resultado       = $item["nr_meta"];
		$nr_percentual_resultado = $item["nr_percentual_f"];
	}
	
	$body[] = array(
		$contador--,
		$referencia,
		(trim($item["nr_valor_3"]) != '' ? number_format($item["nr_valor_3"], 0, ',', '.') : ''),
		(trim($item["nr_valor_1"]) != '' ? number_format($item["nr_valor_1"], 0, ',', '.') : ''),
		(trim($item["nr_valor_2"]) != '' ? number_format($item["nr_valor_2"], 0, ',', '.') : ''),
		(trim($nr_percentual_f) != '' ? number_format($nr_percentual_f, 2, ',', '.').' %' : ''),
		number_format($nr_meta, 2, ',', '.').' %',
		array($observacao, 'text-align:"left"'), 
		$link 
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