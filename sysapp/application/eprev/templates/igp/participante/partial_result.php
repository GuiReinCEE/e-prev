<?php
$body=array();
$head = array( 
	'#', $label_0, $label_1, $label_2, $label_3, $label_4, $label_5, $label_6, $label_7, $label_8, $label_9, $label_10, $label_11, $label_12,''
);

$acumular_ate=12; // meses
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
        $link=anchor( "igp/participante/detalhe/" . $item["cd_participante"], "editar" );
        $contador_ano_atual++;
    }
    else
    {
        $link='';
    }

	$semestre = floatval($item['nr_semestre']);
	$meta = floatval($item['nr_meta']);
	$peso = floatval($item['nr_peso']);
	$instituidor = floatval($item['nr_instituidor']);

	$resultado_por_meta = (  floatval($semestre) / floatval($meta)  )*100;

	// SE(F109>1;E109;F109*E109)
	//		E: $peso
	//		F: $resultado_por_meta
	$rf_mes=0;
	if( (floatval($resultado_por_meta)/100)>1 )
	{
		$rf_mes = floatval($peso);
	}
	else
	{
		$rf_mes = (floatval($resultado_por_meta)/100)*floatval($peso);
	}

	if(floatval($item['nr_partic_mes'])>0)
	{
		$participantes_mes = $item['nr_partic_mes'];
	}
	else
	{
		$participantes_mes = intval($item["nr_semestre"])+intval($item["nr_instituidor"]);
	}

	$participantes_mes_sem_instituidor = floatval($participantes_mes)-floatval($instituidor);

	$meta_por_resultado_acumulado = ( floatval($participantes_mes_sem_instituidor) / floatval($meta) )*100;

	// RF ACUM
	// SE(I109>1;E109;I109*E109)
	// E: $peso
	// I: $meta_por_resultado_acumulado
	if( (floatval($meta_por_resultado_acumulado)/100)>1 )
	{
		$rf_acum = floatval($peso);
	}
	else
	{
		$rf_acum = (floatval($meta_por_resultado_acumulado)/100)*floatval($peso);
	}

	// MÉDIA MÓVEL, média dos últimos 12 meses
	$a_percentual_media_movel[]=$participantes_mes_sem_instituidor;
	$percentual_media_movel=0;

	// MÉDIA MÓVEL, média dos últimos 12 meses do "RF MES"	
	$a_rf_mes[]=$rf_mes;
	$media_movel=0;

	$j=1;
	for( $i=sizeof($a_percentual_media_movel);$i>0;$i-- )
	{
		if( $j<=$acumular_ate )
		{
			$percentual_media_movel += $a_percentual_media_movel[$i-1];
			$media_movel += $a_rf_mes[$i-1];

			$j++;
		}
	}

	$divisor=(sizeof($a_percentual_media_movel)<$acumular_ate)?sizeof($a_percentual_media_movel):$acumular_ate;
	$percentual_media_movel=floatval($percentual_media_movel)/$divisor;
	$media_movel=floatval($media_movel)/$divisor;

	$body[] = array(
		$contador--
		, $item['mes_referencia']
		, $semestre
		, $meta
		, $peso
		, number_format( $resultado_por_meta, 2,',','.' ).'%'
		, number_format( $rf_mes, 2,',','.' )
		, $participantes_mes_sem_instituidor
		, number_format( $meta_por_resultado_acumulado, 2,',','.' ).'%'
		, number_format( $rf_acum, 2,',','.' )
		, number_format($percentual_media_movel,0,'','')
		, number_format( $media_movel, 2,',','.' )
		, $instituidor
		, $participantes_mes
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