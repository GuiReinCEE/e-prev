<?php
$body=array();
$head = array( 
	"#",$label_0, $label_1, $label_2, $label_3, $label_4, $label_5, $label_6, $label_8, $label_7, ''
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
$nr_valor_1_total   = 0;
$nr_valor_2_total   = 0;
$nr_valor_3_total   = 0;
$nr_valor_4_total   = 0;
$nr_media_total     = 0;
$nr_resultado_total = 0;

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
		$link = anchor("indicador_plugin/igp_retorno_colaborador/cadastro/".$item["cd_igp_retorno_colaborador"], "editar");

		$referencia = $item['mes_referencia'];
	}
	
	$nr_valor_1   = $item['nr_valor_1'];
	$nr_valor_2   = $item['nr_valor_2'];
	$nr_valor_3   = $item['nr_valor_3'];
	$nr_valor_4   = $item['nr_valor_4'];
	$nr_resultado = $item['nr_resultado'];
	$nr_meta      = $item['nr_meta'];

	$nr_percentual = 0;

	if($nr_resultado > 0)
	{
		$nr_percentual = ($nr_resultado / $nr_meta) * 100;
	}

	if( intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && $item['fl_media']!='S' )
	{
		$contador_ano_atual++;
		$nr_valor_1_total   += $nr_valor_1;
		$nr_valor_2_total   += $nr_valor_2;
		$nr_valor_3_total   += $nr_valor_3;
		$nr_valor_4_total   += $nr_valor_4;
		$nr_media_total     += $nr_meta;
		$nr_resultado_total += $nr_resultado;
	}

	$body[] = array(
		$contador--,
		$referencia,
		number_format($nr_valor_1,0),
		number_format($nr_valor_2,2,',','.'),
		number_format($nr_valor_3,2,',','.'),
		number_format($nr_valor_4,2,',','.'),
		number_format($nr_resultado,2,',','.'),
		number_format($nr_meta,2,',','.'),
		number_format($nr_percentual,2,',','.')." %",
		array(nl2br($observacao), 'text-align:"left"'),
		$link 
	);
}

if(intval($contador_ano_atual) > 0)
{
	$nr_percentual = 0;

	if($nr_resultado_total > 0)
	{
		$nr_percentual = ($nr_resultado_total / $nr_media_total) * 100;
	}

	$body[] = array(
		0,
		'<b>Acumulado do '.intval($tabela[0]['nr_ano_referencia']).'</b>',
		'<b>'.number_format($nr_valor_1_total/$contador_ano_atual,2,',','.').'</b>',
		'<b>'.number_format($nr_valor_2_total,2,',','.').'</b>',
		'<b>'.number_format($nr_valor_3_total,2,',','.').'</b>',
		'<b>'.number_format($nr_valor_4_total,2,',','.').'</b>',
		'<b>'.number_format($nr_resultado_total,2,',','.').'</b>',
		'<b>'.number_format($nr_media_total,2,',','.').'</b>',
		'<b>'.number_format($nr_percentual,2,',','.').' %</b>',
		'',
		''
	);

	$body[] = array(
		0,
		'<b>Média do '.intval($tabela[0]['nr_ano_referencia']).'</b>',
		'<b>'.number_format($nr_valor_1_total/$contador_ano_atual,2,',','.').'</b>',
		'<b>'.number_format($nr_valor_2_total/$contador_ano_atual,2,',','.').'</b>',
		'<b>'.number_format($nr_valor_3_total/$contador_ano_atual,2,',','.').'</b>',
		'<b>'.number_format($nr_valor_4_total/$contador_ano_atual,2,',','.').'</b>',
		'<b>'.number_format($nr_resultado_total/$contador_ano_atual,2,',','.').'</b>',
		'<b>'.number_format($nr_media_total/$contador_ano_atual,2,',','.').'</b>',
		'<b>'.number_format($nr_percentual,2,',','.').' %</b>',
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
