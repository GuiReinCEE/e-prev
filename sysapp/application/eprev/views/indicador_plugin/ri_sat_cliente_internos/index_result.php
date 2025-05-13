<?php
$body = array();
$head = array( 
	'#', $label_0, $label_1, $label_2, $label_3, $label_4, ''
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
$media_ano            = array();
$a_data               = array(0, 0);
$nr_tot_ano_visita    = 0;
$nr_tot_ano_contato   = 0;
$nr_tot_ano_inscricao = 0;
$nr_meta              = 0;

foreach($collection as $item)
{
	$a_data     = explode("/", $item['mes_referencia']);
	$observacao = $item["observacao"];

	if(trim($item['fl_media']) == 'S')
	{
		$link = '';

		$referencia = " Resultado de " . $item['ano_referencia'];
	}
	else
	{
		$link = anchor("indicador_plugin/ri_sat_cliente_internos/cadastro/" . $item["cd_ri_sat_cliente_internos"], "editar");

		$referencia = $item['mes_referencia'];
	}
	
	$nr_valor_1      = $item["nr_valor_1"];
	$nr_valor_2      = $item["nr_valor_2"];
	$nr_percentual_f = $item["nr_percentual_f"];
	$nr_meta         = $item["nr_meta"];

	if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && trim($item['fl_media']) != 'S')
	{
		$contador_ano_atual++;
		
		$nr_tot_ano_visita    += $item["nr_valor_1"];
		$nr_tot_ano_contato   += $item["nr_valor_2"];
	}

	$body[] = array(
		$contador--,
		$referencia,
		(trim($nr_valor_1) != '' ? $nr_valor_1 : ''),
		(trim($nr_valor_2) != '' ? $nr_valor_2 : ''),
		(trim($nr_percentual_f) != '' ? number_format($nr_percentual_f,2,',','.').' %' : ''),
		number_format($nr_meta,2,',','.').' %',
		$link 
	);
}

$resultado_ano = ($nr_tot_ano_visita > 0 ? (($nr_tot_ano_contato / $nr_tot_ano_visita) * 100) : 0);

$body[] = array(
	0,
	'<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>',
	'<b>'.number_format($nr_tot_ano_visita,0,',','.').'</b>',
	'<b>'.number_format($nr_tot_ano_contato,0,',','.').'</b>',
	'<b>'.number_format($resultado_ano,2,',','.').'%</b>',
	'<b>'.number_format($nr_meta,2,',','.').'%</b>',
	'',
);

echo "<input type='hidden' id='mes_input' name='mes_input' value='".$a_data[0]."' />";
echo "<input type='hidden' id='contador_input' name='contador_input' value='".$contador_ano_atual."' />";

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();


?>
