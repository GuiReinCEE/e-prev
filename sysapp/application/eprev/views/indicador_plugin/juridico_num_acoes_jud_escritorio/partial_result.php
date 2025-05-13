<?php
$body = array();
$head = array( 
    "#", $label_0, $label_1, $label_2, $label_3, $label_4, $label_5, $label_6, $label_7, $label_8, $label_9, ""
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

$contador_ano_atual = 0;
$contador           = count($collection);
$a_data             = array(0, 0);
$nr_juchem_total    = 0;
$pr_juchem_total    = 0;
$nr_ribeiro_total   = 0;
$pr_ribeiro_total   = 0;
$nr_cenco_total     = 0;
$pr_cenco_total     = 0;
$nr_total_total     = 0;
$nr_meta            = 0;

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
        $link = anchor("indicador_plugin/juridico_num_acoes_jud_escritorio/detalhe/".$item["cd_juridico_num_acoes_jud_escritorio"], "editar");
        $referencia = $item['mes_referencia'];
    }

    if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && trim($item['fl_media']) != 'S')
    {
        $contador_ano_atual++;
		$nr_juchem_total  += $item["nr_juchem"];
		$pr_juchem_total  += $item["pr_juchem"];
		$nr_ribeiro_total += $item["nr_ribeiro"];
		$pr_ribeiro_total += $item["pr_ribeiro"];
		$nr_cenco_total   += $item["nr_cenco"];
		$pr_cenco_total   += $item["pr_cenco"];
		$nr_total_total   += $item["nr_total"];
    }

    $body[] = array(
		$contador--,
        $referencia,
        (trim($item["nr_juchem"])  != "" ? number_format($item["nr_juchem"],0,',','.') : ''),
        (trim($item["pr_juchem"])  != "" ? number_format($item["pr_juchem"],2,',','.')."%" : ''),
        (trim($item["nr_ribeiro"]) != "" ? number_format($item["nr_ribeiro"],0,',','.') : ''),
        (trim($item["pr_ribeiro"]) != "" ? number_format($item["pr_ribeiro"],2,',','.')."%" : ''),
        (trim($item["nr_cenco"])   != "" ? number_format($item["nr_cenco"],0,',','.') : ''),
        (trim($item["pr_cenco"])   != "" ? number_format($item["pr_cenco"],2,',','.')."%" : ''),
        (trim($item["nr_total"])   != "" ? number_format($item["nr_total"],0,',','.') : ''),
        '',
        array($item["observacao"], 'text-align:left'), 
        $link 
    );
}



#### TOTAL
$body[] = array(
    -1,
    '<b>Total de '.intval($tabela[0]['nr_ano_referencia']).'</b>',
    '<b>'.(trim($nr_juchem_total)  != "" ? number_format($nr_juchem_total,0,',','.') : '').'</b>',
    '<b>'.(trim($nr_juchem_total)  != "" ? number_format((($nr_juchem_total / (intval($nr_total_total) > 0 ? intval($nr_total_total) : 1)) * 100),2,',','.')."%" : '').'</b>',
    '<b>'.(trim($nr_ribeiro_total) != "" ? number_format($nr_ribeiro_total,0,',','.') : '').'</b>',
    '<b>'.(trim($nr_ribeiro_total) != "" ? number_format((($nr_ribeiro_total / (intval($nr_total_total) > 0 ? intval($nr_total_total) : 1)) * 100),2,',','.')."%" : '').'</b>',
    '<b>'.(trim($nr_cenco_total)   != "" ? number_format($nr_cenco_total,0,',','.') : '').'</b>',
    '<b>'.(trim($nr_cenco_total)   != "" ? number_format((($nr_cenco_total / (intval($nr_total_total) > 0 ? intval($nr_total_total) : 1)) * 100),2,',','.')."%" : '').'</b>',
    '<b>'.(trim($nr_total_total)   != "" ? number_format($nr_total_total,0,',','.') : '').'</b>',
    '<b>'.(trim($nr_meta) != "" ? number_format($nr_meta,0,',','.') : '').'</b>',
    '',
    ''
);


#### MEDIA
$body[] = array(
    0,
    '<b>Média de '.intval($tabela[0]['nr_ano_referencia']).'</b>',
    '<b>'.(trim($nr_juchem_total)  != "" ? number_format(($nr_juchem_total / (intval($contador_ano_atual) > 0 ? intval($contador_ano_atual) : 1)),0,',','.') : '').'</b>',
    '<b>'.(trim($pr_juchem_total)  != "" ? number_format(($pr_juchem_total / (intval($contador_ano_atual) > 0 ? intval($contador_ano_atual) : 1)),2,',','.')."%" : '').'</b>',
    '<b>'.(trim($nr_ribeiro_total) != "" ? number_format(($nr_ribeiro_total / (intval($contador_ano_atual) > 0 ? intval($contador_ano_atual) : 1)),0,',','.') : '').'</b>',
    '<b>'.(trim($pr_ribeiro_total) != "" ? number_format(($pr_ribeiro_total / (intval($contador_ano_atual) > 0 ? intval($contador_ano_atual) : 1)),2,',','.')."%" : '').'</b>',    
    '<b>'.(trim($nr_cenco_total)   != "" ? number_format(($nr_cenco_total / (intval($contador_ano_atual) > 0 ? intval($contador_ano_atual) : 1)),0,',','.') : '').'</b>',
    '<b>'.(trim($pr_cenco_total)   != "" ? number_format(($pr_cenco_total / (intval($contador_ano_atual) > 0 ? intval($contador_ano_atual) : 1)),2,',','.')."%" : '').'</b>', 	
	'<b>'.(trim($nr_total_total) != "" ? number_format(($nr_total_total / (intval($contador_ano_atual) > 0 ? intval($contador_ano_atual) : 1)),0,',','.') : '').'</b>',
    '',
    '',
    ''
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