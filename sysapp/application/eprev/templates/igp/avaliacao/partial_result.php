<?php
$body=array();
$head=array( 
	'#', $label_0, $label_1, $label_2, $label_3, $label_4, $label_5, $label_6, $label_7, $label_8, $label_9, $label_10,''
);
$acum=0;
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
    echo anchor_popup("indicador/apresentacao/detalhe/".intval( $grafico[0]['cd_indicador_tabela'] ), 'Visualizar apresentação', $ar_janela);

foreach( $collection as $item )
{
    $a_data = explode( "/", $item['mes_referencia'] );

    if( $tabela[0]['cd_indicador_tabela']==$item['cd_indicador_tabela'] )
    {
        $link=anchor("igp/avaliacao/detalhe/" . $item["cd_avaliacao"], "editar");
        $contador_ano_atual++;
    }
    else
    {
        $link='';
    }
 
	$pontos = $item['nr_pontos'];
	$meta = $item["nr_meta"];
	$peso = $item["nr_peso"];

	$meta_resultado=($pontos/$meta)*100;

	// =SE(F144<1;F144*E144;E144)
	if( ($meta_resultado/100)<1 )
	{
		$rf_mes = ($meta_resultado/100) * $peso ;
	}
	else
	{
		$rf_mes = $peso;
	}

	if($acum>0)
	{
		$acum = ($acum+$pontos)/2;
	}
	else
	{
		$acum = $pontos;
	}

	$meta_acum=($acum/$meta)*100;

	// =SE(I141>1;E141;I141*E141)
	if( ($meta_acum/100)>1 )
	{
		$rf_acum=$peso;
	}
	else
	{
		$rf_acum=($meta_acum/100)*$peso;
	}

	// % MÉDIA MÓVEL
	$a_percentual_media_movel[]=$pontos;
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
		, array(  number_format($pontos, 2,',','.'), "text-align:right;"  )
		, array(  number_format($meta, 0) , "text-align:right;"  )
		, array(  number_format($peso, 0) , "text-align:right;"  )
		, array(  number_format($meta_resultado, 2,',','.').'%', "text-align:right;"  )
		, array(  number_format($rf_mes, 2,',','.') , "text-align:right;"  )
		, array(  number_format($acum, 2,',','.') , "text-align:right;"  )
		, array(  number_format($meta_acum, 2,',','.') , "text-align:right;"  )
		, array(  number_format($rf_acum, 2,',','.') , "text-align:right;"  )
		, array(  number_format($percentual_media_movel, 2,',','.') , "text-align:right;"  )
		, array(  number_format($media_movel, 2,',','.') , "text-align:right;"  )
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