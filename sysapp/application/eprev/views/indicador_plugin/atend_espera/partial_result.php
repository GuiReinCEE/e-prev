<?php
$body=array();
$head = array( 
	'#', $label_0, /*$label_1,*/ $label_2, $label_3, $label_4, $label_6, ''
);

if(sizeof($tabela)<=0)
{
	echo "N�o foi identificado per�odo aberto para o Indicador";
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
    echo anchor_popup("indicador/apresentacao/detalhe/".intval( $tabela[0]['cd_indicador_tabela'] ), 'Visualizar apresenta��o', $ar_janela);

	$contador_ano_atual=0;
	$contador = sizeof($collection);
	$media_ano=array();
	$a_data=array(0, 0);
	$nr_total_valor_1     = 0;
	$nr_total_valor_2 	  = 0;
	$nr_total_tempo_medio = 0;
	$nr_total_meta 		  = 0;
	foreach( $collection as $item )
	{
		$a_data = explode( "/", $item['mes_referencia'] );

		$nr_meta = $item["nr_meta"];

		$nr_percentual_f = $item['nr_percentual_f'];
        $observacao = $item["observacao"];

		if( $item['fl_media']=='S' )
		{
			$link = '';

			$referencia = " M�dia de " . $item['ano_referencia'];

			$nr_valor_1 = '';
			$nr_valor_2 = number_format($item['nr_valor_2'], 2, ',', '.');
			$nr_percentual_f = $item['nr_percentual_f'];
		}
		else
		{
			$link = anchor("indicador_plugin/atend_espera/detalhe/" . $item["cd_atend_espera"], "editar");

			$referencia = $item['mes_referencia'];

			$nr_valor_1 = $item["nr_valor_1"];
			$nr_valor_2 = $item["nr_valor_2"];

			$nr_total_valor_1 += $item["nr_valor_1"];
			$nr_total_valor_2 += $item["nr_valor_2"];
			$nr_total_meta 	  += $item["nr_meta"];
			
			if($nr_percentual_f=='')
			{
				if( floatval($nr_valor_2)>0 )
				{
					$nr_percentual_f = ( floatval($nr_valor_1)/floatval($nr_valor_2) );
				}
				else
				{
					$nr_percentual_f = '0';
				}
			}

			$nr_total_tempo_medio += $nr_percentual_f;
		}
		
		if( intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && $item['fl_media']!='S')
		{
			$contador_ano_atual++;
		}
		
		if( intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && $item['fl_media']!='S' && intval($nr_valor_1) > 0 )
		{
			$media_ano[] = $nr_percentual_f;
		}

		$body[] = array(
			 $contador--
			, $referencia
			//, ($nr_valor_1!='')?$nr_valor_1:''
			, ($nr_valor_2!='')?$nr_valor_2:''
			, ($nr_percentual_f!='')?number_format($nr_percentual_f,2,',','.').'':''
			, number_format($nr_meta,2,',','.').''
            , array($observacao, 'text-align:"left"')
			, $link 
		);
	}

	if( intval($contador_ano_atual) > 0 )
	{
		/*$media = 0;
		foreach( $media_ano as $valor )
		{
			$media += $valor;
		}

		$media = number_format( ($media / sizeof($media_ano)), 2 );*/

		$nr_valor_2 = $nr_total_valor_2 / $contador_ano_atual;
		$nr_tempo_medio = (floatval($nr_total_tempo_medio) / floatval($contador_ano_atual));
		$nr_meta = (floatval($nr_total_meta) / floatval($contador_ano_atual));

		$body[] = array(
			0
			, '<b>M�dia de '.intval($tabela[0]['nr_ano_referencia']).'</b>'
			//, ''
			, '<big><b>'.number_format($nr_valor_2, 2, ',', '.').'</b></big>'
			, '<big><b>'.number_format($nr_tempo_medio, 2, ',', '.').'</b></big>'
			, '<big><b>'.number_format($nr_meta, 2, ',', '.').'</b></big>'
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
