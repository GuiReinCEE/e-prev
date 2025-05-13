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

$contador_ano_atual = 0;
$contador = sizeof($collection);
$media_ano = array();

$a_data = array(0, 0);

foreach( $collection as $item )
{
	$a_data = explode( "/", $item['mes_referencia'] );
	$observacao = $item["observacao"];

	if(trim($item['fl_media']) == 'S')
	{
		$link = '';

		$referencia = " Média de " . $item['ano_referencia'];		
	}
	else
	{
		$link = anchor("indicador_plugin/ri_sat_eventos_internos/cadastro/" . $item["cd_ri_sat_eventos_internos"], "editar");

		$referencia = $item['mes_referencia'];
	}
	
	$nr_valor_1      = $item["nr_valor_1"];
	$nr_valor_2      = $item["nr_valor_2"];
	$nr_meta         = $item['nr_meta'];
	$nr_percentual_f = $item['nr_percentual_f'];

	if( intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && $item['fl_media']!='S' )
	{
		$contador_ano_atual++;
		$media_ano[] = $nr_percentual_f;
	}

	$body[] = array(
		$contador--, 
		$referencia, 
		number_format($nr_valor_1, 0, ',', '.'), 
		number_format($nr_valor_2, 0, ',', '.'), 
		number_format($nr_percentual_f, 2, ',', '.').' %', 
		number_format($nr_meta,2,',','.').' %', 
		array($observacao, 'text-align:"left"'), 
		($item['fl_editar'] == "S" ? $link : "")
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
