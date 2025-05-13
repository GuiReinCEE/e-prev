<?php
$body=array();
$head = array( 
	'#', $label_0, $label_1, $label_2, ''
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
	$total=0;
	$a_data=array(0, 0);
	$total_ano=0;
	
	
	#echo "<PRE>".print_r($collection,true)."</PRE>";
	

	foreach( $collection as $item )
	{
		$a_data = explode( "/", $item['mes_referencia'] );
        $observacao = $item["observacao"];
		
		if( $item['fl_media']=='S' )
		{
			$link = '';
			$referencia = '<b>Total de '.$item['ano_referencia'].'</b>';
			$nr_valor_1 = $item['nr_valor_1'];
		}
		else
		{
			$link = anchor("indicador_plugin/atend_ssocial_indiv/detalhe/" . $item["cd_atend_ssocial_indiv"], "editar");
			$referencia = $item['mes_referencia'];
			$nr_valor_1 = $item['nr_valor_1'];
		}

		if( intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && $item['fl_media']!='S' )
		{
			$contador_ano_atual++;
			$total_ano+= $item['nr_valor_1'];
		}

		$body[] = array(
			$contador--
			, $referencia
			, $nr_valor_1
            , array($observacao, 'text-align:"left"')
			, $link
		);
	}

	$body[] = array(
		0
		, '<b>Total de '.$tabela[0]['nr_ano_referencia'].'</b>'
		, number_format($total_ano,0,",",".")
		, ''
		, ''
	);

	echo "<input type='hidden' id='mes_input' name='mes_input' value='".$a_data[0]."' />";
	echo "<input type='hidden' id='contador_input' name='contador_input' value='".$contador_ano_atual."' />";

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();
}
?>
