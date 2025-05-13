<?php
$body=array();
$head = array( 
	'#', $label_0, $label_1, $label_2, $label_3, $label_4, $label_5, ''
);

#$tabela = indicador_tabela_aberta( intval( enum_indicador::RH_EXECUCAO_PLANO_TREINAMENTO ) );

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
	$a_data=array(0, 0);
	foreach( $collection as $item )
	{
		$a_data = explode( "/", $item['mes_referencia'] );

		$nr_meta = $item["nr_meta"];

		if( $item['fl_media']=='S' )
		{
			$link = '';

			$referencia = " Acumulado de " . $item['ano_referencia'];

			$nr_valor_1= '';
			$nr_valor_2= '';
            $observacao = '';
			$nr_percentual_f = $item['nr_percentual_f']; // valor da m�dia dos anos anteriores � gravada nessa coluna quando o per�odo � fechado
		}
		else
		{
			$link = anchor("indicador_plugin/administrativo_ept/detalhe/" . $item["cd_administrativo_ept"], "editar");

			$referencia = $item['mes_referencia'];

			$nr_valor_1 = $item["nr_valor_1"];
			$nr_valor_2 = $item["nr_valor_2"];
            $observacao = $item["observacao"];

			$nr_percentual_f = (floatval($nr_valor_1)/floatval($nr_valor_2)) * 100;
		}

		if( intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && $item['fl_media']!='S' )
		{
			$contador_ano_atual++;
		}

		$body[] = array(
			 $contador--
			, $referencia
			, ($nr_valor_1!='')?number_format($nr_valor_1,0):''
			, ($nr_valor_2!='')?number_format($nr_valor_2,0):''
			, ($nr_percentual_f!='')?number_format($nr_percentual_f, 2, ',', '.'):''
			, ($nr_meta!='')?number_format($nr_meta,2,',','.'):''
            , array($observacao, 'text-align:"left"')
			, $link 
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
