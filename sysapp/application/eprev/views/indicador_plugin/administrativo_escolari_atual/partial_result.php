<?php
$body=array();
$head = array( 
	'#', $label_0, $label_1, $label_2, $label_3, $label_4, $label_5, $label_6, $label_7, $label_8, $label_9, $label_10, $label_11, ''
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

		$nr_percentual_f = $item['nr_percentual_f'];

		if( $item['fl_media']=='S' )
		{
			$link = '';

			$referencia = " Média de " . $item['ano_referencia'];

			$nr_valor_1 = '';
            $nr_valor_2 = '';
            $nr_valor_3 = '';
            $nr_valor_4 = '';
            $nr_valor_5 = '';
            $nr_valor_6 = '';
            $nr_valor_7 = '';
            $nr_valor_8 = '';
            $nr_valor_9 = '';
            $observacao = '';
			$nr_percentual_f = $item['nr_percentual_f'];

		}
		else
		{
			$link = anchor("indicador_plugin/administrativo_escolari_atual/detalhe/" . $item["cd_administrativo_escolari_atual"], "editar");

			$referencia = $item['mes_referencia'];

			$nr_valor_1 = $item["nr_valor_1"];
            $nr_valor_2 = $item["nr_valor_2"];
            $nr_valor_3 = $item["nr_valor_3"];
            $nr_valor_4 = $item["nr_valor_4"];
            $nr_valor_5 = $item["nr_valor_5"];
            $nr_valor_6 = $item["nr_valor_6"];
            $nr_valor_7 = $item["nr_valor_7"];
            $nr_valor_8 = $item["nr_valor_8"];
            $nr_valor_9 = $item["nr_valor_9"];
            $observacao = $item["observacao"];

            $soma = $nr_valor_1 + $nr_valor_2 + $nr_valor_3 + $nr_valor_4 + $nr_valor_5 + $nr_valor_6 + $nr_valor_7
                + $nr_valor_8 + $nr_valor_9;
		
		}

		if( intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && $item['fl_media']!='S' )
		{
			$contador_ano_atual++;
			$media_ano[] = $nr_percentual_f;
		}

		$body[] = array(
			 $contador--
			, $referencia
			, ($nr_valor_1!='')?number_format($nr_valor_1,2,',','.'):''
			, ($nr_valor_2!='')?number_format($nr_valor_2,2,',','.'):''
            , ($nr_valor_3!='')?number_format($nr_valor_3,2,',','.'):''
            , ($nr_valor_4!='')?number_format($nr_valor_4,2,',','.'):''
            , ($nr_valor_5!='')?number_format($nr_valor_5,2,',','.'):''
            , ($nr_valor_6!='')?number_format($nr_valor_6,2,',','.'):''
            , ($nr_valor_7!='')?number_format($nr_valor_7,2,',','.'):''
            , ($nr_valor_8!='')?number_format($nr_valor_8,2,',','.'):''
            , ($nr_valor_9!='')?number_format($nr_valor_9,2,',','.'):''
			, ($soma!='')?number_format($soma,2,',','.'):''
            , array($observacao, 'text-align:"left"')
			, ($item['fl_editar'] == "S" ? $link : "")
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
/*
		$body[] = array(
			0
			, '<b>Média de '.intval($tabela[0]['nr_ano_referencia']).'</b>'
			, ''
			, ''
			, '<big><b>'.app_decimal_para_php( $media ).' %</b></big>'
			, ''
			, ''
		);
 * 
 */
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
