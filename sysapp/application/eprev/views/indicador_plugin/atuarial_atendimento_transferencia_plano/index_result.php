<?php
$body = array();
$head = array( 
	'#', $label_0, $label_2, $label_1, $label_3, $label_4, $label_6, ''
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

$nr_tarefas_result     = 0;
$nr_realizadas_result  = 0;
$nr_meta_result        = 0;
$nr_resultado_result   = 0;

foreach($collection as $item)
{
	$a_data     = explode("/", $item['mes_referencia']);
	$ds_observacao = $item["ds_observacao"];
	
	if(trim($item['fl_media']) == 'S')
	{
		$link = '';

		$referencia = " Total de ".$item['ano_referencia'];
	}
	else
	{
		$link = anchor("indicador_plugin/atuarial_atendimento_transferencia_plano/cadastro/".$item["cd_atuarial_atendimento_transferencia_plano"], "editar");

		$referencia = $item['mes_referencia'];
	}
	
	$nr_tarefas      = $item["nr_tarefas"];
	$nr_realizadas   = $item["nr_realizadas"];
	$nr_meta         = $item["nr_meta"];
	$nr_resultado    = $item['nr_resultado'];
	
	if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && trim($item['fl_media']) != 'S')
	{
		$contador_ano_atual++;
		$media_ano[] = $nr_resultado;
		
		$nr_tarefas_result     += $item["nr_tarefas"];
		$nr_realizadas_result  += $item["nr_realizadas"];
		$nr_meta_result        = $item["nr_meta"];
		
	}
	
	$body[] = array(
		$contador--,
		$referencia,
		(trim($nr_tarefas) != '' ? $nr_tarefas : ''),
		(trim($nr_realizadas) != '' ? $nr_realizadas : ''),
		(trim($nr_resultado) != '' ? number_format($nr_resultado, 2, ',', '.').' %' : ''),
		number_format($nr_meta, 2, ',', '.').' %',
		array($ds_observacao, 'text-align:"left"'), 
		$link 
	);
}

if(sizeof($media_ano) >0)
{
	$nr_resultado_result  = 100;
			
	if(floatval($nr_tarefas_result) > 0)
	{
		$nr_resultado_result  = ($nr_realizadas_result / $nr_tarefas_result) * 100;
	}
	
	foreach($media_ano as $valor)
	{
		$media += $valor;
	}

	$body[] = array(
		0, 
		'<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>', 
		(trim($nr_tarefas_result) != '' ? $nr_tarefas_result : ''),
		(trim($nr_realizadas_result) != '' ? $nr_realizadas_result : ''),
		'<big><b>'.(trim($nr_resultado_result) != '' ? number_format($nr_resultado_result, 2, ',', '.').' %' : '').' </b></big>', 
		number_format($nr_meta_result, 2, ',', '.').' %', 
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