<?php
$body=array();
$head=array( 
	'#', $label_0, $label_1, $label_2, $label_3, $label_4, $label_5, $label_6, $label_7, $label_8, $label_9, $label_10, $label_11, $label_12, $label_13, $label_14, $label_15, ''
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
    echo anchor_popup("indicador/apresentacao/detalhe/".intval( $tabela[0]['cd_indicador_tabela'] ), 'Visualizar apresentação', $ar_janela);


foreach( $collection as $item )
{
    $a_data = explode( "/", $item['mes_referencia'] );

    if( $tabela[0]['cd_indicador_tabela']==$item['cd_indicador_tabela'] )
    {
        $link=anchor("igp/treinamento/detalhe/".$item["cd_treinamento"], "editar");
        $contador_ano_atual++;
    }
    else
    {
        $link='';
    }
    
	$mes_ano=split( '/',$item["mes_referencia"] );

	$hora=floatval( $item['nr_hora'] );
	$homem=floatval( $item['nr_homem'] );
	$meta=floatval($item['nr_meta']);
	$meta_ano=floatval($item["nr_meta_ano"]);
	$peso=floatval($item["nr_peso"]);

	// =B82/C82+D81
	if($mes_ano[0]==1) // todo janeiro inicia novamente.
	{
		$hora_homem_treinamento=floatval($hora/$homem);
	}
	else // os meses seguintes, acumula.
	{
		$hora_homem_treinamento=floatval($hora/$homem+$hora_homem_treinamento);
	}

	$a_horas_acumulado[]=intval($hora);
	$horas_acumulado=0;
	$j=1;
	for( $i=sizeof($a_horas_acumulado);$i>0;$i-- )
	{
		if( $j<=$acumular_ate )
		{
			$horas_acumulado+=intval($a_horas_acumulado[$i-1]);
			$j++;
		}
	}

	$a_empregado_acumulado[]=intval($homem);
	$empregado_acumulado=0;
	$j=1;
	for( $i=sizeof($a_empregado_acumulado);$i>0;$i-- )
	{
		if( $j<=$acumular_ate )
		{
			$empregado_acumulado+=intval($a_empregado_acumulado[$i-1]);
			$j++;
		}
	}
	
	$divisor=(sizeof($a_empregado_acumulado)<$acumular_ate)?sizeof($a_empregado_acumulado):$acumular_ate;
	$empregado_acumulado=floatval($empregado_acumulado)/$divisor;

	$hora_homem_acumulado = floatval($horas_acumulado)/floatval($empregado_acumulado);

	// =SE(D82>0;D82/E82;1)     hht     mm
	if( floatval($hora_homem_treinamento)>0 )
	{
		$resultado_meta = ( floatval($hora_homem_treinamento) / floatval($meta) ) * 100;
	}
	else
	{
		$resultado_meta = 100;
	}

	// =SE(J82>1;I82;I82*J82)
	if(floatval($resultado_meta/100)>1)
	{
		$rf_mes=$peso;
	}
	else
	{
		$rf_mes=( floatval($peso)*floatval($resultado_meta/100) );
	}

	// todo ano de 2008 e janeiro de 2009 usam META_ANO para calculo
	if( intval($mes_ano[1])==2008 || (intval($mes_ano[0])==1 && intval($mes_ano[1])==2009) )
	{
		$meta_perc_acumulado=( floatval($hora_homem_acumulado)/floatval($meta_ano) )*100;
	}
	else
	{
		$meta_perc_acumulado=( floatval($hora_homem_acumulado)/floatval($meta) )*100;
	}

	//=SE(M82>1;I82;I82*M82)
	if(floatval($meta_perc_acumulado/100)>1)
	{
		$rf_acum=$peso;
	}
	else
	{
		$rf_acum=( floatval($peso)*floatval($meta_perc_acumulado/100) );
	}

	//% MÉDIA MÓVEL
	$a_percentual_media_movel[]=$hora_homem_treinamento;
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
		, number_format($hora, 0)
		, number_format($homem, 0)
		, number_format($hora_homem_treinamento, 2,',','.')
		, number_format($meta, 2,',','.')
		, number_format($horas_acumulado, 0,'.','')
		, number_format($empregado_acumulado, 0)
		, number_format($hora_homem_acumulado, 2,',','.')
		, number_format($peso, 0)
		, number_format($resultado_meta, 0).'%'
		, number_format($rf_mes, 2,',','.')
		, number_format($meta_perc_acumulado, 3, '.', '').'%'
		, number_format($rf_acum, 2,',','.')
		, number_format($percentual_media_movel, 2,',','.')
		, number_format($media_movel, 2,',','.')
		, number_format($meta_ano, 0)
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