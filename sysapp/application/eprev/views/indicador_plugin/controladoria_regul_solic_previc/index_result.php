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
		$link = anchor("indicador_plugin/controladoria_regul_solic_previc/cadastro/" . $item["cd_controladoria_regul_solic_previc"], "editar");

		$referencia = $item['mes_referencia'];
	}
	
	$nr_valor_1      = $item["nr_valor_1"];
	$nr_valor_2      = $item["nr_valor_2"];
	$nr_meta_f       = $item["nr_meta_f"];
	$nr_percentual_f = $item['nr_percentual_f'];
	
	if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && trim($item['fl_media']) != 'S')
	{
		$contador_ano_atual++;
		$media_ano[] = $nr_percentual_f;
		
		$nr_meta_media = $nr_meta;
	}
	
	$body[] = array(
		$contador--,
		$item["ds_evento"],
		(trim($nr_valor_1) != '' ? $nr_valor_1 : ''),
		(trim($nr_valor_2) != '' ? $nr_valor_2 : ''),
		number_format($nr_meta_f, 0, ',', '.'),
		(trim($nr_percentual_f) != '' ? (intval($nr_percentual_f) == 0 ? 'Não Atingido' : 'Atingido') : ''),
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