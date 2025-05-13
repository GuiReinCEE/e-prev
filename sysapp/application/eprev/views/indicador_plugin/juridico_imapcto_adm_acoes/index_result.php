<?php
$body = array();
$head = array( 
    '#', $label_0, $label_1, $label_2, $label_3, $label_4, $label_6, ''
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
$nr_valor_1_total     = 0;
$nr_valor_2_total     = 0;
$nr_meta_total     = 0;
$nr_resultado_f_total = 0;
$nr_meta              = 0;

foreach($collection as $item)
{
    $a_data     = explode("/", $item['mes_referencia']);

    if(trim($item['fl_media']) == 'S')
    {
        $link = '';

        $referencia = " Resultado de " . $item['ano_referencia'];
    }
    else
    {
        $link = anchor("indicador_plugin/juridico_imapcto_adm_acoes/cadastro/".$item["cd_juridico_imapcto_adm_acoes"], "editar");

        $referencia = $item['mes_referencia'];
    }

    $nr_valor_1      = $item["nr_valor_1"];
    $nr_valor_2      = $item["nr_valor_2"];
    $nr_percentual_f = $item["nr_percentual_f"];
    $nr_meta         = $item["nr_meta"];
    $observacao      = $item["observacao"];

    if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && trim($item['fl_media']) != 'S')
    {
        $contador_ano_atual++;

        $nr_resultado_f_total += $nr_percentual_f;

        $nr_valor_1_total += $item["nr_valor_1"];
        $nr_valor_2_total += $item["nr_valor_2"];
        $nr_meta_total += $item["nr_meta"];
    }

    $body[] = array(
        $contador--,
        $referencia,
        (trim($nr_valor_1) != '' ? number_format($nr_valor_1,2,',','.') : ''),
        (trim($nr_valor_2) != '' ? number_format($nr_valor_2,0,',','.') : ''),
        (trim($nr_percentual_f) != '' ? number_format($nr_percentual_f,2,',','.') : ''),
        (trim($nr_meta) != '' ? number_format($nr_meta,2,',','.') : ''),
        array($observacao, 'text-align:"left"'), 
        $link 
    );
}

$nr_resultado_f_total = ($contador_ano_atual > 0 ? (($nr_resultado_f_total / $contador_ano_atual)) : 0);

$body[] = array(
    0,
    '<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>',
    '<b>'.number_format($nr_valor_1_total,2,',','.').'</b>',
    '<b>'.number_format($nr_valor_2_total,0,',','.').'</b>',
    '<b>'.number_format($nr_resultado_f_total,2,',','.').'</b>',
    '<b>'.number_format($nr_meta,2,',','.').'</b>',
    '',
    '',
);

echo "<input type='hidden' id='mes_input' name='mes_input' value='".$a_data[0]."' />";
echo "<input type='hidden' id='contador_input' name='contador_input' value='".$contador_ano_atual."' />";

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();

?>