<?php
$body=array();
$head = array( 
	'#', $label_0, "", $label_1, $label_4, $label_5, ''
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

$contador_ano_atual    = 0;
$contador              = sizeof($collection);
$a_data                = array(0, 0);
$nr_venda_total        = 0;
$nr_ingresso_total     = 0;
$nr_desligamento_total = 0;
$nr_meta_total         = 0;

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
		$link = anchor("indicador_plugin/exp_venda/cadastro/".$item["cd_exp_venda"], "editar");
		$referencia = $item['mes_referencia'];
	}
	
	$nr_venda        = $item['nr_venda'];
	$nr_ingresso     = $item['nr_ingresso'];
	$nr_desligamento = $item['nr_desligamento'];
	$nr_meta         = $item['nr_meta'];
	$observacao      = $item["observacao"];
	
	if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && trim($item['fl_media']) != 'S')
	{
		$contador_ano_atual++;
		$nr_venda_total        += $nr_venda;
		$nr_ingresso_total     += $nr_ingresso;
		$nr_desligamento_total += $nr_desligamento;
		$nr_meta_total         += $nr_meta;
	}
		
	$body[] = array(
		$contador--,
		$referencia,
		indicador_status($item["fl_meta"], $item["fl_direcao"]),
		(trim($nr_venda) != '' ? number_format($nr_venda, 2, ',', '.') : ''),
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
		'<big><b>'.(trim($nr_venda_total) != '' ? number_format($nr_venda_total, 2, ',', '.') : '').'</b></big>',
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