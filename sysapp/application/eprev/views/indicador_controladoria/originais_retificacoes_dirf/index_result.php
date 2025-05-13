<?php
$body=array();
$head = array( 
	'#', $label_0, $label_1, $label_2, $label_3, $label_4, $label_5, $label_6, $label_7, $label_9, ''
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

$nr_original_total             = 0;
$nr_retificacao_1_total        = 0;
$nr_retificacao_2_total        = 0;
$nr_retificacao_3_total        = 0;
$nr_retificacao_4_total        = 0;
$nr_retificacao_5_total        = 0;
$nr_declaracoes_entregue_total = 0;
$nr_meta_ano                   = 0;

foreach($collection as $item)
{
	$a_data = explode("/", $item['mes_referencia']);
	
	if(trim($item['fl_media']) == 'S')
	{
		$link = '';

		$referencia = $item['ano_referencia'];
	}
	else
	{
		$link = anchor("indicador_controladoria/originais_retificacoes_dirf/cadastro/" . $item["cd_originais_retificacoes_dirf"], "editar");

		$referencia = $item['mes_referencia'];
	}
	
	$nr_original             = $item["nr_original"];
	$nr_retificacao_1        = $item["nr_retificacao_1"];
    $nr_retificacao_2        = $item["nr_retificacao_2"];
    $nr_retificacao_3        = $item["nr_retificacao_3"];
    $nr_retificacao_4        = $item["nr_retificacao_4"];
    $nr_retificacao_5        = $item["nr_retificacao_5"];
	$nr_declaracoes_entregue = $item["nr_declaracoes_entregue"];
	$nr_meta                 = $item["nr_meta"];
	$observacao              = $item["observacao"];
	
	if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && trim($item['fl_media']) != 'S')
	{
		$contador_ano_atual++;
        
        $nr_original_total             += $item["nr_original"];
        $nr_retificacao_1_total        += $item["nr_retificacao_1"];
        $nr_retificacao_2_total        += $item["nr_retificacao_2"];
        $nr_retificacao_3_total        += $item["nr_retificacao_2"];
        $nr_retificacao_4_total        += $item["nr_retificacao_2"];
        $nr_retificacao_5_total        += $item["nr_retificacao_2"];
        $nr_declaracoes_entregue_total += $item["nr_declaracoes_entregue"];
        $nr_meta_ano                    = $item["nr_meta"];
	}
		
	$body[] = array(
		$contador--,
		$referencia,
		(trim($nr_original) != '' ? intval($nr_original) : ''),
		(trim($nr_retificacao_1) != '' ? intval($nr_retificacao_1) : ''),
        (trim($nr_retificacao_2) != '' ? intval($nr_retificacao_2) : ''),
		(trim($nr_retificacao_3) != '' ? intval($nr_retificacao_3) : ''),
        (trim($nr_retificacao_4) != '' ? intval($nr_retificacao_4) : ''),
        (trim($nr_retificacao_5) != '' ? intval($nr_retificacao_5) : ''),
		(trim($nr_declaracoes_entregue) != '' ? intval($nr_declaracoes_entregue) : ''),
        (trim($nr_meta) != '' ? intval($nr_meta) : ''),
		$link 
	);
}

if(intval($contador_ano_atual) >0)
{	
	$body[] = array(
		0, 
		'<b>Total de '.intval($tabela[0]['nr_ano_referencia']).'</b>', 
		'<b>'.(trim($nr_original_total) != '' ? intval($nr_original_total) : '').'</b>', 
		'<b>'.(trim($nr_retificacao_1_total) != '' ? intval($nr_retificacao_1_total) : '').'</b>', 
        '<b>'.(trim($nr_retificacao_2_total) != '' ? intval($nr_retificacao_2_total) : '').'</b>', 
		'<b>'.(trim($nr_retificacao_3_total) != '' ? intval($nr_retificacao_3_total) : '').'</b>', 
        '<b>'.(trim($nr_retificacao_4_total) != '' ? intval($nr_retificacao_4_total) : '').'</b>', 
        '<b>'.(trim($nr_retificacao_5_total) != '' ? intval($nr_retificacao_5_total) : '').'</b>', 
		'<b>'.(trim($nr_declaracoes_entregue_total) != '' ? intval($nr_declaracoes_entregue_total) : '').'</b>', 
        '<b>'.(trim($nr_meta_ano) != '' ? intval($nr_meta_ano) : ''),
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
