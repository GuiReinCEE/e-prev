<?php
$body=array();
$head = array( 
	'#', $label_0, $label_1, $label_2, $label_3, $label_4, $label_5, $label_6, $label_7, $label_8, $label_9, $label_10, $label_11, $label_12, $label_13, $label_14, ''
);

if(sizeof($tabela)<=0)
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
    echo anchor_popup("indicador/apresentacao/detalhe/".intval( $tabela[0]['cd_indicador_tabela'] ), 'Visualizar apresentação', $ar_janela);

	$acumular_ate=12;
    $a_data=array(0, 0);
    $contador_ano_atual=0;

	$contador=sizeof($collection);

	foreach( $collection as $item )
	{
        $a_data = explode( "/", $item['mes_referencia'] );

		if( $tabela[0]['cd_indicador_tabela']==$item['cd_indicador_tabela'] )
		{
			$link=anchor("igp/calculo_inicial/detalhe/" . $item["cd_calculo_inicial"], "editar");
            $contador_ano_atual++;
		}
		else
		{
			$link='';
		}

		$concedido = floatval($item["nr_concedido"]);
		$erro = floatval($item["nr_erro"]);
		$gasto=(floatval($erro)/floatval($concedido)) * 100;
		$meta = floatval($item["nr_meta"]);

		$arr[]=floatval($concedido);
		$concedido_acumulado = 0;
		$j=1;
		for( $i=sizeof($arr);$i>0;$i-- )
		{
			if( $j<=$acumular_ate )
			{
				$concedido_acumulado+=$arr[$i-1];

				$j++;
			}
		}

		$arr_2[]=floatval($erro);
		$erro_acumulado = 0;
		$j=1;
		for( $i=sizeof($arr_2);$i>0;$i-- )
		{
			if( $j<=$acumular_ate )
			{
				$erro_acumulado+=$arr_2[$i-1];

				$j++;
			}
		}

		$perc_gasto_sobre_acum =(floatval($erro_acumulado)/floatval($concedido_acumulado))*100;
		$peso = floatval($item["nr_peso"]);

		// =(F105-E105)/F105
		
		$meta_por_resultado = ( (floatval($meta)-floatval($gasto))/floatval($meta) )* 100;

		// =SE(K216>1;J216;J216*K216)
		if( (floatval($meta_por_resultado)/100)>1 )
		{
			$rf_mes=$peso;
		}
		else
		{
			$rf_mes=(floatval($peso)*floatval($meta_por_resultado))/100;
		}

        if($rf_mes < 0)
        {
            $rf_mes = 0;
        }
		
		// =(((F110-I110))/F110)
		// $meta_por_perc_acum = ( floatval($perc_gasto_sobre_acum)/floatval($meta) )*100;
		$meta_por_perc_acum = ( (floatval($meta)-floatval($perc_gasto_sobre_acum))/floatval($meta) )* 100;

		// =SE(N216<1;N216*J216;J216)
		if( (floatval($meta_por_perc_acum)/100)<1 )
		{
			$rf_acum=(floatval($meta_por_perc_acum)*floatval($peso))/100;
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
			, array( $concedido,'text-align:right;' )
			, array( $erro,'text-align:right;' )
			, array( number_format($gasto,2,',','.').'%','text-align:right;' )
			, array( number_format(100-$meta,2,',','.').'%','text-align:right;' )
			, array( $concedido_acumulado,'text-align:right;' )
			, array( number_format($erro_acumulado,2,',','.'),'text-align:right;' )
			, array( number_format($perc_gasto_sobre_acum,2,',','.').'%','text-align:right;' )
			, array( number_format($peso,2,',','.'),'text-align:right;' )
			, array( number_format($meta_por_resultado,3,',','.').'%','text-align:right;' )
			, array( number_format($rf_mes,2,',','.'),'text-align:right;' )
			, array( number_format($meta_por_perc_acum,3,',','.').'%','text-align:right;' )
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
}
?>