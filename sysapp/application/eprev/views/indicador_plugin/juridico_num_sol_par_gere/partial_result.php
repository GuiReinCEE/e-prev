<?php
$body = array();
$head = array( 
    "#", 
	$label_0, $label_1, $label_2, $label_3, $label_4, $label_5, 
	$label_6, $label_7, $label_8, $label_9, $label_10, $label_11, 
	$label_12, $label_13, $label_14, $label_15, $label_16, $label_17, $label_18,
	""
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
$nr_meta            = 0;
$nr_sg_tot_anual    = 0; 
$nr_gri_tot_anual   = 0;
$nr_gap_tot_anual   = 0;
$nr_gb_tot_anual    = 0;
$nr_ga_tot_anual    = 0;
$nr_gc_tot_anual    = 0;
$nr_gf_tot_anual    = 0;
$nr_gin_tot_anual   = 0;
$nr_rh_tot_anual    = 0;
$nr_gad_tot_anual   = 0;
$nr_gi_tot_anual    = 0;
$nr_pre_tot_anual   = 0;
$nr_seg_tot_anual   = 0;
$nr_fin_tot_anual   = 0;
$nr_adm_tot_anual   = 0;
$nr_total_tot_anual = 0;


foreach($collection as $item)
{
    $a_data = explode("/", $item['mes_referencia']);
	$nr_meta = $item['nr_meta'];

    if(trim($item['fl_media']) == 'S')
    {
        $link = '';
        $referencia = "Total de " . $item['ano_referencia'];
    }
    else
    {
        $link = anchor("indicador_plugin/juridico_num_sol_par_gere/detalhe/".$item["cd_juridico_num_sol_par_gere"], "editar");
        $referencia = $item['mes_referencia'];
    }

    if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && trim($item['fl_media']) != 'S')
    {
        $contador_ano_atual++;
		$nr_sg_tot_anual    += intval($item['nr_sg']); 
		$nr_gri_tot_anual   += intval($item['nr_gri']);
		$nr_gap_tot_anual   += intval($item['nr_gap']);
		$nr_gb_tot_anual    += intval($item['nr_gb']);
		$nr_ga_tot_anual    += intval($item['nr_ga']);
		$nr_gc_tot_anual    += intval($item['nr_gc']);
		$nr_gf_tot_anual    += intval($item['nr_gf']);
		$nr_gin_tot_anual   += intval($item['nr_gin']);
		$nr_rh_tot_anual    += intval($item['nr_rh']);
		$nr_gad_tot_anual   += intval($item['nr_gad']);
		$nr_gi_tot_anual    += intval($item['nr_gi']);
		$nr_pre_tot_anual   += intval($item['nr_pre']);
		$nr_seg_tot_anual   += intval($item['nr_seg']);
		$nr_fin_tot_anual   += intval($item['nr_fin']);
		$nr_adm_tot_anual   += intval($item['nr_adm']);
		$nr_total_tot_anual += intval($item['nr_total']);
    }
	
    $body[] = array(
		$contador--,
        $referencia,
        number_format($item["nr_sg"],0,',','.'),
        number_format($item["nr_gri"],0,',','.'),
        number_format($item["nr_gap"],0,',','.'),
        number_format($item["nr_gb"],0,',','.'),
        number_format($item["nr_ga"],0,',','.'),
        number_format($item["nr_gc"],0,',','.'),
        number_format($item["nr_gf"],0,',','.'),
        number_format($item["nr_gin"],0,',','.'),
        number_format($item["nr_rh"],0,',','.'),
        number_format($item["nr_gad"],0,',','.'),
        number_format($item["nr_gi"],0,',','.'),
        number_format($item["nr_total"],0,',','.'),
        number_format($item["nr_pre"],0,',','.'),
        number_format($item["nr_seg"],0,',','.'),
        number_format($item["nr_fin"],0,',','.'),
        number_format($item["nr_adm"],0,',','.'),
        number_format($item["nr_meta"],0,',','.'),
        array($item["observacao"], 'text-align:left'), 
        $link 
    );
}

$body[] = array(
    0,
    "<b>Resultado de ".intval($tabela[0]['nr_ano_referencia'])."</b>",
	"<b>".number_format($nr_sg_tot_anual,0,',','.')."</b>",
	"<b>".number_format($nr_gri_tot_anual,0,',','.')."</b>",
	"<b>".number_format($nr_gap_tot_anual,0,',','.')."</b>",
	"<b>".number_format($nr_gb_tot_anual,0,',','.')."</b>",
	"<b>".number_format($nr_ga_tot_anual,0,',','.')."</b>",
	"<b>".number_format($nr_gc_tot_anual,0,',','.')."</b>",
	"<b>".number_format($nr_gf_tot_anual,0,',','.')."</b>",
	"<b>".number_format($nr_gin_tot_anual,0,',','.')."</b>",
	"<b>".number_format($nr_rh_tot_anual,0,',','.')."</b>",
	"<b>".number_format($nr_gad_tot_anual,0,',','.')."</b>",
	"<b>".number_format($nr_gi_tot_anual,0,',','.')."</b>",
	"<b>".number_format($nr_total_tot_anual,0,',','.')."</b>",
	"<b>".number_format($nr_pre_tot_anual,0,',','.')."</b>",
	"<b>".number_format($nr_seg_tot_anual,0,',','.')."</b>",
	"<b>".number_format($nr_fin_tot_anual,0,',','.')."</b>",
	"<b>".number_format($nr_adm_tot_anual,0,',','.')."</b>",
	"<b>".number_format($nr_meta,0,',','.')."</b>",
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