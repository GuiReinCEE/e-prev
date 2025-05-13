<?php
$body=array();
$head = array( 
	'#', $label_0, $label_1, $label_2, $label_3, $label_4,$label_5, $label_7, ''
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
	$total_ano=array();
	$a_data=array(0, 0);
	foreach( $collection as $item )
	{
		$a_data = explode( "/", $item['mes_referencia'] );

		$nr_meta = $item["nr_meta"];
		$nr_referencial = $item["nr_referencial"];

		$nr_percentual_f = $item['nr_percentual_f'];

        $observacao = $item["observacao"];

		if( $item['fl_media']=='S' )
		{
			$link = '';

			$referencia = " Total de " . $item['ano_referencia'];

			$nr_valor_1 = '';
			$nr_valor_2 = '';
			$nr_percentual_f = $item['nr_percentual_f'];
		}
		else
		{
			$link = anchor("indicador_plugin/administrativo_rotatividade/detalhe/" . $item["cd_administrativo_rotatividade"], "editar");

			$referencia = $item['mes_referencia'];

			$nr_valor_1 = $item["nr_valor_1"];
			$nr_valor_2 = $item["nr_valor_2"];
			
			if($nr_percentual_f=='')
			{
				if( floatval($nr_valor_2)>0 )
				{
					$nr_percentual_f = ( floatval($nr_valor_2)/floatval($nr_valor_1) )*100;
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
			$total_ano[] = $nr_percentual_f;
		}

		$body[] = array(
			 $contador--
			, $referencia
			, ($nr_valor_1!='')?number_format($nr_valor_1,2,',','.'):''
			, ($nr_valor_2!='')?number_format($nr_valor_2,2,',','.'):''
			, ($nr_percentual_f!='')?number_format($nr_percentual_f,2,',','.').' %':''
			, number_format($nr_meta,2,',','.').' %'
			, number_format($nr_referencial,2,',','.').' %'
            , array($observacao, 'text-align:"left"')
			, $link 
		);
	}

	if( sizeof($total_ano)>0 )
	{
		$total = 0;
		foreach( $total_ano as $valor )
		{
			$total += $valor;
		}

		$total = $total / $contador_ano_atual;

		$body[] = array(
			0
			, '<b>Total de '.intval($tabela[0]['nr_ano_referencia']).'</b>'
			, ''
			, ''
			, '<big><b>'.number_format($total,2,',','.').' %</b></big>'
			, ''
			, ''
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
