<?php
$body = array();
$head = array( 
    "#", $label_0, $label_1, $label_2, $label_3, $label_4, ""
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

echo '<div style="text-align:center;">'.br().anchor_popup("indicador/apresentacao/detalhe/".intval( $tabela[0]['cd_indicador_tabela'] ), '[Visualizar Apresenta��o]', $ar_janela)."</div>";

$contador_ano_atual = 0;
$contador           = count($collection);
$a_data             = array(0, 0);
$nr_nova_total      = 0;
$nr_encerrada_total = 0;
$nr_meta            = 0;

foreach($collection as $item)
{
    $a_data = explode("/", $item['mes_referencia']);
	$nr_meta = $item['nr_meta'];

    if(trim($item['fl_media']) == 'S')
    {
        $link = '';
        $referencia = "M�dia de ".$item['ano_referencia'];
    }
    else
    {
        $link = anchor("indicador_plugin/juridico_num_acoes_jud/detalhe/".$item["cd_juridico_num_acoes_jud"], "editar");
        $referencia = $item['mes_referencia'];
    }

    if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && trim($item['fl_media']) != 'S')
    {
        $contador_ano_atual++;
        $nr_nova_total      += $item["nr_nova"];
        $nr_encerrada_total += $item["nr_encerrada"];
    }

    $body[] = array(
		$contador--,
        $referencia,
        (trim($item["nr_nova"]) != '' ? number_format($item["nr_nova"],0,',','.') : ''),
        (trim($item["nr_encerrada"]) != '' ? number_format($item["nr_encerrada"],0,',','.') : ''),
        number_format($item["nr_meta"],0,',','.'),
        array($item["observacao"], 'text-align:left'), 
        $link 
    );
}


$nr_nova_media     = ($nr_nova_total / ($contador_ano_atual == 0 ? 1 : $contador_ano_atual));
$nr_encerrada_media = ($nr_encerrada_total / ($contador_ano_atual == 0 ? 1 : $contador_ano_atual));

$body[] = array(
    0,
    '<b>M�dia de '.intval($tabela[0]['nr_ano_referencia']).'</b>',
    '<b>'.number_format($nr_nova_media,0,',','.').'</b>',
    '<b>'.number_format($nr_encerrada_media,0,',','.').'</b>',
    '<b>'.number_format($nr_meta,0,',','.').'</b>',
    '',
    '',
);

echo "<input type='hidden' id='mes_input' name='mes_input' value='".$a_data[0]."' />";
echo "<input type='hidden' id='contador_input' name='contador_input' value='".$contador_ano_atual."' />";

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
$grid->col_oculta = Array(0);
echo $grid->render();
?>