<?php
$body=array();
$head = array(
	'#', $label_0, $label_1, $label_2, $label_3, ''
);

#$tabela = indicador_tabela_aberta( intval( enum_indicador::INVESTIMENTO_ENQUADRAMENTO_POLITICA_INVESTIMENTOS ) );

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
	$nr_acumulado_anterior = 0;
	foreach( $collection as $item )
	{
		$a_data = explode( "/", $item['mes_referencia'] );

		$nr_meta = floatval($item["nr_meta"]);
		$nr_valor_1 = floatval($item["nr_valor_1"]);

        $observacao = $item["observacao"];

		if( $item['fl_media']=='S' )
		{
			$link = '';
			$referencia = " Dezembro de " . $item['ano_referencia'];
		}
		else
		{
			$link = anchor("indicador_plugin/investimento_pi/detalhe/" . $item["cd_investimento_pi"], "editar");
			$referencia = $item['mes_referencia'];
		}

		if( intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && $item['fl_media']!='S' )
		{
			$contador_ano_atual++;
		}

		$body[] = array(
			 $contador--
			, $referencia
			, ($nr_valor_1!='')?number_format($nr_valor_1,2,',','.').' %':''
			, ($nr_meta!='')?number_format($nr_meta,2,',','.').' %':''
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
