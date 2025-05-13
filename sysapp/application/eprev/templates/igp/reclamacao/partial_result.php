<?php
$body=array();
$head=array( 
	'#', $label_0, $label_1, $label_2, $label_3, $label_4, $label_5, $label_6, $label_7, $label_8, $label_9, $label_10, $label_11, $label_12, $label_13, $label_14, ''
);

$atendimento_acumulado=0;
$reclamacao_acumulado=0;
$acumular_ate=12;
$contador_ano_atual=0;
$a_data=array(0, 0);
$contador=sizeof($collection);

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
    echo anchor_popup("indicador/apresentacao/detalhe/".intval( $tabela[0]['cd_indicador_tabela'] ), 'Visualizar apresentação', $ar_janela);


foreach( $collection as $item )
{
    $a_data = explode( "/", $item['mes_referencia'] );

    if( $tabela[0]['cd_indicador_tabela']==$item['cd_indicador_tabela'] )
    {
        $link=anchor("igp/reclamacao/detalhe/" . $item["cd_reclamacao"], "editar");
        $contador_ano_atual++;
    }
    else
    {
        $link='';
    }

	$percentual_reclamacao = (intval($item["nr_reclamacao"])/intval($item["nr_atendimento"]))*100;
	$percentual_reclamacao = $percentual_reclamacao.'%';

	// ATEND ACUMUL: ultimos 12 meses
	$a_atendimento_acumulado[]=$item["nr_atendimento"];
	$atendimento_acumulado=0;

	// RECL ACUMUL: ultimos 12 meses
	$a_reclamacao_acumulado[]=$item["nr_reclamacao"];
	$reclamacao_acumulado=0;

	$j=1;
	for( $i=sizeof($a_atendimento_acumulado);$i>0;$i-- )
	{
		if( $j<=$acumular_ate )
		{
			$atendimento_acumulado += $a_atendimento_acumulado[$i-1];
			$reclamacao_acumulado += $a_reclamacao_acumulado[$i-1];

			$j++;
		}
	}

	$percentual_reclamacao_acumulado = (intval($reclamacao_acumulado)/intval($atendimento_acumulado))*100;
	
	
	// = SE(E63>0;F63/E63;F63*100)			META / RESULT
	// E63 = $percentual_reclamacao
	// F63 = $item["nr_meta"]
	$meta_resultado = ($percentual_reclamacao>0)?( $item["nr_meta"]/$percentual_reclamacao ):( $item["nr_meta"]*100 );
	$meta_resultado = $meta_resultado*100;

	// = SE(K63>1;J63;J63*K63)				RF MES
	// K63 = $meta_resultado
	// J63 = $item["nr_peso"]
	$rf_mes = (floatval($meta_resultado)>100) ? $item["nr_peso"] : ($item["nr_peso"]*floatval($meta_resultado))/100;
	$rf_mes = $rf_mes;

	//echo $percentual_reclamacao_acumulado;exit;
	$meta_percentual_acumulado=( $item['nr_meta'] / $percentual_reclamacao_acumulado )*100;
	//$meta_percentual_acumulado=$meta_percentual_acumulado;

	// =SE(M63<1;M63*J63;J63)				RF ACUMULADO
	// M63 = $meta_percentual_acumulado
	// J63 = $item['nr_peso']
	$rf_acumulado=( $meta_percentual_acumulado<100 )?($meta_percentual_acumulado*$item['nr_peso'])/100:$item['nr_peso'];
	$rf_acumulado=$rf_acumulado;

	//% MÉDIA MÓVEL, média dos últimos 12 meses do "% RECL"
	$a_percentual_reclamacao[]=$percentual_reclamacao;
	$percentual_media_movel=0;
	
	//% MÉDIA MÓVEL, média dos últimos 12 meses do "RF MES"
	$a_rf_mes[]=$rf_mes;
	$media_movel=0;

	$j=1;
	for( $i=sizeof($a_percentual_reclamacao);$i>0;$i-- )
	{
		if( $j<=$acumular_ate )
		{
			$percentual_media_movel += $a_percentual_reclamacao[$i-1];
			$media_movel += $a_rf_mes[$i-1];

			$j++;
		}
	}

	$divisor=(sizeof($a_percentual_reclamacao)<$acumular_ate)?sizeof($a_percentual_reclamacao):$acumular_ate;
	$percentual_media_movel=floatval($percentual_media_movel)/$divisor;
	$media_movel=floatval($media_movel)/$divisor;

	$body[] = array(
		$contador--
		, $item['mes_referencia']
		, $item["nr_atendimento"]
		, $item["nr_reclamacao"]
		, number_format($percentual_reclamacao,2,',','.').'%'
		, number_format($item["nr_meta"],2,',','.') . '%'
		, $atendimento_acumulado
		, $reclamacao_acumulado
		, number_format($percentual_reclamacao_acumulado, 2,',','.').'%'
		, $item["nr_peso"]
		, number_format($meta_resultado,2,',','.').'%'
		, number_format($rf_mes,2,',','.')
		, number_format($meta_percentual_acumulado,2,',','.').'%'
		, number_format($rf_acumulado,2,',','.')
		, number_format($percentual_media_movel,2,',','.').'%'
		, number_format($media_movel,2,',','.')
		, ($item['fl_editar'] == "S" ? $link : "")
	);
}

echo "<input type='hidden' id='mes_input' name='mes_input' value='".$a_data[0]."' />";
echo "<input type='hidden' id='contador_input' name='contador_input' value='".$contador_ano_atual."' />";

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>