<?php
$body=array();
$head = array( 
	'#', $label_0, "", $label_1, $label_2, $label_3, $label_4, $label_6, ''
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
	$a_data=array(0, 0);
    
	foreach( $collection as $item )
	{
		$a_data = explode( "/", $item['mes_referencia'] );

        $link = anchor("indicador_poder/performance_carte_invest/detalhe/" . $item["cd_performance_carte_invest"], "editar");

		$nr_meta         = $item["nr_meta"];
        $nr_valor_1      = $item["nr_valor_1"];
        $nr_valor_2      = $item["nr_valor_2"];
		$nr_percentual_f = $item['nr_percentual_f'];
        $referencia      = $item['mes_referencia'];
        $nr_faixa        = $item['nr_faixa'];

		if( intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && $item['fl_media']!='S' )
		{
			$contador_ano_atual++;
		}

		$body[] = array(
			 $contador--
			, $referencia
			, indicador_status($item["fl_meta"], $item["fl_direcao"])
			, ($nr_valor_1!='')?number_format($nr_valor_1,4,',','.'):''
			, ($nr_valor_2!='')?number_format($nr_valor_2,4,',','.'):''
			, ($nr_percentual_f!='')?number_format($nr_percentual_f,4,',','.'):''
			, number_format($nr_meta,2,',','.')
            , $nr_faixa
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
