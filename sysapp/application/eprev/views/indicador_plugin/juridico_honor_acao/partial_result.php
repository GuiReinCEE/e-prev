<?php
$body = array();
$head = array( 
    "#", $label_0, $label_1, $label_2, $label_3, $label_4, $label_6, ""
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

$contador_ano_atual       = 0;
$contador                 = count($collection);
$a_data                   = array(0, 0);
$vl_honorario_total       = 0;
$vl_honorario_medio_total = 0;
$nr_meta                  = 0;

foreach($collection as $item)
{
    $a_data = explode("/", $item['mes_referencia']);
	$nr_meta = $item['nr_meta'];

    if(trim($item['fl_media']) == 'S')
    {
        $link = '';
        $referencia = " Resultado de " . $item['ano_referencia'];
    }
    else
    {
        $link = anchor("indicador_plugin/juridico_honor_acao/detalhe/".$item["cd_juridico_honor_acao"], "editar");
        $referencia = $item['mes_referencia'];
    }

    if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && trim($item['fl_media']) != 'S')
    {
        $contador_ano_atual++;
        $vl_honorario_total       += $item["vl_honorario"];
        $vl_honorario_medio_total += $item["vl_honorario_medio"];
    }

    $body[] = array(
		$contador--,
        $referencia,
        (trim($item["vl_honorario"]) != '' ? number_format($item["vl_honorario"],2,',','.') : ''),
        (trim($item["qt_acoes"]) != '' ? number_format($item["qt_acoes"],0,',','.') : ''),
        (trim($item["vl_honorario_medio"]) != '' ? number_format($item["vl_honorario_medio"],2,',','.') : ''),
        number_format($item["nr_meta"],2,',','.'),
        array($item["observacao"], 'text-align:left'), 
        $link 
    );
}

$vl_honorario_medio_total = ($vl_honorario_medio_total / ($contador_ano_atual == 0 ? 1 : $contador_ano_atual));

$body[] = array(
    0,
    '<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>',
    '<b>'.number_format($vl_honorario_total,2,',','.').'</b>',
    '',
    '<b>'.number_format($vl_honorario_medio_total,2,',','.').'</b>',
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
$grid->col_oculta = Array(0);
echo $grid->render();
?>