<?php
$body=array();
$head=array( 
	'#', $label_0, $label_1, $label_2, $label_3, $label_4, $label_5, $label_6, $label_7, $label_8, $label_9, /*$label_10, $label_11,*/ $label_12, $label_13, $label_14, $label_15, $label_16, $label_17, $label_18, $label_19, $label_20, ''
);

$acumular_ate=12; // meses
$contador=sizeof($collection);$i=0;
$rentabilidade_indice_f = 100;
$benchmark_indice_f = 100;
$a_rentabilidade_indice_f = array();
$a_benchmark_indice_f = array();
$contador_ano_atual=0;
$a_data=array(0, 0);

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


$te = 1.660133353;

foreach( $collection as $item )
{
	$i++;

    $a_data = explode( "/", $item['mes_referencia'] );

    if( $tabela[0]['cd_indicador_tabela']==$item['cd_indicador_tabela'] )
    {
        $link=anchor("igp/rentabilidade_ci/detalhe/" . $item["cd_rentabilidade_ci"], "editar");
        $contador_ano_atual++;
    }
    else
    {
        $link='';
    }

	$rentabilidade = floatval($item['nr_rentabilidade']);
	$benchmark = floatval($item['nr_benchmark']);
	$diferenca_rentabilidade_benchmark = floatval( $rentabilidade ) - floatval( $benchmark );
	$rentabilidade_fator_f = floatval($rentabilidade)/100+1;
	$benchmark_fator_f = floatval($benchmark)/100+1;
	
	$a_rentabilidade_indice_f[] = floatval($rentabilidade_indice_f);
	$a_benchmark_indice_f[] = floatval($benchmark_indice_f);
	$rentabilidade_indice_f = floatval($rentabilidade_indice_f)*floatval($rentabilidade_fator_f);
	$benchmark_indice_f = floatval($benchmark_indice_f)*floatval($benchmark_fator_f);

	$rentabilidade_acu_f = '';
	$benchmark_acu_f = '';
	$minimo_f = 0;
	$maximo_f = 0;
	$poder_f = 0;

	if($i>=12)
	{
		$rentabilidade_acu_f = (  floatval($rentabilidade_indice_f) / floatval($a_rentabilidade_indice_f[sizeof($a_rentabilidade_indice_f)-12])-1  )*100;
		$benchmark_acu_f = (  floatval($benchmark_indice_f) / floatval($a_benchmark_indice_f[sizeof($a_benchmark_indice_f)-12])-1  ) * 100;

		$minimo_f = floatval($benchmark_acu_f) - (2*$te);
		$maximo_f = floatval($benchmark_acu_f) + (2*$te);

		//=J57-(K57-1)
		$poder_f = floatval($rentabilidade_acu_f)-(floatval($benchmark_acu_f)-1);
	}
	
	$peso_igp = floatval($item['nr_peso_igp']);
	$igp_mes_f = floatval($rentabilidade);
	$igp_acumulado_f = floatval($rentabilidade_acu_f);

	$igp_media_f = 0;
	$a_igp_media_f[]=$rentabilidade;
	if($i>=12)
	{
		$j=1;
		for( $k=sizeof($a_igp_media_f);$k>0;$k-- )
		{
			if( $j<=$acumular_ate )
			{
				$igp_media_f+=$a_igp_media_f[$k-1];

				$j++;
			}
		}
		$divisor=(sizeof($a_igp_media_f)<$acumular_ate)?sizeof($a_igp_media_f):$acumular_ate;
		$igp_media_f=floatval($igp_media_f)/$divisor;
	}
	
	$diferenca_acumulado_f = floatval($rentabilidade_acu_f)-floatval($benchmark_acu_f);
	
	$peso_f='';
	$peso_acumulado_f='';
	if($i>=12)
	{
		$peso_f=0;
		$peso_acumulado_f=0;

		//=SE(E58>=0;O58;SE(C58<0;SE(D58<0;(1-(E58/D58))*O58);(C58/D58*O58)))
		if(   floatval($diferenca_rentabilidade_benchmark)>=0   )
		{
			$peso_f = floatval($peso_igp);
		}
		else
		{
			if( floatval($rentabilidade)<0 )
			{
				if(floatval($benchmark)<0)
				{
					$peso_f = (1-(floatval($diferenca_rentabilidade_benchmark))/floatval($benchmark))*floatval($peso_igp);
				}
			}
			else
			{
				$peso_f = ( floatval($rentabilidade) / floatval($benchmark)*floatval($peso_igp) );
			}
		}

		//=SE(S57>=0;O57;SE(J57<0;SE(K57<0;(1-(S57/K57))*O57);(J57/K57*O57)))
		$peso_acumulado_f = 0;
		if(   floatval($diferenca_acumulado_f)>=0   )
		{
			$peso_acumulado_f = floatval($peso_igp);
		}
		else
		{
			if( floatval($rentabilidade_acu_f)<0 )
			{
				if(floatval($benchmark_acu_f)<0)
				{
					$peso_acumulado_f = (1-(floatval($diferenca_acumulado_f))/floatval($benchmark_acu_f))*floatval($peso_igp);
				}
			}
			else
			{
				$peso_acumulado_f = ( floatval($rentabilidade_acu_f) / floatval($benchmark_acu_f)*floatval($peso_igp) );
			}
		}
	}

	$peso_media_f = 0;
	$a_peso_media_f[]=$peso_f;
	if($i>=24)
	{
		$j=1;
		for( $k=sizeof($a_peso_media_f);$k>0;$k-- )
		{
			if( $j<=$acumular_ate )
			{
				$peso_media_f+=$a_peso_media_f[$k-1];

				$j++;
			}
		}
		$divisor=(sizeof($a_peso_media_f)<$acumular_ate)?sizeof($a_peso_media_f):$acumular_ate;
		$peso_media_f=floatval($peso_media_f)/$divisor;
	}

	$body[] = array(
		$contador--
		, $item['mes_referencia']
		, number_format($rentabilidade,4,',','.')
		, number_format($benchmark,4,',','.')
		, number_format($diferenca_rentabilidade_benchmark,4,',','.')
		, number_format($rentabilidade_fator_f,4,',','.')
		, number_format($benchmark_fator_f,4,',','.')
		, number_format($rentabilidade_indice_f,4,',','.')
		, number_format($benchmark_indice_f,4,',','.')
		, number_format($rentabilidade_acu_f,4,',','.')
		, number_format($benchmark_acu_f,4,',','.')
//		, number_format($minimo_f,4,',','.')
//		, number_format($maximo_f,4,',','.')
		, number_format($poder_f,4,',','.')
		, number_format($peso_igp,2,',','.')
		, number_format($igp_mes_f,4,',','.')
		, number_format($igp_acumulado_f,4,',','.')
		, number_format($igp_media_f,4,',','.')
		, number_format($diferenca_acumulado_f,4,',','.')
		, number_format($peso_f,2,',','.')
		, number_format($peso_acumulado_f,2,',','.')
		, number_format($peso_media_f,4,',','.')
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