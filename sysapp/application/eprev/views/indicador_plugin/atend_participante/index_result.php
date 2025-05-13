<?php

$head = array(
	'#', $label_0, $label_1, $label_2, #$label_3, 
	$label_4, $label_5, $label_6, #$label_7, 
	$label_8, $label_9, $label_10, $label_11, $label_15, $label_16, $label_12, $label_13, $label_14, ''
);

#$tabela = indicador_tabela_aberta( intval( enum_indicador::ATENDIMENTO_PARTICIPANTE ) );

if(sizeof($tabela) <= 0)
{
	echo "Não foi identificado período aberto para o Indicador";
}
else
{
    echo "<BR>";
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
	$contador              = sizeof($collection);
	$media_ano             = array();
	$a_data                = array(0, 0);
	$nr_acumulado_anterior = 0;

	$body = array();

	foreach($collection as $item)
	{
		$a_data = explode( "/", $item['mes_ano_referencia'] );

		$nr_meta = floatval($item["nr_meta"]);

        $observacao = $item["observacao"];

		if($item['fl_media'] == 'S')
		{
			$link = '';

			$referencia = " Resultado de " . $item['ano_referencia'];
		}
		else
		{
			$link = anchor("indicador_plugin/atend_participante/cadastro/" . $item["cd_atend_participante"], "[editar]");

			$referencia = $item['mes_ano_referencia'];
		}

		if( intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && $item['fl_media']!='S' )
		{
			$contador_ano_atual++;

			$media_ano[] = $item['nr_total_f'];

			
		}

		$nr_ceee    = ($item["nr_ceee"]);
		$nr_aes     = ($item["nr_aes"]);
		$nr_cgtee   = ($item["nr_cgtee"]);
		$nr_rge     = ($item["nr_rge"]);
		$nr_crm     = ($item["nr_crm"]);
		$nr_senge   = ($item["nr_senge"]);
		$nr_sinpro  = ($item["nr_sinpro"]);
		$nr_familia = ($item["nr_familia"]);
		$nr_inpel   = ($item["nr_inpel"]);
		$nr_ceran   = ($item["nr_ceran"]);
		$nr_foz     = ($item["nr_foz"]);
		$nr_familia_municipio     = ($item["nr_familia_municipio"]);
		$nr_ieabprev     = ($item["nr_ieabprev"]);
		$nr_total_f = $item["nr_total_f"];

		$body[] = array(
			  $contador--,
			  $referencia,
			  ($nr_ceee!=''? number_format($nr_ceee,0,',','.') :''), 
			  ($nr_aes!=''?number_format($nr_aes,0,',','.'):''),
			  ($nr_rge!=''?number_format($nr_rge,0,',','.'):''),
			  ($nr_crm!=''?number_format($nr_crm,0,',','.'):''),
			  ($nr_senge!=''?number_format($nr_senge,0,',','.'):''), 
			  ($nr_familia!=""?number_format($nr_familia,0,',','.'):''),
			  ($nr_inpel!=""?number_format($nr_inpel,0,',','.'):''),
			  ($nr_ceran!=""?number_format($nr_ceran,0,',','.'):''),
			  ($nr_foz!=""?number_format($nr_foz,0,',','.'):''),
			  ($nr_familia_municipio!=""?number_format($nr_familia_municipio,0,',','.'):''),
			  ($nr_ieabprev!=""?number_format($nr_ieabprev,0,',','.'):''),
			  ($nr_total_f!=''?number_format($nr_total_f,0,',','.'):''),
			  ($nr_meta!=''?number_format($nr_meta,0,',','.'):''),
              array(nl2br($observacao), "text-align:justify;"),
			  $link 
		);
	}

	if(sizeof($media_ano) >0)
	{
		
		$body[] = array(
			0, 
			'<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>', 
			'<b>'.number_format($nr_ceee,0,',','.').'</b>', 
			'<b>'.number_format($nr_aes,0,',','.').'</b>',
			'<b>'.number_format($nr_rge,0,',','.').'</b>',
			'<b>'.number_format($nr_crm,0,',','.').'</b>',
			'<b>'.number_format($nr_senge,0,',','.').'</b>',
			'<b>'.number_format($nr_familia,0,',','.').'</b>',
			'<b>'.number_format($nr_inpel,0,',','.').'</b>',
			'<b>'.number_format($nr_ceran,0,',','.').'</b>',
			'<b>'.number_format($nr_foz,0,',','.').'</b>',
			'<b>'.number_format($nr_familia_municipio,0,',','.').'</b>',
			'<b>'.number_format($nr_ieabprev,0,',','.').'</b>',
			'<b>'.number_format($nr_total_f,0,',','.').'</b>',
			'<b>'.number_format($nr_meta,0,',','.').'</b>', 
			'' ,
			''
		);
	}


	echo "<input type='hidden' id='mes_input' name='mes_input' value='".$a_data[0]."' />";
	echo "<input type='hidden' id='contador_input' name='contador_input' value='".$contador_ano_atual."' />";

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();
}
?>
