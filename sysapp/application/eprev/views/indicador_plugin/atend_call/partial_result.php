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
	$ar_janela = array(
				  'width'      => '700',
				  'height'     => '500',
				  'scrollbars' => 'yes',
				  'status'     => 'yes',
				  'resizable'  => 'yes',
				  'screenx'    => '0',
				  'screeny'    => '0'
				);
	echo '<div style="text-align:center;">'.br().anchor_popup("indicador/apresentacao/detalhe/".intval( $tabela[0]['cd_indicador_tabela'] ), '[Visualizar Apresenta��o]', $ar_janela)."</div>";
	
	$contador_ano_atual=0;
	$contador = sizeof($collection);
	$media_ano=array();
	$a_data=array(0, 0);
	$nr_media_2 = 0;
	foreach( $collection as $item )
	{
		$a_data = explode( "/", $item['mes_referencia'] );

		$nr_meta = $item["nr_meta"];
        $observacao = $item["observacao"];

		$nr_percentual_f = $item['nr_percentual_f'];

		if( $item['fl_media']=='S' )
		{
			$link = '';

			$referencia = " Resultado de " . $item['ano_referencia'];

			$nr_valor_1 = '';
			$nr_valor_2 = '';
			$nr_meta = '';
			$nr_percentual_f = $item['nr_percentual_f'];
		}
		else
		{
			$link = anchor("indicador_plugin/atend_call/detalhe/" . $item["cd_atend_call"], "editar");

			$referencia = $item['mes_referencia'];

			$nr_valor_1 = $item["nr_valor_1"];
			$nr_valor_2 = $item["nr_valor_2"];
			$nr_meta    = $item["nr_meta"];

			$nr_media_2 += $item["nr_valor_2"];
			
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
		}

		if( intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && $item['fl_media']!='S' )
		{
			$contador_ano_atual++;
			$media_ano[] = $nr_percentual_f;
		}

		$body[] = array(
			 $contador--
			, $referencia
			//, ($nr_valor_1!='')?$nr_valor_1:''
			, ($nr_valor_2!='')?$nr_valor_2:''
			, number_format($item['nr_percentual_f'],2,',','.')
			, ($nr_meta!='')?number_format($nr_meta,2,',','.'):''
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
			, '<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>'
			, $nr_media_2
			, '<big><b>'.number_format($media,2,',','.').'</b></big>'
			, '<big><b>'.number_format($nr_meta,2,',','.').'</b></big>'
            , ''
            , ''
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
