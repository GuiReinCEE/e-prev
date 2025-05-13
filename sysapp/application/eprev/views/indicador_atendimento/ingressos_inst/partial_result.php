<?php
$body=array();
$head = array( 
	'#', $label_0, $label_1, $label_2, $label_4, $label_3, $label_5, ''
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
    $soma_total = 0;
    $soma_vl_1  = 0;
    $soma_vl_2  = 0;
    $soma_vl_3  = 0;
    $soma_vl_4  = 0;
	foreach( $collection as $item )
	{
		$a_data = explode( "/", $item['mes_referencia'] );

		$nr_meta = $item["nr_meta"];

		$nr_percentual_f = $item['nr_percentual_f'];

		if( $item['fl_media']=='S' )
		{
			$link = '';

			$referencia = " Total de " . $item['ano_referencia'];

		}
		else
		{
			$link = anchor("indicador_atendimento/ingressos_inst/detalhe/" . $item["cd_ingressos_inst"], "editar");

			$referencia = $item['mes_referencia'];
		}

        $nr_valor_1 = $item["nr_valor_1"];
        $nr_valor_2 = $item["nr_valor_2"];
        $nr_valor_3 = $item["nr_valor_3"];
        $nr_valor_4 = $item["nr_valor_4"];

        $nr_percentual_f = $item['nr_percentual_f'];

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
            , ($nr_valor_4!='')?$nr_valor_4:''
            , ($nr_valor_3!='')?$nr_valor_3:''
			, ($nr_percentual_f!='')?$nr_percentual_f.'':''
			, $link 
		);
		
		if( intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && $item['fl_media']!='S' )
		{
			$soma_total += $nr_percentual_f;
			$soma_vl_1  += $nr_valor_1;
			$soma_vl_2  += $nr_valor_2;
			$soma_vl_3  += $nr_valor_3;
			$soma_vl_4  += $nr_valor_4;
		}
	}

	if( sizeof($media_ano)>0 )
	{
		$body[] = array(
			0
			, '<b>Total de '.intval($tabela[0]['nr_ano_referencia']).'</b>'
			, $soma_vl_1
			, $soma_vl_2
            , $soma_vl_4
			, $soma_vl_3
			, '<big><b>'.intval( $soma_total ).' </b></big>'
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
