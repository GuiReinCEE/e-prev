<?php
$body=array();
$head=array(
	'#', $label_0, $label_1, $label_2, $label_3, $label_4, $label_5, $label_6, $label_7, $label_8, $label_9, $label_10, $label_11, $label_12, $label_13, $label_14, $label_15, $label_16, $label_17, ''
);

$concedido_acumulado=0;
$erro_acumulado=0;
$acumular_ate=12; // meses
$contador_ano_atual=0;
$a_data=array(0, 0);
$indicador = array();
$igp = array();

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
        $link=anchor("igp/rpp/detalhe/" . $item["cd_rpp"], "editar");
        $contador_ano_atual++;
    }
    else
    {
        $link='';
    }

	$inpc = $item['nr_inpc'];

	if(floatval($item['nr_indice_mes'])==0)
	{
		$indice_mes = (   pow( (floatval($item['nr_indice_ano']/100)+1), (1/12) )-1  )*100;
	}
	else
	{
		$indice_mes = floatval( $item['nr_indice_mes'] );
	}

	$inpc_mais_indice =(  ( (1+($inpc/100)) * (1+($indice_mes/100)) )-1  )*100;

	// INPC 12 MESES: ultimos 12 meses
	$a_inpc_12_meses[] = ( 1+($inpc/100) );
	$inpc_12_meses=0;

	$j=1;
	for( $i=sizeof($a_inpc_12_meses);$i>0;$i-- )
	{
		if( $j<=$acumular_ate )
		{
			if($inpc_12_meses>0)
			{
				$inpc_12_meses *= $a_inpc_12_meses[$i-1];
			}
			else
			{
				$inpc_12_meses=$a_inpc_12_meses[$i-1];
			}

			$j++;
		}
	}
	
	if(floatval($item['nr_inpc_12_meses'])>0)
	{
		$inpc_12_meses = floatval($item['nr_inpc_12_meses']);
	}
	else
	{
		$inpc_12_meses=($inpc_12_meses-1)*100;
	}

	$indice_ano=$item['nr_indice_ano'];

	// =((1+F223)*(1+G223))-1       f indice_12_meses     g indice_ano
	$meta_acum = ( ( (1+($inpc_12_meses/100)) * (1+($indice_ano/100)) ) -1 )*100;

	$wacc = $item['nr_wacc'];

	// WACC ACUM: ultimos 12 meses
	$a_wacc_acum[] = ( 1+($wacc/100) );
	$wacc_acum=0;

	$j=1;
	for( $i=sizeof($a_wacc_acum);$i>0;$i-- )
	{
		if( $j<=$acumular_ate )
		{
			if($wacc_acum>0)
			{
				$wacc_acum*=$a_wacc_acum[$i-1];
			}
			else
			{
				$wacc_acum=$a_wacc_acum[$i-1];
			}

			$j++;
		}
	}
	$wacc_acum = ($wacc_acum-1)*100;

	$peso = $item['nr_peso'];

	if( ($wacc/100)<0 )
	{
		$ob_meta = ( ( 1+($wacc/100) ) / ( 1+($inpc_mais_indice/100) ) - 1 )*100;
	}
	else
	{
		$ob_meta = ( ($wacc/100) / ($inpc_mais_indice/100) )*100;
	}
	
	// =SE(L223<1;K223*L223;K223)
	if( ($ob_meta/100)<1 )
	{
		$rf_mes_helper = $peso*($ob_meta/100);
	}
	else
	{
		$rf_mes_helper = $peso;
	}

	// =SE(M219<0;0;M219)
	if( $rf_mes_helper<0 )
	{
		$rf_mes = 0;
	}
	else
	{
		$rf_mes = $rf_mes_helper;
	}
	
	// =SE(J223<0;(1+J223)/(1+H223)-1;(J223/H223))
	if( ($wacc_acum/100)<0 )
	{
		$wacc_acum_meta_acum = ( ( 1+($wacc_acum/100) ) / ( 1+($meta_acum/100) )-1 )*100;
	}
	else
	{
		$wacc_acum_meta_acum = ( ($wacc_acum/100) / ($meta_acum/100) )*100;
	}
	
	// =SE(O223<1;O223*K223;K223)
	if( ($wacc_acum_meta_acum/100)<1 )
	{
		$rf_acum_helper = ($wacc_acum_meta_acum/100)*$peso;
	}
	else
	{
		$rf_acum_helper = $peso;
	}
	
	// =SE(P223<0;0;P223)
	if($rf_acum_helper<0)
	{
		$rf_acum=0;
	}
	else
	{
		$rf_acum=$rf_acum_helper;
	}
	
	//% MÉDIA MÓVEL, =MÉDIA(I218:I229)
	$a_media_movel_wacc[]=$wacc;
	$media_movel_wacc=0;

	//% MÉDIA MÓVEL, =MÉDIA(N211:N222)
	$a_rf_mes[]=$rf_mes;
	$media_movel_rf_mes=0;

	$j=1;
	for( $i=sizeof($a_media_movel_wacc);$i>0;$i-- )
	{
		if( $j<=$acumular_ate )
		{
			$media_movel_wacc += $a_media_movel_wacc[$i-1];
			$media_movel_rf_mes += $a_rf_mes[$i-1];

			$j++;
		}
	}
	
	$divisor=(sizeof($a_media_movel_wacc)<$acumular_ate)?sizeof($a_media_movel_wacc):$acumular_ate;
	$media_movel_wacc=floatval($media_movel_wacc)/$divisor;
	$media_movel_rf_mes=floatval($media_movel_rf_mes)/$divisor;
	
	$body[] = array(
		$contador--
		, $item['mes_referencia']
		, array( number_format( $inpc, 2,',','.' )."%", 'text-align:right;' )
		, array( number_format($indice_mes, 5,',','.') . "%", 'text-align:right;' )
		, array( number_format($inpc_mais_indice, 3,',','.') . "%", 'text-align:right;' )
		, array( number_format($inpc_12_meses, 2,',','.').'%', 'text-align:right;' )
		, array( number_format($indice_ano, 4,',','.').'%', 'text-align:right;' )
		, array( number_format($meta_acum, 6,',','.').'%', 'text-align:right;' )
		, array( number_format($wacc, 5,',','.') . "%", 'text-align:right;' )
		, array( number_format($wacc_acum, 5,',','.').'%', 'text-align:right;' )
		, array( number_format($peso, 2,',','.'), 'text-align:right;' )
		, array( number_format($ob_meta, 2,',','.') . '%', 'text-align:right;' )
		, array( number_format($rf_mes_helper, 2,',','.'), 'text-align:right;' )
		, array( number_format($rf_mes, 2,',','.'), 'text-align:right;' )
		, array( number_format($wacc_acum_meta_acum, 2,',','.').'%', 'text-align:right;' )
		, array( number_format($rf_acum_helper, 2,',','.'), 'text-align:right;' )
		, array( number_format($rf_acum, 2,',','.'), 'text-align:right;' )
		, array( number_format($media_movel_wacc, 2,',','.').'%', 'text-align:right;' )
		, array( number_format($media_movel_rf_mes, 2,',','.'), 'text-align:right;' )
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