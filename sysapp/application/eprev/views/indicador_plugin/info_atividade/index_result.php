<?php
$body=array();
$head = array( 
	'#', $label_0, "", $label_1, $label_2, $label_3, $label_4, $label_5, $label_6, $label_7, $label_8, ''
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

echo '<div style="text-align:center;">'.br().anchor_popup("indicador/apresentacao/detalhe/".intval($tabela[0]['cd_indicador_tabela']), '[Visualizar Apresentação]', $ar_janela)."</div>";

$contador_ano_atual   = 0;
$contador             = sizeof($collection);
$a_data               = array(0, 0);
$media_ano            = array();
$media                = 0;
$nr_abertas_acu_f     = 0;
$nr_atendidas_acu_f   = 0;
$nr_percentual_acu_f  = 0;

foreach($collection as $item)
{
	$a_data = explode("/", $item['mes_referencia']);
	
	if(trim($item['fl_media']) == 'S')
	{
		$link = '';

		$referencia = " Média de " . $item['ano_referencia'];
	}
	else
	{
		$link = anchor("indicador_plugin/info_atividade/cadastro/".$item["cd_info_atividade"], "editar");

		$referencia = $item['mes_referencia'];
	}
	
	$nr_abertas_mes      = $item['nr_abertas_mes'];
	$nr_atendidas_mes    = $item['nr_atendidas_mes'];
	$nr_meta             = $item['nr_meta'];
	$nr_percentual_mes_f = $item['nr_percentual_mes_f'];
	$observacao          = $item["observacao"];
	
	if(trim($item['nr_abertas_acu_f']) == '')
	{
		$nr_abertas_acu_f += $nr_abertas_mes;
	}
	else
	{
		$nr_abertas_acu_f = floatval($item['nr_abertas_acu_f']);
	}
	
	if(trim($item['nr_atendidas_acu_f']) == '')
	{
		$nr_atendidas_acu_f += $nr_atendidas_mes;
	}
	else
	{
		$nr_atendidas_acu_f = floatval( $item['nr_atendidas_acu_f'] );
	}
	
	if(trim($item['nr_percentual_acu_f']) == '')
	{
		if(floatval($nr_abertas_acu_f) > 0)
		{
			$nr_percentual_acu_f = (floatval($nr_atendidas_acu_f) / floatval($nr_abertas_acu_f) )*100;
		}
		else
		{
			$nr_percentual_acu_f = 0;
		}
	}
	else
	{
		$nr_percentual_acu_f = floatval($item['nr_percentual_acu_f']);
	}
	
	if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && trim($item['fl_media']) != 'S')
	{
		$contador_ano_atual++;
		$media_ano[] = $nr_percentual_mes_f;
	}
		
	$body[] = array(
		$contador--,
		$referencia,
		indicador_status($item["fl_meta"], $item["fl_direcao"]),
		(trim($nr_abertas_mes) != '' ? number_format(intval($nr_abertas_mes), 0, ',', '.') : ''),
		(trim($nr_atendidas_mes) != '' ? number_format(intval($nr_atendidas_mes), 0, ',', '.') : ''),
		(trim($nr_percentual_mes_f) != '' ? number_format($nr_percentual_mes_f, 2, ',', '.').' %' : ''),
		(trim($nr_abertas_acu_f) != '' ? intval($nr_abertas_acu_f) : ''),
		(trim($nr_atendidas_acu_f) != '' ? intval($nr_atendidas_acu_f) : ''),
		(trim($nr_percentual_acu_f) != '' ? number_format($nr_percentual_acu_f, 2, ',', '.').' %' : ''),
		number_format($nr_meta, 2, ',', '.').' %',
		array($observacao, 'text-align:"left"'), 
		$link 
	);
}

if(sizeof($media_ano) >0)
{
	$media = 0;
	
	foreach( $media_ano as $valor )
	{
		$media += $valor;
	}

	$media = number_format( ($media / sizeof($media_ano)), 2 );
	
	$body[] = array(
		0, 
		'<b>Média de '.intval($tabela[0]['nr_ano_referencia']).'</b>', 
		'', 
		'', 
		'', 
		'<big><b>'.app_decimal_para_php( $media ).' %</b></big>',
		'',
		'',
		'',
		'<big><b>'.number_format($nr_meta, 2, ',', '.').' %</b></big>',
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