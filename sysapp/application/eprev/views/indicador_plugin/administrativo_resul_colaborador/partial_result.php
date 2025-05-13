<?php
$body=array();
$head = array( 
	'#', $label_0, $label_1, $label_2, $label_3, $label_4, $label_5, $label_6, $label_7, $label_8, $label_9, ''
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
    $soma = 0;
    $nr_valor_1f = 0;
    $nr_valor_2f = 0;
    $nr_valor_3f = 0;
    $soma_f = 0;

	foreach( $collection as $item )
	{
		$a_data = explode( "/", $item['mes_referencia'] );

		$nr_meta = $item["nr_meta"];

		$nr_percentual_f = $item['nr_percentual_f'];

		if( $item['fl_media']=='S' )
		{
			$link = '';

			$referencia = $item['ano_referencia'];

			$nr_valor_1 = $item["nr_valor_1"];
			$nr_valor_2 = $item["nr_valor_2"];
            $nr_valor_3 = $item["nr_valor_3"];
            $nr_valor_4 = $item["nr_valor_4"];
            $nr_valor_5 = $item["nr_valor_5"];
            $nr_valor_6 = $item["nr_valor_6"];
			$soma = $item['nr_percentual_f'];
            $observacao = '';

            if($nr_valor_4 == '')
                $nr_valor_4 = ($nr_valor_1 / $soma) * 100;

            if($nr_valor_5 == '')
                $nr_valor_5 = ($nr_valor_2 / $soma) * 100;

            if($nr_valor_6 == '')
                $nr_valor_6 = ($nr_valor_3 / $soma) * 100;
		}
		else
		{
			$link = anchor("indicador_plugin/administrativo_resul_colaborador/detalhe/" . $item["cd_administrativo_resul_colaborador"], "editar");

			$referencia = $item['mes_referencia'];

			$nr_valor_1 = $item["nr_valor_1"];
			$nr_valor_2 = $item["nr_valor_2"];
            $nr_valor_3 = $item["nr_valor_3"];
            $observacao = $item["observacao"];

			$soma = $nr_valor_1 + $nr_valor_2 + $nr_valor_3;

            $nr_valor_4 = ($nr_valor_1 / $soma) * 100;
            $nr_valor_5 = ($nr_valor_2 / $soma) * 100;
            $nr_valor_6 = ($nr_valor_3 / $soma) * 100;

            $nr_valor_1f += $nr_valor_1;
            $nr_valor_2f += $nr_valor_2;
            $nr_valor_3f += $nr_valor_3;

            $soma_f = $nr_valor_1f + $nr_valor_2f + $nr_valor_3f;
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
            , ($nr_valor_4!='')?number_format($nr_valor_4,2,',','.').' %':''
			, ($nr_valor_2!='')?$nr_valor_2:''
            , ($nr_valor_5!='')?number_format($nr_valor_5,2,',','.').' %':''
            , ($nr_valor_3!='')?$nr_valor_3:''
            , ($nr_valor_6!='')?number_format($nr_valor_6,2,',','.').' %':''
			, ($soma!='')?$soma:''
            , ($nr_meta!='')?number_format($nr_meta,2,',','.').' %':''
            , array($observacao, 'text-align:"left"')
			, ($item['fl_editar'] == "S" ? $link : "")
		);
	}

    if(sizeof($media_ano)>0)
    {
        $soma_f = $nr_valor_1f + $nr_valor_2f + $nr_valor_3f;

        $nr_valor_4f = ($nr_valor_1f / $soma_f) * 100;
        $nr_valor_5f = ($nr_valor_2f / $soma_f) * 100;
        $nr_valor_6f = ($nr_valor_3f / $soma_f) * 100;

        $body[] = array(
			 0
			, '<b>Total do ano  '.intval($tabela[0]['nr_ano_referencia']).'</b>'
            , $nr_valor_1f
            , number_format($nr_valor_4f,2,',','.')
			, $nr_valor_2f
            , number_format($nr_valor_5f,2,',','.')
            , $nr_valor_3f
            , number_format($nr_valor_6f,2,',','.')
			, $soma_f
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
