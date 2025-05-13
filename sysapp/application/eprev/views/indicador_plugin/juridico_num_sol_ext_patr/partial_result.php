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
$nr_perito_total    = 0;
$nr_ceee_total      = 0;
$nr_aes_total       = 0;
$nr_rge_total       = 0;
$nr_cgtee_total     = 0;
$nr_crm_total       = 0;
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
        $link = anchor("indicador_plugin/juridico_num_sol_ext_patr/detalhe/".$item["cd_juridico_num_sol_ext_patr"], "editar");
        $referencia = $item['mes_referencia'];
    }

    if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && trim($item['fl_media']) != 'S')
    {
        $contador_ano_atual++;
        $nr_perito_total += $item["nr_perito"];
        $nr_ceee_total   += $item["nr_ceee"];
        $nr_aes_total    += $item["nr_aes"];
        $nr_rge_total    += $item["nr_rge"];
        $nr_cgtee_total  += $item["nr_cgtee"];
        $nr_crm_total    += $item["nr_crm"];
        $nr_total_total  += $item["nr_total"];
    }

    $body[] = array(
		$contador--,
        $referencia,
        (trim($item["nr_perito"]) != "" ? number_format($item["nr_perito"],0,',','.') : ''),
        (trim($item["nr_ceee"])   != "" ? number_format($item["nr_ceee"],0,',','.') : ''),
        (trim($item["nr_aes"])    != "" ? number_format($item["nr_aes"],0,',','.') : ''),
        (trim($item["nr_rge"])    != "" ? number_format($item["nr_rge"],0,',','.') : ''),
        (trim($item["nr_cgtee"])  != "" ? number_format($item["nr_cgtee"],0,',','.') : ''),
        (trim($item["nr_crm"])    != "" ? number_format($item["nr_crm"],0,',','.') : ''),
        (trim($item["nr_total"])  != "" ? number_format($item["nr_total"],0,',','.') : ''),
        (trim($item["nr_meta"])   != "" ? number_format($item["nr_meta"],0,',','.') : ''),
        array($item["observacao"], 'text-align:left'), 
        $link 
    );
}

$body[] = array(
    0,
    '<b>Total de '.intval($tabela[0]['nr_ano_referencia']).'</b>',
    '<b>'.(trim($nr_perito_total) != "" ? number_format($nr_perito_total,0,',','.') : '').'</b>',
    '<b>'.(trim($nr_ceee_total) != "" ? number_format($nr_ceee_total,0,',','.') : '').'</b>',
    '<b>'.(trim($nr_aes_total) != "" ? number_format($nr_aes_total,0,',','.') : '').'</b>',
    '<b>'.(trim($nr_rge_total) != "" ? number_format($nr_rge_total,0,',','.') : '').'</b>',
    '<b>'.(trim($nr_cgtee_total) != "" ? number_format($nr_cgtee_total,0,',','.') : '').'</b>',
    '<b>'.(trim($nr_crm_total) != "" ? number_format($nr_crm_total,0,',','.') : '').'</b>',
    '<b>'.(trim($nr_total_total) != "" ? number_format($nr_total_total,0,',','.') : '').'</b>',
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