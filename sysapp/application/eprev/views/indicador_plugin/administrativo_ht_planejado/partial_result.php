<?php
$body=array();
$head = array( 
	'#', $label_0, $label_1, $label_2, $label_3, $label_4, ''
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
    $soma_f = 0;
    $soma_1 = 0;
    $soma_2 = 0;
	foreach( $collection as $item )
	{
		$a_data = explode( "/", $item['mes_referencia'] );

		$nr_meta = $item["nr_meta"];

		$nr_percentual_f = $item['nr_percentual_f'];

		if( $item['fl_media']=='S' )
		{
			$link = '';

			$referencia = " Total de " . $item['ano_referencia'];

			$nr_valor_1 = $item["nr_valor_1"];
			$nr_valor_2 = $item["nr_valor_2"];
			$soma = $item['nr_percentual_f'];
            $observacao = '';
		}
		else
		{
			$link = anchor("indicador_plugin/administrativo_ht_planejado/detalhe/" . $item["cd_administrativo_ht_planejado"], "editar");

			$referencia = $item['mes_referencia'];

			$nr_valor_1 = $item["nr_valor_1"];
			$nr_valor_2 = $item["nr_valor_2"];
            $observacao = $item["observacao"];
			
			$soma = $nr_valor_1 + $nr_valor_2;
            $soma_1 += $nr_valor_1;
            $soma_2 += $nr_valor_2;
            $soma_f += $soma;
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
			, ($soma!='')?$soma:''
            , array($observacao, 'text-align:"left"')
			, $link 
		);
	}

	if( sizeof($media_ano)>0 )
	{

		$body[] = array(
			0
			, '<b>Total de '.intval($tabela[0]['nr_ano_referencia']).'</b>'
			, $soma_1
			, $soma_2
			, '<big><b>'.$soma_f.'</b></big>'
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
