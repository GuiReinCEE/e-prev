<?php
$body=array();
$head = array( 
	"#",$label_0, $label_1, $label_2, $label_3, $label_4, $label_5, ''
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
$resultado_valor_1 = 0;
$resultado_valor_2 = 0;	
$ultima_meta = 0;

$a_data = array(0, 0);

foreach( $collection as $item )
{
	$a_data = explode( "/", $item['mes_referencia'] );
	$observacao = $item["observacao"];

	if(trim($item['fl_media']) == 'S')
	{
		$link = '';
	
		$referencia = " Total de " . $item['ano_referencia'];
	}
	else
	{
		$link = anchor("indicador_plugin/igp_sat_participantes_pos_venda/cadastro/".$item["cd_igp_sat_participantes_pos_venda"], "editar");

		$referencia = $item['mes_referencia'];
	}
	
	$nr_valor_1   = $item['nr_valor_1'];
	$nr_valor_2   = $item['nr_valor_2'];
	$nr_resultado = $item['nr_resultado'];
	$nr_meta      = $item['nr_meta'];

	if( intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && $item['fl_media']!='S' )
	{
		$contador_ano_atual++;
		$resultado_valor_1 += $nr_valor_1;
		$resultado_valor_2 += $nr_valor_2;
		$ultima_meta = $item['nr_meta'];
		
	}

	$body[] = array(
		$contador--,
		$referencia,
		number_format($nr_valor_1,0,',','.'),
		number_format($nr_valor_2,0,',','.'),
		number_format($nr_resultado,2,',','.').'%',
		number_format($nr_meta,2,',','.').'%',
		array($observacao, 'text-align:"left"'),
		$link 
	);
}

if(intval($contador_ano_atual) > 0)
{
	$body[] = array(
		0,
		'<b>Total do '.intval($tabela[0]['nr_ano_referencia']).'</b>',
		'<b>'.number_format($resultado_valor_1,0,',','.').'</b>',
		'<b>'.number_format($resultado_valor_2,0,',','.').'</b>',
		'<b>'.number_format(($resultado_valor_2 / $resultado_valor_1)*100,2,',','.').'%</b>',
		'<b>'.number_format($ultima_meta,2,',','.').'%</b>',
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
