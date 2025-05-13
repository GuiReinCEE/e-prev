<?php
$body = array();
$head = array( 
	'#', $label_0, $label_1, $label_2, $label_3, ''
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

		$referencia = " Total de ".$item['ano_referencia'];
	}
	else
	{
		$link = anchor("indicador_plugin/exp_adesao_potencial/cadastro/".$item["cd_exp_adesao_potencial"], "editar");

		$referencia = $item['ano_referencia'];
	}
	
	$nr_meta         = $item["nr_meta"];
	$nr_resultado = $item['nr_resultado'];
	
	if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && trim($item['fl_media']) != 'S')
	{
		$contador_ano_atual++;
		$media_ano[] = $nr_resultado;
		
		$nr_meta_media = $nr_meta;
	}
	
	$body[] = array(
		$contador--,
		$referencia,
		(trim($nr_resultado) != '' ? number_format($nr_resultado, 2, ',', '.').' %' : ''),
		number_format($nr_meta, 2, ',', '.').' %',
		array(nl2br($observacao), 'text-align:"left"'), 
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
		'<big><b>'.(trim($nr_resultado ) != '' ? number_format($nr_resultado , 2, ',', '.').' %' : '').' </b></big>', 
		number_format($nr_meta, 2, ',', '.').' %', 
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