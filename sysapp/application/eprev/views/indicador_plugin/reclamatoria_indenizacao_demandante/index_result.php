<?php
$body = array();
$head = array( 
    "#", $label_0, $label_1, $label_3,  $label_5, $label_7, $label_6, $label_9, ""
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
$vl_indenizacao_total            = 0;
$nr_liquidada_total              = 0;
$nr_demandante_total             = 0;
$vl_indenizacao_liquidada_total  = 0;
$vl_indenizacao_demandante_total = 0;
$vl_resultado_total              = 0;
$nr_meta                         = 0;

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
        $link = anchor("indicador_plugin/reclamatoria_indenizacao_demandante/cadastro/".$item["cd_reclamatoria_indenizacao_demandante"], "editar");
        $referencia = $item['mes_referencia'];
    }

    if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && trim($item['fl_media']) != 'S')
    {
        $contador_ano_atual++;
        $vl_indenizacao_total  += $item["vl_indenizacao"];
		$nr_liquidada_total    += $item["nr_liquidada"];
		$nr_demandante_total   += $item["nr_demandante"];	 	
    }

    $body[] = array(
		$contador--,
        $referencia,
        (trim($item["vl_indenizacao"]) != '' ? number_format($item["vl_indenizacao"],2,',','.') : ''),
        (trim($item["nr_demandante"]) != '' ? number_format($item["nr_demandante"],0,',','.') : ''),
        (trim($item["vl_indenizacao_demandante"]) != '' ? number_format($item["vl_indenizacao_demandante"],2,',','.') : ''),
        (trim($item["nr_resultado"]) != '' ? number_format($item["nr_resultado"],2,',','.') : ''),
        number_format($item["nr_meta"],2,',','.'),
        array($item["observacao"], 'text-align:left'), 
        $link 
    );
}

$vl_indenizacao_liquidada_total  = ($vl_indenizacao_total / ($nr_liquidada_total == 0 ? 1 : $nr_liquidada_total));
$vl_indenizacao_demandante_total = ($vl_indenizacao_total / ($nr_demandante_total == 0 ? 1 : $nr_demandante_total));
$vl_resultado_total              = ($vl_indenizacao_total > 0 ? ($vl_indenizacao_demandante_total/$vl_indenizacao_total)*100 : 0);

$body[] = array(
    0,
    '<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>',
    '<b>'.number_format($vl_indenizacao_total,2,',','.').'</b>',
    '<b>'.number_format($nr_demandante_total,0,',','.').'</b>',
    '<b>'.number_format($vl_indenizacao_demandante_total,2,',','.').'</b>',
    '<b>'.number_format($vl_resultado_total,2,',','.').'</b>',
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