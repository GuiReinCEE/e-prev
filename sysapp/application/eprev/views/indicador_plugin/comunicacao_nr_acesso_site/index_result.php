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

$nr_valor_1_total = 0;
$nr_meta_total = 0;

foreach($collection as $item)
{
	$a_data     = explode("/", $item['mes_referencia']);
	$observacao = $item["observacao"];
	
	if(trim($item['fl_media']) == 'S')
	{
		$link = '';

		$referencia = " Média de ".$item['ano_referencia'];
	}
	else
	{
		$link = anchor("indicador_plugin/comunicacao_nr_acesso_site/cadastro/".$item["cd_comunicacao_nr_acesso_site"], "editar");

		$referencia = $item['mes_referencia'];
	}
	
	$nr_valor_1      = $item["nr_valor_1"];
	$nr_meta         = $item["nr_meta"];
	
	if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && trim($item['fl_media']) != 'S')
	{
		$contador_ano_atual++;
		
		$nr_meta_media = $nr_meta;

		$nr_valor_1_total += $nr_valor_1 ;
		$nr_meta_total += $nr_meta ;
	}
	
	$body[] = array(
		$contador--,
		$referencia,
		(trim($nr_valor_1) != '' ? $nr_valor_1 : ''),
		$nr_meta,
		array($observacao, 'text-align:"left"'), 
		$link 
	);
}

if($contador_ano_atual >0)
{
	
	foreach($media_ano as $valor)
	{
		$media += $valor;
	}

	$body[] = array(
		0, 
		'<b>Total de '.intval($tabela[0]['nr_ano_referencia']).'</b>', 
		'<big><b>'.$nr_valor_1_total.'</b></big>', 
		'<big><b>'.$nr_meta_total.'</b></big>', 
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