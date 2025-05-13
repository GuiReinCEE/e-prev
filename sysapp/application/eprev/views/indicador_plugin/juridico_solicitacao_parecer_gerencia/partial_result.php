<?php
$body = array();
$head = array( 
    "#", 
	$label_0, $label_1, $label_2, $label_3, $label_4, $label_5, 
	$label_6, $label_7, $label_8, $label_9, $label_10, /*$label_11,*/
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

$nr_ac_tot_anual = 0;
$nr_ai_tot_anual = 0;
$nr_aj_tot_anual = 0;
$nr_gc_tot_anual = 0;
$nr_ge_tot_anual = 0;
$nr_gfc_tot_anual = 0;
$nr_ggs_tot_anual = 0;
$nr_gin_tot_anual = 0;
$nr_gp_tot_anual = 0;
$nr_sg_tot_anual = 0;

$nr_pre_tot_anual = 0;
$nr_prev_tot_anual = 0;
$nr_fin_tot_anual = 0;
$nr_infr_tot_anual = 0;

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
        $link = anchor("indicador_plugin/juridico_solicitacao_parecer_gerencia/detalhe/".$item["cd_juridico_solicitacao_parecer_gerencia"], "editar");
        $referencia = $item['mes_referencia'];
    }

    if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && trim($item['fl_media']) != 'S')
    {
        $contador_ano_atual++;

        $nr_ac_tot_anual += intval($item['nr_ac']); 
        $nr_ai_tot_anual += intval($item['nr_ai']); 
        $nr_aj_tot_anual += intval($item['nr_aj']); 
        $nr_gc_tot_anual += intval($item['nr_gc']); 
        $nr_ge_tot_anual += intval($item['nr_ge']); 
        $nr_gfc_tot_anual += intval($item['nr_gfc']); 
        $nr_ggs_tot_anual += intval($item['nr_ggs']); 
        $nr_gin_tot_anual += intval($item['nr_gin']); 
        $nr_gp_tot_anual += intval($item['nr_gp']); 
        $nr_sg_tot_anual+= intval($item['nr_sg']); 
		$nr_pre_tot_anual   += intval($item['nr_pre']);
		$nr_prev_tot_anual   += intval($item['nr_prev']);
		$nr_fin_tot_anual   += intval($item['nr_fin']);
		$nr_infr_tot_anual   += intval($item['nr_infr']);
		$nr_total_tot_anual += intval($item['nr_total']);
    }
	
    $body[] = array(
		$contador--,
        $referencia,
        number_format($item["nr_ac"],0,',','.'),
        number_format($item["nr_ai"],0,',','.'),
        number_format($item["nr_aj"],0,',','.'),
        number_format($item["nr_gc"],0,',','.'),
        number_format($item["nr_ge"],0,',','.'),
        number_format($item["nr_gfc"],0,',','.'),
        number_format($item["nr_ggs"],0,',','.'),
        number_format($item["nr_gin"],0,',','.'),
        number_format($item["nr_gp"],0,',','.'),
        number_format($item["nr_sg"],0,',','.'),
        number_format($item["nr_total"],0,',','.'),
        number_format($item["nr_pre"],0,',','.'),
        number_format($item["nr_prev"],0,',','.'),
        number_format($item["nr_fin"],0,',','.'),
        number_format($item["nr_infr"],0,',','.'),
        number_format($item["nr_meta"],0,',','.'),
        array($item["observacao"], 'text-align:left'), 
        $link 
    );
}

$body[] = array(
    0,
    "<b>Resultado de ".intval($tabela[0]['nr_ano_referencia'])."</b>",
	"<b>".number_format($nr_ac_tot_anual,0,',','.')."</b>",
	"<b>".number_format($nr_ai_tot_anual,0,',','.')."</b>",
	"<b>".number_format($nr_aj_tot_anual,0,',','.')."</b>",
	"<b>".number_format($nr_gc_tot_anual,0,',','.')."</b>",
	"<b>".number_format($nr_ge_tot_anual,0,',','.')."</b>",
	"<b>".number_format($nr_gfc_tot_anual,0,',','.')."</b>",
	"<b>".number_format($nr_ggs_tot_anual,0,',','.')."</b>",
	"<b>".number_format($nr_gin_tot_anual,0,',','.')."</b>",
	"<b>".number_format($nr_gp_tot_anual,0,',','.')."</b>",
	"<b>".number_format($nr_sg_tot_anual,0,',','.')."</b>",
	"<b>".number_format($nr_total_tot_anual,0,',','.')."</b>",
	"<b>".number_format($nr_pre_tot_anual,0,',','.')."</b>",
	"<b>".number_format($nr_prev_tot_anual,0,',','.')."</b>",
	"<b>".number_format($nr_fin_tot_anual,0,',','.')."</b>",
	"<b>".number_format($nr_infr_tot_anual,0,',','.')."</b>",
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