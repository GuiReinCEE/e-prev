<?php
$body=array();
$head = array( 
	'#', $label_0, $label_1, $label_2, $label_3, $label_4, $label_5, $label_6, $label_8, ''
);

if(sizeof($tabela)<=0)
{
	echo "Não foi identificado período aberto para o Indicador";
}
else
{
	$ar_janela = array(
				  'width'      => '700',
				  'height'     => '500',
				  'scrollbars' => 'yes',
				  'status'     => 'yes',
				  'resizable'  => 'yes',
				  'screenx'    => '0',
				  'screeny'    => '0'
				);
	echo '<div style="text-align:center;">'.br().anchor_popup("indicador/apresentacao/detalhe/".intval( $tabela[0]['cd_indicador_tabela'] ), '[Visualizar Apresentação]', $ar_janela)."</div>";

	$contador_ano_atual=0;
	$contador = sizeof($collection);
	$media_ano=array();
	$a_data=array(0, 0);
	foreach( $collection as $item )
	{
		$a_data = explode( "/", $item['mes_referencia'] );

		$nr_meta = $item["nr_meta"];

		$nr_percentual_f = $item['nr_percentual_f'];

        $observacao = $item["observacao"];

		if( $item['fl_media']=='S' )
		{
			$link = '';

			$referencia = " Média de " . $item['ano_referencia'];

			$nr_valor_1 = '';
			$nr_valor_2 = '';
            $nr_valor_3 = '';
			$nr_valor_4 = '';
			$nr_percentual_f = $item['nr_percentual_f'];
		}
		else
		{
			$link = anchor("indicador_plugin/ri_info_divul_fora_prazo/detalhe/" . $item["cd_ri_info_divul_fora_prazo"], "editar");

			$referencia = $item['mes_referencia'];

			$nr_valor_1 = $item["nr_valor_1"];
			$nr_valor_2 = $item["nr_valor_2"];
            $nr_valor_3 = $item["nr_valor_3"];
            $nr_valor_4 = $item["nr_valor_4"];
			
			if($nr_percentual_f == '')
			{
                if((floatval($nr_valor_1) > 0) AND (floatval($nr_valor_3) > 0))
                {
                    $nr_percentual_f = ( floatval($nr_valor_2)/floatval($nr_valor_1) ) * $enum_externo+( floatval($nr_valor_4)/floatval($nr_valor_3)) * $enum_interno;
                }
                else 
                {
                    $nr_percentual_f = 0;
                }
            }
		}

		if( intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && $item['fl_media']!='S' )
		{
			$contador_ano_atual++;
			$media_ano[] = $nr_percentual_f;
		}

		$body[] = array(
			 $contador--
			, $referencia
			, ($nr_valor_1!='')?$nr_valor_1:''
			, ($nr_valor_2!='')?$nr_valor_2:''
            , ($nr_valor_3!='')?$nr_valor_3:''
			, ($nr_valor_4!='')?$nr_valor_4:''
			, number_format($nr_percentual_f,2,',','.').' %'
			, number_format($nr_meta,2,',','.').' %'
            , array($observacao, 'text-align:"left"')
			, $link 
		);
	}

	if( sizeof($media_ano)>0 )
	{
        
		$media = 0;
		foreach( $media_ano as $valor )
		{
			$media += $valor;
		}

		$media = number_format( ($media / sizeof($media_ano)), 2 );

		$body[] = array(
			0
			, '<b>Média de '.intval($tabela[0]['nr_ano_referencia']).'</b>'
			, ''
			, ''
            , ''
            , ''
			, '<big><b>'.app_decimal_para_php( $media ).' %</b></big>'
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
