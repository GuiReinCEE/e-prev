<?php
$body=array();
$head=array( 
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
    $contador_ano_atual=0;
    $a_data=array(0, 0);
	$contador=sizeof( $collection );

	foreach( $collection as $item )
	{
        $a_data = explode( "/", $item['mes_referencia'] );

		if( $tabela[0]['cd_indicador_tabela']==$item['cd_indicador_tabela'] )
		{
			$link=anchor("igp/beneficio_erro/detalhe/" . $item["cd_beneficio_erro"], "editar");
            $contador_ano_atual++;
		}
		else
		{
			$link='';
		}

		$beneficio_concedido = intval($item['nr_concedido']);
		$beneficio_com_erro = $item['nr_erro'];
		$percentual_incorrecoes = ( $beneficio_com_erro / $beneficio_concedido )*100;
		$meta_acertos = $item['nr_meta'];

		// BENEF CONC ACUMULADO: ultimos 12 meses
		$a_benef_conc_acumulado[]=intval($beneficio_concedido);
		$benef_conc_acumulado=0;
		$j=1;
		for( $i=sizeof($a_benef_conc_acumulado);$i>0;$i-- )
		{
			if( $j<=$acumular_ate )
			{
				$benef_conc_acumulado+=intval($a_benef_conc_acumulado[$i-1]);
				$j++;
			}
		}

		// BENEF C ERRO ACUMULADO: ultimos 12 meses
		$a_benef_com_erro_acumulado[]=intval($beneficio_com_erro);
		$benef_com_erro_acumulado=0;
		$j=1;
		for( $i=sizeof($a_benef_com_erro_acumulado);$i>0;$i-- )
		{
			if( $j<=$acumular_ate )
			{
				$benef_com_erro_acumulado+=intval($a_benef_com_erro_acumulado[$i-1]);
				$j++;
			}
		}

		$incorrecao_acumulada = ( intval($benef_com_erro_acumulado) / intval($benef_conc_acumulado) )*100;

		$peso=floatval($item['nr_peso']);
		$meta_por_resultado=(  ( floatval($meta_acertos)-floatval($percentual_incorrecoes) )/floatval($meta_acertos)  )*100;

		// RF MÊS    =SE(K65>1;J65;J65*K65) K = $meta_por_resultado J = $peso
		$rf_mes=($meta_por_resultado>100)?$peso:(  ($peso*$meta_por_resultado)/100  );

		$meta_por_acumulado=(  ( $meta_acertos-$incorrecao_acumulada )/$meta_acertos  )*100;

		// RF ACUM   =SE(N64<100;N64*J64;J64)  N = $meta_por_acumulado  J = $peso
		$rf_acum = (floatval($meta_por_acumulado)<100)?floatval(($meta_por_acumulado*$peso))/100:floatval($peso);
		
		//% MÉDIA MÓVEL, média dos últimos 12 meses do "% INCORRECOES"
		$a_percentual_incorrecoes[]=$percentual_incorrecoes;
		$percentual_media_movel=0;

		//% MÉDIA MÓVEL, média dos últimos 12 meses do "RF MES"
		$a_rf_mes[]=$rf_mes;
		$media_movel=0;

		$j=1;
		for( $i=sizeof($a_percentual_incorrecoes);$i>0;$i-- )
		{
			if( $j<=$acumular_ate )
			{
				$percentual_media_movel += $a_percentual_incorrecoes[$i-1];
				$media_movel += $a_rf_mes[$i-1];

				$j++;
			}
		}

		$divisor=(sizeof($a_percentual_incorrecoes)<$acumular_ate)?sizeof($a_percentual_incorrecoes):$acumular_ate;
		$percentual_media_movel=floatval($percentual_media_movel)/$divisor;
		$media_movel=floatval($media_movel)/$divisor;

		$body[] = array(
			$contador--
			, $item['mes_referencia']
			,array($beneficio_concedido,'text-align:right;')
			,array($beneficio_com_erro,'text-align:right;')
			,array(number_format($percentual_incorrecoes,2,',','.').'%','text-align:right;')
			,array(number_format($meta_acertos,2,',','.').'%','text-align:right;')
			,array($benef_conc_acumulado,'text-align:right;')
			,array($benef_com_erro_acumulado,'text-align:right;')
			,array(number_format($incorrecao_acumulada,2,',','.').'%','text-align:right;')
			,array(number_format($peso,2,',','.'),'text-align:right;')
			,array(number_format($meta_por_resultado,3,',','.'),'text-align:right;')
			,array(number_format($rf_mes,2,',','.'),'text-align:right;')
			,array(number_format($meta_por_acumulado,3,',','.'),'text-align:right;')
			,array(number_format($rf_acum,2,',','.'),'text-align:right;')
			,array(number_format($percentual_media_movel,2,',','.').'%','text-align:right;')
			,array(number_format($media_movel,2,',','.'),'text-align:right;')
			,($item['fl_editar'] == "S" ? $link : "")
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