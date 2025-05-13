<?php
$body = array();
$head = array( 
    "#", $label_0, $label_1, $label_2, $label_3, $label_4, $label_5, $label_6, $label_7, $label_8, $label_9, $label_10, ""
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

$contador_ano_atual    = 0;
$contador              = count($collection);
$a_data                = array(0, 0);
$nr_laudo_total        = 0;
$nr_liquidacao_total   = 0;
$nr_total_total        = 0;
$nr_manifestacao_total = 0;
$vl_perito_total       = 0;
$vl_fundacao_total     = 0;
$vl_reversao_total     = 0;
$pr_reversao_total     = 0;
$nr_meta               = 0;

foreach($collection as $item)
{
    $a_data = explode("/", $item['mes_referencia']);
	$nr_meta = $item['nr_meta'];

    if(trim($item['fl_media']) == 'S')
    {
        $link = '';
        $referencia = "Total de ".$item['ano_referencia'];
    }
    else
    {
        $link = anchor("indicador_plugin/juridico_valor_per_fun/detalhe/".$item["cd_juridico_valor_per_fun"], "editar");
        $referencia = $item['mes_referencia'];
    }

    if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && trim($item['fl_media']) != 'S')
    {
        $contador_ano_atual++;
        $nr_laudo_total        += $item["nr_laudo"];
        $nr_liquidacao_total   += $item["nr_liquidacao"];
        $nr_total_total        += $item["nr_total"];
        $nr_manifestacao_total += $item["nr_manifestacao"];
        $vl_perito_total       += $item["vl_perito"];
        $vl_fundacao_total     += $item["vl_fundacao"];
        $vl_reversao_total     += $item["vl_reversao"];
		$pr_reversao_total     += $item["pr_reversao"];
    }

    $body[] = array(
		$contador--,
        $referencia,
        (trim($item["nr_laudo"]) != "" ? number_format($item["nr_laudo"],0,',','.') : ''),
        (trim($item["nr_liquidacao"])   != "" ? number_format($item["nr_liquidacao"],0,',','.') : ''),
        (trim($item["nr_total"])    != "" ? number_format($item["nr_total"],0,',','.') : ''),
        (trim($item["nr_manifestacao"])    != "" ? number_format($item["nr_manifestacao"],0,',','.') : ''),
        (trim($item["vl_perito"])  != "" ? number_format($item["vl_perito"],2,',','.') : ''),
        (trim($item["vl_fundacao"])    != "" ? number_format($item["vl_fundacao"],2,',','.') : ''),
        (trim($item["vl_reversao"])  != "" ? number_format($item["vl_reversao"],2,',','.') : ''),
        (trim($item["pr_reversao"])  != "" ? number_format($item["pr_reversao"],2,',','.')."%" : ''),
        (trim($item["nr_meta"])   != "" ? number_format($item["nr_meta"],0,',','.') : ''),
        array($item["observacao"], 'text-align:left'), 
        $link 
    );
}
$pr_reversao_resul = ($vl_reversao_total / (floatval($vl_perito_total) > 0 ? floatval($vl_perito_total) : 1) ) * 100;

#### TOTAL
$body[] = array(
    -1,
    '<b>Total de '.intval($tabela[0]['nr_ano_referencia']).'</b>',
    '<b>'.(trim($nr_laudo_total) != "" ? number_format($nr_laudo_total,0,',','.') : '').'</b>',
    '<b>'.(trim($nr_liquidacao_total) != "" ? number_format($nr_liquidacao_total,0,',','.') : '').'</b>',
    '<b>'.(trim($nr_total_total) != "" ? number_format($nr_total_total,0,',','.') : '').'</b>',
    '<b>'.(trim($nr_manifestacao_total) != "" ? number_format($nr_manifestacao_total,0,',','.') : '').'</b>',
    '<b>'.(trim($vl_perito_total) != "" ? number_format($vl_perito_total,2,',','.') : '').'</b>',
    '<b>'.(trim($vl_fundacao_total) != "" ? number_format($vl_fundacao_total,2,',','.') : '').'</b>',
    '<b>'.(trim($vl_reversao_total) != "" ? number_format($vl_reversao_total,2,',','.') : '').'</b>',
    '<b>'.(trim($pr_reversao_resul) != "" ? number_format($pr_reversao_resul,2,',','.')."%" : '').'</b>',
    '<b>'.(trim($nr_meta) != "" ? number_format($nr_meta,0,',','.') : '').'</b>',
    '',
    '',
);

#### ME´DIA
$body[] = array(
    0,
    '<b>Média de '.intval($tabela[0]['nr_ano_referencia']).'</b>',
    '<b>'.(trim($nr_laudo_total) != "" ? number_format(($nr_laudo_total / (intval($contador_ano_atual) > 0 ? intval($contador_ano_atual) : 1)),0,',','.') : '').'</b>',
    '<b>'.(trim($nr_liquidacao_total) != "" ? number_format(($nr_liquidacao_total / (intval($contador_ano_atual) > 0 ? intval($contador_ano_atual) : 1)),0,',','.') : '').'</b>',
    '<b>'.(trim($nr_total_total) != "" ? number_format(($nr_total_total / (intval($contador_ano_atual) > 0 ? intval($contador_ano_atual) : 1)),0,',','.') : '').'</b>',
    '<b>'.(trim($nr_manifestacao_total) != "" ? number_format(($nr_manifestacao_total / (intval($contador_ano_atual) > 0 ? intval($contador_ano_atual) : 1)),0,',','.') : '').'</b>',
    '<b>'.(trim($vl_perito_total) != "" ? number_format(($vl_perito_total / (intval($contador_ano_atual) > 0 ? intval($contador_ano_atual) : 1)),2,',','.') : '').'</b>',
    '<b>'.(trim($vl_fundacao_total) != "" ? number_format(($vl_fundacao_total / (intval($contador_ano_atual) > 0 ? intval($contador_ano_atual) : 1)),2,',','.') : '').'</b>',
    '<b>'.(trim($vl_reversao_total) != "" ? number_format(($vl_reversao_total / (intval($contador_ano_atual) > 0 ? intval($contador_ano_atual) : 1)),2,',','.') : '').'</b>',
    '<b>'.(trim($pr_reversao_total) != "" ? number_format(($pr_reversao_total / (intval($contador_ano_atual) > 0 ? intval($contador_ano_atual) : 1)),2,',','.')."%" : '').'</b>',
    '<b>'.(trim($nr_meta) != "" ? number_format($nr_meta,0,',','.') : '').'</b>',
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