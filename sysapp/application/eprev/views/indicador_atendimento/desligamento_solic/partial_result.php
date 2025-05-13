<?php
$body=array();
$head = array(
	'#', 
	$label_0, 
	$label_1, 
	$label_2, 
	$label_3, 
	$label_4, 
	$label_5, 
	$label_6, 
	$label_7, 
	$label_8, 
	$label_9, 
	$label_10, 
	$label_11,
	$label_12,
	''
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
    $soma_vl_5  = 0;
    $soma_vl_6  = 0;
    $soma_vl_7  = 0;
    $soma_vl_8  = 0;
    $soma_vl_9  = 0;
    $soma_vl_10  = 0;
    $soma_vl_11  = 0;

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
			$link = anchor("indicador_atendimento/desligamento_solic/detalhe/" . $item["cd_desligamento_solic"], "editar");

			$referencia = $item['mes_referencia'];
		}

        $nr_valor_1 = $item["nr_valor_1"];
        $nr_valor_2 = $item["nr_valor_2"];
        $nr_valor_3 = $item["nr_valor_3"];
        $nr_valor_4 = $item["nr_valor_4"];
        $nr_valor_5 = $item["nr_valor_5"];
        $nr_valor_6 = $item["nr_valor_6"];
        $nr_valor_7 = $item["nr_valor_7"];
        $nr_valor_8 = $item["nr_valor_8"];
        $nr_valor_9 = $item["nr_valor_9"];
        $nr_valor_10 = $item["nr_valor_10"];
        $nr_valor_11 = $item["nr_valor_11"];

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
            , ($nr_valor_3!='')?$nr_valor_3:''
            , ($nr_valor_4!='')?$nr_valor_4:''
            , ($nr_valor_5!='')?$nr_valor_5:''
            , ($nr_valor_6!='')?$nr_valor_6:''
            , ($nr_valor_7!='')?$nr_valor_7:''
            , ($nr_valor_8!='')?$nr_valor_8:''
            , ($nr_valor_9!='')?$nr_valor_9:''
            , ($nr_valor_10!='')?$nr_valor_10:''
            , ($nr_valor_11!='')?$nr_valor_11:''
			, ($nr_percentual_f!='')?$nr_percentual_f.'':''
			, $link
		);

        if( $item['fl_media']!='S' )
		{
            $soma_total += $nr_percentual_f;
            $soma_vl_1  += $nr_valor_1;
            $soma_vl_2  += $nr_valor_2;
            $soma_vl_3  += $nr_valor_3;
            $soma_vl_4  += $nr_valor_4;
            $soma_vl_5  += $nr_valor_5;
            $soma_vl_6  += $nr_valor_6;
            $soma_vl_7  += $nr_valor_7;
            $soma_vl_8  += $nr_valor_8;
            $soma_vl_9  += $nr_valor_9;
            $soma_vl_10  += $nr_valor_10;
            $soma_vl_11  += $nr_valor_11;
        }
	}

	if( sizeof($media_ano)>0 )
	{
		$body[] = array(
			0
			, '<b>Total de '.intval($tabela[0]['nr_ano_referencia']).'</b>'
			, $soma_vl_1
			, $soma_vl_2
			, $soma_vl_3
			, $soma_vl_4
            , $soma_vl_5
            , $soma_vl_6
            , $soma_vl_7
            , $soma_vl_8
            , $soma_vl_9
            , $soma_vl_10
            , $soma_vl_11
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
