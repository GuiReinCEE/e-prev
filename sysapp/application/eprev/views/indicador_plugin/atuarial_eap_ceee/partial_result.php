<?php
$body=array();
$head = array( 
	'#', $label_0, $label_1, $label_2, $label_3, $label_4, $label_6, ''
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

	$contador_ano_atual=0;
	$contador = sizeof($collection);
	$media_ano=array();
	$a_data=array(0, 0);
	foreach( $collection as $item )
	{
		$a_data = explode( "/", $item['mes_referencia'] );

		$nr_meta = $item["nr_meta"];

		$nr_variacao_f = $item['nr_variacao_f'];

        $observacao = $item["observacao"];

		if( $item['fl_media']!='S' )
		{
			$link = anchor("indicador_plugin/atuarial_eap_ceee/detalhe/" . $item["cd_atuarial_eap_ceee"], "editar");

			$referencia = $item['mes_referencia'];

			$nr_reserva_tecnica = $item["nr_reserva_tecnica"];
			$nr_provisao_matematica = $item["nr_provisao_matematica"];
			
			if($nr_variacao_f=='')
			{
				if( floatval($nr_reserva_tecnica)>0 )
				{
					$nr_variacao_f = ( ( floatval($nr_reserva_tecnica)/floatval($nr_provisao_matematica) ) ) ;
				}
				else
				{
					$nr_variacao_f = '';
				}
			}


		if( intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && $item['fl_media']!='S' )
		{
			$contador_ano_atual++;
			$media_ano[] = $nr_variacao_f;
		}

		$body[] = array(
			 $contador--
			, $referencia
			, ($nr_reserva_tecnica!='')?number_format($nr_reserva_tecnica,2,',','.'):''
			, ($nr_provisao_matematica!='')?number_format($nr_provisao_matematica,2,',','.'):''
			, ($nr_variacao_f!='')?number_format($nr_variacao_f,4,',','.').'':''
			, number_format($nr_meta,4,',','.').''
            , array($observacao, 'text-align:"left"')
			, $link 
		);
		}
	}

	if( sizeof($media_ano)>0 )
	{
		$media = 0;
		foreach( $media_ano as $valor )
		{
			$media += $valor;
		}

		$media = number_format( ($media / sizeof($media_ano)), 4 );

		$body[] = array(
			0
			, '<b>Média de '.intval($tabela[0]['nr_ano_referencia']).'</b>'
			, ''
			, ''
			, '<big><b>'.app_decimal_para_php( $media ).'</b></big>'
			, ''
			, '', ''
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
