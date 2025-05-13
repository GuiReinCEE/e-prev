<?php
$body=array();
$head = array(
    '#', $label_0, $label_12, $label_11, $label_10, $label_9, $label_8, $label_7, $label_6,
         $label_5, $label_4, $label_3, $label_2, $label_1, $label_13, $label_14,''
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
	$soma=0;
    $sm_valor_1 = 0;
    $sm_valor_2 = 0;
    $sm_valor_3 = 0;
    $sm_valor_4 = 0;
    $sm_valor_5 = 0;
    $sm_valor_6 = 0;
    $sm_valor_7 = 0;
    $sm_valor_8 = 0;
    $sm_valor_9 = 0;
    $sm_valor_10 = 0;
    $sm_valor_11 = 0;
    $sm_valor_12 = 0;
    $sm_valor_13 = 0;

	foreach( $collection as $item )
	{
		$a_data = explode( "/", $item['mes_referencia'] );

        $observacao = $item["observacao"];

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
        $nr_valor_12 = $item["nr_valor_12"];
        $nr_valor_13 = $item["nr_valor_13"];

		if( $item['fl_media']=='S' )
		{
			$link = '';

			$referencia = $item['ano_referencia'];
		}
		else
		{
			$link = anchor("indicador_plugin/administrativo_total_digitalizado/detalhe/" . $item["cd_administrativo_total_digitalizado"], "editar");

			$referencia = $item['mes_referencia'];
		}

		if( intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && $item['fl_media']!='S' )
		{
			$contador_ano_atual++;
			$media_ano[] = $nr_valor_1;
		}

		$body[] = array(
			 $contador--
			, $referencia
            , ($nr_valor_12!='')?$nr_valor_12:''
            , ($nr_valor_11!='')?$nr_valor_11:''
			, ($nr_valor_10!='')?$nr_valor_10:''
			, ($nr_valor_9!='')?$nr_valor_9:''
            , ($nr_valor_8!='')?$nr_valor_8:''
            , ($nr_valor_7!='')?$nr_valor_7:''
            , ($nr_valor_6!='')?$nr_valor_6:''
            , ($nr_valor_5!='')?$nr_valor_5:''
            , ($nr_valor_4!='')?$nr_valor_4:''
            , ($nr_valor_3!='')?$nr_valor_3:''
            , ($nr_valor_2!='')?$nr_valor_2:''
            , ($nr_valor_1!='')?$nr_valor_1:''
            , ($nr_valor_13!='')?$nr_valor_13:''
            , array($observacao, 'text-align:"left"')
			, $link 
		);

        $sm_valor_1 += $nr_valor_1;
        $sm_valor_2 += $nr_valor_2;
        $sm_valor_3 += $nr_valor_3;
        $sm_valor_4 += $nr_valor_4;
        $sm_valor_5 += $nr_valor_5;
        $sm_valor_6 += $nr_valor_6;
        $sm_valor_7 += $nr_valor_7;
        $sm_valor_8 += $nr_valor_8;
        $sm_valor_9 += $nr_valor_9;
        $sm_valor_10 += $nr_valor_10;
        $sm_valor_11 += $nr_valor_11;
        $sm_valor_12 += $nr_valor_12;
        $sm_valor_13 += $nr_valor_13;
       
	}

    if( sizeof($media_ano)>0 )
	{
        $body[] = array(
            0
            , "<b>".$item['ano_referencia']."<b/>"
            , $sm_valor_12
            , $sm_valor_11
            , $sm_valor_10
            , $sm_valor_9
            , $sm_valor_8
            , $sm_valor_7
            , $sm_valor_6
            , $sm_valor_5
            , $sm_valor_4
            , $sm_valor_3
            , $sm_valor_2
            , $sm_valor_1
            , $sm_valor_13
            , '', ''
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