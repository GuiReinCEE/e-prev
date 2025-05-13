<?php
$body=array();
$head = array( 
	'#', $label_0, $label_1, $label_2, $label_3, $label_4, $label_5, $label_6, $label_7, $label_8, $label_9, $label_10, $label_11, $label_12, $label_13, $label_14, ''
);

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
        $link=anchor("igp/variacao_orcamentaria/detalhe/" . $item["cd_variacao_orcamentaria"], "editar");
        $contador_ano_atual++;
    }
    else
    {
        $link='';
    }

	$orcado = floatval($item["nr_orcado"]);
	$realizado = floatval($item["nr_realizado"]);
	$gasto=(floatval($realizado)/floatval($orcado)) * 100;
	$meta = floatval($item["nr_meta"]);

	$arr[]=floatval($orcado);
	$orcado_acumulada = 0;
	$j=1;
	for( $i=sizeof($arr);$i>0;$i-- )
	{
		if( $j<=$acumular_ate )
		{
			$orcado_acumulada+=$arr[$i-1];

			$j++;
		}
	}

	$arr_2[]=floatval($realizado);
	$realizado_acumulada = 0;
	$j=1;
	for( $i=sizeof($arr_2);$i>0;$i-- )
	{
		if( $j<=$acumular_ate )
		{
			$realizado_acumulada+=$arr_2[$i-1];

			$j++;
		}
	}

	$perc_gasto_sobre_acum=(floatval($realizado_acumulada)/floatval($orcado_acumulada))*100;
	$peso=floatval($item["nr_peso"]);

	$meta_por_resultado = ( floatval($gasto)/floatval($meta) )*100;

	// = SE(K170>0,98;SE(K170<1,02;J170;SE(K170>=2;0;1-(K170-1))*J170);K170*J170)
	// K = meta por resultado
	// J = peso
	if( (floatval($meta_por_resultado)/100)>0.98 )
	{
		if( (floatval($meta_por_resultado)/100)<1.02 )
		{
			$rf_mes = $peso;
		}
		else
		{
			if( (floatval($meta_por_resultado)/100)>=2 )
			{
				$rf_mes = 0;
			}
			else
			{
				$rf_mes = 1-( (floatval($meta_por_resultado)/100)-1 );
			}
			$rf_mes = $rf_mes * $peso;
		}
	}
	else
	{
		$rf_mes = (floatval($meta_por_resultado)/100) * $peso;
	}

	$meta_por_perc_acum = ( floatval($perc_gasto_sobre_acum)/floatval($meta) )*100;

	// = SE(M170>0,98;SE(M170<1,02;J170;SE(M170>=2;0;1-(M170-1))*J170);M170*J170)
	// M = meta perc acum
	// J = peso
	if( (floatval($meta_por_perc_acum)/100)>0.98 )
	{
		if( (floatval($meta_por_perc_acum)/100)<1.02 )
		{
			$rf_acum = $peso;
		}
		else
		{
			if( (floatval($meta_por_perc_acum)/100)>=2 )
			{
				$rf_acum = 0;
			}
			else
			{
				$rf_acum = 1 - ( (floatval($meta_por_perc_acum)/100)-1 );
			}
			
			$rf_acum = $rf_acum * $peso;
		}
	}
	else
	{
		$rf_acum = (floatval($meta_por_perc_acum)/100) * $peso;
	}
	
	
	if( (floatval($meta_por_perc_acum)/100)<0 )
	{
		$rf_acum=((floatval($meta_por_perc_acum)/100)*floatval($peso));
	}
	else
	{
		$rf_acum=$peso;
	}

	//% MÉDIA MÓVEL
	$a_percentual_media_movel[]=floatval($gasto);
	$percentual_media_movel=0;

	// MÉDIA MÓVEL	
	$a_rf_mes[]=$rf_mes;
	$media_movel=0;

	$j=1;
	for( $i=sizeof($a_percentual_media_movel);$i>0;$i-- )
	{
		if( $j<=$acumular_ate )
		{
			$percentual_media_movel+=$a_percentual_media_movel[$i-1];
			$media_movel+=$a_rf_mes[$i-1];

			$j++;
		}
	}

	$divisor=(sizeof($a_percentual_media_movel)<$acumular_ate)?sizeof($a_percentual_media_movel):$acumular_ate;
	$percentual_media_movel=floatval($percentual_media_movel)/$divisor;
	$media_movel=floatval($media_movel)/$divisor;

	$body[] = array(
		$contador--
		, $item['mes_referencia']
		, array( number_format($orcado,2,',','.'),'text-align:right;' )
		, array( number_format($realizado,2,',','.'),'text-align:right;' )
		, array( number_format($gasto,2,',','.').'%','text-align:right;' )
		, array( number_format($meta,2,',','.').'%','text-align:right;' )
		, array( number_format($orcado_acumulada,2,',','.'),'text-align:right;' )
		, array( number_format($realizado_acumulada,2,',','.'),'text-align:right;' )
		, array( number_format($perc_gasto_sobre_acum,2,',','.').'%','text-align:right;' )
		, array( number_format($peso,2,',','.'),'text-align:right;' )
		, array( number_format($meta_por_resultado,2,',','.').'%','text-align:right;' )
		, array( number_format($rf_mes,2,',','.'),'text-align:right;' )
		, array( number_format($meta_por_perc_acum,2,',','.').'%','text-align:right;' )
		, array( number_format($rf_acum,2,',','.'),'text-align:right;' )
		, array( number_format($percentual_media_movel,2,',','.').'%','text-align:right;' )
		, array( number_format($media_movel,2,',','.'),'text-align:right;' )
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