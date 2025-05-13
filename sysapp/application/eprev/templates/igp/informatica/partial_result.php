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
        $link=anchor("igp/informatica/detalhe/" . $item["cd_informatica"], "editar");
        $contador_ano_atual++;
    }
    else
    {
        $link='';
    }

	$expediente = $item['nr_expediente'];
	$bco_fora = $item["nr_bco_fora"];
	$tempo_perc = $item["nr_tempo_perc"];
	$meta = $item["nr_meta"];
	
	// MNUTOS EXPED: ultimos 12 meses
	$a_minutos_exped[]=intval($expediente);
	$minutos_exped=0;
	$j=1;
	for( $i=sizeof($a_minutos_exped);$i>0;$i-- )
	{
		if( $j<=$acumular_ate )
		{
			$minutos_exped+=intval($a_minutos_exped[$i-1]);
			$j++;
		}
	}

	// MNUTOS BCO FORA: ultimos 12 meses
	$a_minutos_bco_fora[]=intval($bco_fora);
	$minutos_bco_fora=0;
	$j=1;
	for( $i=sizeof($a_minutos_bco_fora);$i>0;$i-- )
	{
		if( $j<=$acumular_ate )
		{
			$minutos_bco_fora+=intval($a_minutos_bco_fora[$i-1]);
			$j++;
		}
	}

	// =(G179/F179)
	$perc_bco_indisp = (intval($minutos_bco_fora) / intval($minutos_exped))*100;

	$peso = $item["nr_peso"];

	// =(E179-D179)/E179
	$result_por_meta = ( ( floatval($meta)-floatval($tempo_perc) )/floatval($meta) )*100;

	// RF MES - =SE(J179>1;I179;I179*J179)
	if( (floatval($result_por_meta)/100)>1 )
	{
		$rf_mes = floatval($peso);
	}
	else
	{
		$rf_mes = floatval($peso)*($result_por_meta/100);
	}

	// META PERCENT ACUM - =(E179-H179)/E179
	$meta_perc_acum=( ( floatval($meta)-floatval($perc_bco_indisp) ) / floatval($meta) ) * 100 ;

	// =SE(M179>1;I179;I179*M179)
	if( (floatval($meta_perc_acum)/100)>1 )
	{
		$meta_perc_acum_aux=floatval($peso);
	}
	else
	{
		$meta_perc_acum_aux=( floatval($peso)*(floatval($meta_perc_acum)/100) );
	}

	//% MÉDIA MÓVEL
	$a_percentual_media_movel[]=floatval($tempo_perc);
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
		, array( number_format( $expediente, 0,',','.' ), 'text-align:right;' )
		, array( number_format( $bco_fora, 0,',','.'  ), 'text-align:right;' )
		, array( number_format( $tempo_perc, 2,',','.' ).'%', 'text-align:right;' )
		, array( number_format( 100-floatval($meta), 2,',','.' ).'%', 'text-align:right;' )
		, array( number_format( $minutos_exped, 0, ',','.' ), 'text-align:right;' )
		, array( number_format( $minutos_bco_fora, 0, ',','.' ), 'text-align:right;' )
		, array( number_format( $perc_bco_indisp, 2,',','.' ).'%', 'text-align:right;' )
		, array( number_format( $peso, 2,',','.' ), 'text-align:right;' )
		, array( number_format( $result_por_meta, 2,',','.' ).'%', 'text-align:right;' )
		, array( number_format( $rf_mes, 2,',','.' ), 'text-align:right;' )
		, array( number_format( $meta_perc_acum, 2,',','.' ).'%', 'text-align:right;' )
		, array( number_format( $meta_perc_acum_aux, 2,',','.' ), 'text-align:right;' )
		, array( number_format( $percentual_media_movel, 2,',','.' ).'%', 'text-align:right;' )
		, array( number_format( $media_movel, 2,',','.' ), 'text-align:right;' )
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