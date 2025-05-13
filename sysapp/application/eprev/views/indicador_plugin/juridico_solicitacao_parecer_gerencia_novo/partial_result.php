<?php
	$body = array();
	
	$head = array( 
	    "#", 
		$label_0, 
		$label_1, 
		$label_2, 
		$label_3, 
		$label_4, 
		$label_5, 
		$label_6, 
		$label_7, 
		$label_8, 
		$label_9, 
		$label_10, 
		$label_11,
		$label_12,
		$label_13,
		$label_14,
		$label_15,
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
	$nr_ai_tot_anual  	= 0;
	$nr_grc_tot_anual 	= 0;
	$nr_gj_tot_anual  	= 0;
	$nr_gc_tot_anual  	= 0;
	$nr_gti_tot_anual 	= 0;
	$nr_gin_tot_anual 	= 0;
	$nr_gfc_tot_anual 	= 0;
	$nr_gcm_tot_anual 	= 0;
	$nr_gp_tot_anual  	= 0;
	$nr_de_tot_anual  	= 0;
	$nr_cf_tot_anual  	= 0;
	$nr_cd_tot_anual  	= 0;
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
	        $link = anchor("indicador_plugin/juridico_solicitacao_parecer_gerencia_novo/detalhe/".$item["cd_juridico_solicitacao_parecer_gerencia_novo"], "editar");
	        $referencia = $item['mes_referencia'];
	    }

	    if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && trim($item['fl_media']) != 'S')
	    {
	        $contador_ano_atual++;

	        $nr_ai_tot_anual  += intval($item['nr_ai']);
	        $nr_grc_tot_anual += intval($item['nr_grc']);
	        $nr_gj_tot_anual  += intval($item['nr_gj']);
	        $nr_gc_tot_anual  += intval($item['nr_gc']);
	        $nr_gti_tot_anual += intval($item['nr_gti']);
	        $nr_gin_tot_anual += intval($item['nr_gin']);
	        $nr_gfc_tot_anual += intval($item['nr_gfc']);
	        $nr_gcm_tot_anual += intval($item['nr_gcm']);
	        $nr_gp_tot_anual  += intval($item['nr_gp']);
	        $nr_de_tot_anual  += intval($item['nr_de']);
	        $nr_cf_tot_anual  += intval($item['nr_cf']);
	        $nr_cd_tot_anual  += intval($item['nr_cd']);

	        $nr_total_tot_anual = $nr_ai_tot_anual  + $nr_grc_tot_anual + $nr_gj_tot_anual  + $nr_gc_tot_anual  + $nr_gti_tot_anual + $nr_gin_tot_anual + $nr_gfc_tot_anual + $nr_gcm_tot_anual + $nr_gp_tot_anual + $nr_de_tot_anual + $nr_cf_tot_anual + $nr_cd_tot_anual;
	    }
		
	    $body[] = array(
			$contador--,
	        $referencia,
	        number_format($item["nr_ai"],0,',','.'),
	        number_format($item["nr_grc"],0,',','.'),
	        number_format($item["nr_gj"],0,',','.'),
	        number_format($item["nr_gc"],0,',','.'),
	        number_format($item["nr_gti"],0,',','.'),
	        number_format($item["nr_gin"],0,',','.'),
	        number_format($item["nr_gfc"],0,',','.'),
	        number_format($item["nr_gcm"],0,',','.'),
	        number_format($item["nr_gp"],0,',','.'),
	        number_format($item["nr_de"],0,',','.'),
	        number_format($item["nr_cf"],0,',','.'),
	        number_format($item["nr_cd"],0,',','.'),
	        number_format($item["nr_total"],0,',','.'),
	        number_format($item["nr_meta"],0,',','.'),
	        array($item["observacao"], 'text-align:left'), 
	        $link 
	    );
	}

	$body[] = array(
	    0,
	    "<b>Resultado de ".intval($tabela[0]['nr_ano_referencia'])."</b>",
		"<b>".number_format($nr_ai_tot_anual,0,',','.')."</b>",
		"<b>".number_format($nr_grc_tot_anual,0,',','.')."</b>",
		"<b>".number_format($nr_gj_tot_anual,0,',','.')."</b>",
		"<b>".number_format($nr_gc_tot_anual,0,',','.')."</b>",
		"<b>".number_format($nr_gti_tot_anual,0,',','.')."</b>",
		"<b>".number_format($nr_gin_tot_anual,0,',','.')."</b>",
		"<b>".number_format($nr_gfc_tot_anual,0,',','.')."</b>",
		"<b>".number_format($nr_gcm_tot_anual,0,',','.')."</b>",
		"<b>".number_format($nr_gp_tot_anual,0,',','.')."</b>",
		"<b>".number_format($nr_de_tot_anual,0,',','.')."</b>",
		"<b>".number_format($nr_cf_tot_anual,0,',','.')."</b>",
		"<b>".number_format($nr_cd_tot_anual,0,',','.')."</b>",
		"<b>".number_format($nr_total_tot_anual,0,',','.')."</b>",
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
	$grid->col_oculta = Array('0');
	echo $grid->render();
?>