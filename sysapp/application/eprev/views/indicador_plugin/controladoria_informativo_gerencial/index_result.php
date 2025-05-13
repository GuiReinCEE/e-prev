<?php
$body=array();
$head = array( 
	'#', $label_0, $label_1, $label_2, $label_3, $label_4, $label_5, $label_6, $label_8, $label_7, ''
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
		$link = anchor("indicador_plugin/controladoria_informativo_gerencial/cadastro/".$item["cd_controladoria_informativo_gerencial"], "editar");

		$referencia = $item['mes_referencia'];
	}
	
	$nr_meta = $item['nr_meta'];


	if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && trim($item['fl_media']) != 'S')
	{
		$contador_ano_atual++;
		$media_ano[] = $item["nr_satisfacao"];
	}
		
	$body[] = array(
		$contador--,
		$referencia,
		(trim($item["nr_repondente"]) != '' ? intval($item["nr_repondente"]) : ''),
		(trim($item["nr_clareza"]) != '' ? number_format($item["nr_clareza"], 2, ',', '.').' %' : ''),
		(trim($item["nr_exatidao"]) != '' ? number_format($item["nr_exatidao"], 2, ',', '.').' %' : ''),
		(trim($item["nr_tempestividade"]) != '' ? number_format($item["nr_tempestividade"], 2, ',', '.').' %' : ''),
		(trim($item["nr_relevancia"]) != '' ? number_format($item["nr_relevancia"], 2, ',', '.').' %' : ''),
		(trim($item["nr_satisfacao"]) != '' ? number_format($item["nr_satisfacao"], 2, ',', '.').' %' : ''),
		number_format($nr_meta, 2, ',', '.').' %',
		array($item["observacao"], 'text-align:"left"'), 
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
		'',
		'',
		'<big><b>'.app_decimal_para_php( $media ).' %</b></big>',
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