<?php
$body=array();
$head = array( 
	'#', $label_0, "", $label_1, $label_2, $label_4, ''
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

$contador_ano_atual  = 0;
$contador            = sizeof($collection);
$a_data              = array(0, 0);
$nr_contratado_total = 0;
$nr_meta_total       = 0;


foreach($collection as $item)
{
	$a_data = explode("/", $item['mes_referencia']);
	
	if(trim($item['fl_media']) == 'S')
	{
		$link = '';
		$referencia = "Total de " . $item['ano_referencia'];
	}
	else
	{
		$link = anchor("indicador_plugin/exp_valor_contratado/cadastro/".$item["cd_exp_valor_contratado"], "editar");
		$referencia = $item['mes_referencia'];
	}
	
	$nr_contratado       = $item['nr_contratado'];
	$nr_meta             = $item['nr_meta'];
	$observacao          = $item["observacao"];
	
	if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && trim($item['fl_media']) != 'S')
	{
		$contador_ano_atual++;
		$nr_contratado_total += $nr_contratado;
		$nr_meta_total += $nr_meta;
	}
		
	$body[] = array(
		$contador--,
		$referencia,
		indicador_status($item["fl_meta"], $item["fl_direcao"]),
		(trim($nr_contratado) != '' ? number_format($nr_contratado, 2, ',', '.') : ''),
		(trim($nr_meta) != '' ? number_format($nr_meta, 2, ',', '.') : ''),
		array(nl2br($observacao), 'text-align:"left"'), 
		$link 
	);
}

if($contador_ano_atual > 0)
{
	$body[] = array(
		0, 
		'<b>Total de '.intval($tabela[0]['nr_ano_referencia']).'</b>', 
		'', 
		'<big><b>'.(trim($nr_contratado_total) != '' ? number_format($nr_contratado_total, 2, ',', '.') : '').'</b></big>',
		'<big><b>'.(trim($nr_meta_total) != '' ? number_format($nr_meta_total, 2, ',', '.') : '').'</b></big>',
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