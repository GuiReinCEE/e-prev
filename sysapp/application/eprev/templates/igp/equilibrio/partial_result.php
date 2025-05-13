<?php
$body=array();
$head = array( 
	'#', $label_0, $label_1, $label_2, $label_3, $label_4, $label_5, $label_6, $label_7, $label_8, $label_9, $label_10, $label_11, $label_12, '', '', ''
);

$acum_tec=0;
$acum_mat=0;
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
        $link = anchor("igp/equilibrio/detalhe/" . $item["cd_equilibrio"], "editar");
        $contador_ano_atual++;
    }
    else
    {
        $link='';
    }

	$tecnica = floatval($item["nr_tecnica"]);
	$matematica = floatval($item["nr_matematica"]);
	$meta = floatval($item["nr_meta"]);
	$peso = floatval($item["nr_peso"]);

	$tecnica_matematica=( floatval($tecnica)/floatval($matematica) )*100;
	if($acum_tec=='')
	{
		$acum_tec=floatval($tecnica);
	}
	else
	{
		$acum_tec=( floatval($tecnica) + floatval($acum_tec) )/2;
	}
	if($acum_mat=='')
	{
		$acum_mat=floatval($matematica);
	}
	else
	{
		$acum_mat=( floatval($matematica) + floatval($acum_mat) )/2;
	}
	$acum_tec_mat=(floatval($acum_tec)/floatval($acum_mat))*100;

	// =SE(D178>E214????;F178;(D178*F178)/E214???)
	// D: TECNICA / MATEMATICA
	// E: META
	// F: PESO
	if( floatval($tecnica_matematica)>floatval($meta) )
	{
		$rf_mes_aux=floatval($peso);
	}
	else
	{
		$rf_mes_aux = ( (  floatval($tecnica_matematica) * floatval($peso)  ) / floatval($meta/100) ) / 100;
	}
	
	if( floatval($tecnica_matematica)>floatval($meta) )
	{
		$rf_mes = floatval($peso);
	}
	else
	{
		$rf_mes = ( (  floatval($tecnica_matematica) * floatval($peso)  ) / floatval($meta/100) ) / 100;
	}

	// =SE(I177>E177;F177;(I177*F177)/E177)
	// I: ACUM TEC / MAT
	// E: META
	// F: PESO
	if( floatval($acum_tec_mat)>floatval($meta) )
	{
		$rf_acum_aux = floatval($peso);
	}
	else
	{
		$rf_acum_aux = ( (  floatval($acum_tec_mat) * floatval($peso)  ) / floatval($meta/100) ) / 100;
	}

	if( floatval($acum_tec_mat)>floatval($meta) )
	{
		$rf_acum = floatval($peso);
	}
	else
	{
		$rf_acum = ( (  floatval($acum_tec_mat) * floatval($peso)  ) / floatval($meta/100) ) / 100;
	}

	//% MÉDIA MÓVEL
	$a_percentual_media_movel[]=$tecnica_matematica;
	$percentual_media_movel=0;

	$j=1;
	for( $i=sizeof($a_percentual_media_movel);$i>0;$i-- )
	{
		if( $j<=$acumular_ate )
		{
			$percentual_media_movel+=$a_percentual_media_movel[$i-1];

			$j++;
		}
	}

	$divisor=(sizeof($a_percentual_media_movel)<$acumular_ate)?sizeof($a_percentual_media_movel):$acumular_ate;
	$percentual_media_movel=floatval($percentual_media_movel)/$divisor;

	// = SE(N178>E214;F178;(N178*F178)/E214)
	// N: PERCENTUAL DE MÉDIA MÓVEL
	// E: META
	// F: PESO

	if( floatval($percentual_media_movel)>floatval($meta) )
	{
		$media_movel = floatval( $peso );
	}
	else
	{
		$media_movel = ( ( floatval($percentual_media_movel)*floatval($peso) ) / floatval($meta) );
	}

	// =SE(N183>E183;F183;(N183*F183)/E183)
	// N: PERCENTUAL DE MÉDIA MÓVEL
	// E: META
	// F: PESO
	if( floatval($percentual_media_movel)>floatval($meta) )
	{
		$media_movel2 = floatval( $peso );
	}
	else
	{
		$media_movel2 = ( ( floatval($percentual_media_movel)*floatval($peso) ) / floatval($meta) ) ;
	}

	$body[] = array(
		$contador--
		, $item['mes_referencia']
		, number_format( $tecnica, 2, ',', '.' )
		, number_format( $matematica, 2, ',', '.' )
		, number_format( $tecnica_matematica, 2, ',', '.' ).'%'
		, number_format( $meta, 2, ',', '.' ).'%'
		, number_format( $peso, 2, ',', '.' )
		, number_format( $acum_tec, 2, ',', '.' )
		, number_format( $acum_mat, 2, ',', '.' )
		, number_format( $acum_tec_mat, 2, ',', '.' ).'%'
		, number_format( $rf_mes_aux, 2, ',', '.' )
		, number_format( $rf_mes, 2, ',', '.' )
		, number_format( $rf_acum_aux, 2, ',', '.' )
		, number_format( $rf_acum, 2, ',', '.' )
		, number_format( $percentual_media_movel, 2, ',', '.' ).'%'
		, number_format( $media_movel, 2, ',', '.' )
		, number_format( $media_movel2, 2, ',', '.' )
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