<?php
$body=array();
$head = array( 
	'#', $label_0, $label_1, $label_2, $label_3, $label_4, $label_5, $label_6, $label_7, $label_8, $label_9, $label_10, $label_11,''
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
    $contador_2 = 1;
	$media_ano=array();
	$a_data=array(0, 0);

    $soma_mes_vl1 = 0;
    $soma_mes_vl2 = 0;
    $soma_mes_vl3 = 0;
    $soma_mes_vl4 = 0;
    $soma_mes_vl5 = 0;
    $soma_mes_vl6 = 0;
    $soma_mes_vl7 = 0;
    $soma_mes_vl8 = 0;
    $soma_mes_vl9 = 0;
    $soma_mes_tl  = 0;

    $mes = '';
    
    $soma_ano_vl1 = 0;
    $soma_ano_vl2 = 0;
    $soma_ano_vl3 = 0;
    $soma_ano_vl4 = 0;
    $soma_ano_vl5 = 0;
    $soma_ano_vl6 = 0;
    $soma_ano_vl7 = 0;
    $soma_ano_vl8 = 0;
    $soma_ano_vl9 = 0;
    $soma_ano_tl  = 0;

    $soma_ben_vl1 = 0;
    $soma_ben_vl2 = 0;
    $soma_ben_vl3 = 0;
    $soma_ben_vl4 = 0;
    $soma_ben_vl5 = 0;
    $soma_ben_vl6 = 0;
    $soma_ben_vl7 = 0;
    $soma_ben_vl8 = 0;
    $soma_ben_vl9 = 0;
    $soma_ben_tl  = 0;

	foreach( $collection as $item )
	{
		$a_data = explode( "/", $item['mes_referencia'] );

		$nr_meta = $item["nr_meta"];

		$nr_percentual_f = $item['nr_percentual_f'];

        if($mes != $a_data[0])
        {
            if($mes != '')
            {
                $body[] = array(
                    $contador_2
                    , '<b>Total de '.$mes.'/'.$item['ano_referencia'].'</b>'
                    , '<b>Benefício</b>'
                    , '<b>'.$soma_ben_vl1.'</b>'
                    , '<b>'.$soma_ben_vl2.'</b>'
                    , '<b>'.$soma_ben_vl3.'</b>'
                    , '<b>'.$soma_ben_vl4.'</b>'
                    , '<b>'.$soma_ben_vl5.'</b>'
                    , '<b>'.$soma_ben_vl6.'</b>'
                    , '<b>'.$soma_ben_vl7.'</b>'
                    , '<b>'.$soma_ben_vl8.'</b>'
                    , '<b>'.$soma_ben_vl9.'</b>'
                    , '<b>'.$soma_ben_tl.'</b>'
                    , ''
                );

                $contador_2++;
                $soma_ben_vl1 = 0;
                $soma_ben_vl2 = 0;
                $soma_ben_vl3 = 0;
                $soma_ben_vl4 = 0;
                $soma_ben_vl5 = 0;
                $soma_ben_vl6 = 0;
                $soma_ben_vl7 = 0;
                $soma_ben_vl8 = 0;
                $soma_ben_vl9 = 0;
                $soma_ben_tl  = 0;


                $body[] = array(
                    $contador_2
                    , '<b>Total de '.$mes.'/'.$item['ano_referencia'].'</b>'
                    , ''
                    , '<b>'.$soma_mes_vl1.'</b>'
                    , '<b>'.$soma_mes_vl2.'</b>'
                    , '<b>'.$soma_mes_vl3.'</b>'
                    , '<b>'.$soma_mes_vl4.'</b>'
                    , '<b>'.$soma_mes_vl5.'</b>'
                    , '<b>'.$soma_mes_vl6.'</b>'
                    , '<b>'.$soma_mes_vl7.'</b>'
                    , '<b>'.$soma_mes_vl8.'</b>'
                    , '<b>'.$soma_mes_vl9.'</b>'
                    , '<b>'.$soma_mes_tl.'</b>'
                    , ''
                );

                $contador_2++;
                $soma_mes_vl1 = 0;
                $soma_mes_vl2 = 0;
                $soma_mes_vl3 = 0;
                $soma_mes_vl4 = 0;
                $soma_mes_vl5 = 0;
                $soma_mes_vl6 = 0;
                $soma_mes_vl7 = 0;
                $soma_mes_vl8 = 0;
                $soma_mes_vl9 = 0;
                $soma_mes_tl  = 0;
            }

            $mes = $a_data[0];
            $soma_mes=array();
        }

		if( $item['fl_media']=='S' )
		{
			$link = '';

			$referencia = " Total de " . $item['ano_referencia'];
		}
		else
		{
			$link = anchor("indicador_atendimento/habilitacoes/detalhe/" . $item["cd_habilitacoes"], "editar");

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
        $beneficio = $item["beneficio"];

        $nr_percentual_f = $item['nr_percentual_f'];

		if( intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && $item['fl_media']!='S' )
		{
			$contador_ano_atual++;
			$media_ano[] = $nr_percentual_f;
		}
        
		$body[] = array(
			 $contador_2
			, $referencia
            , ($beneficio!='')?$beneficio:''
			, ($nr_valor_1!='')?$nr_valor_1:''
			, ($nr_valor_2!='')?$nr_valor_2:''
            , ($nr_valor_3!='')?$nr_valor_3:''
            , ($nr_valor_4!='')?$nr_valor_4:''
            , ($nr_valor_5!='')?$nr_valor_5:''
            , ($nr_valor_6!='')?$nr_valor_6:''
            , ($nr_valor_7!='')?$nr_valor_7:''
            , ($nr_valor_8!='')?$nr_valor_8:''
            , ($nr_valor_9!='')?$nr_valor_9:''
			, ($nr_percentual_f!='')?$nr_percentual_f.'':''
			, $link 
		);

        $soma_ano_vl1 +=$nr_valor_1;
        $soma_ano_vl2 +=$nr_valor_2;
        $soma_ano_vl3 +=$nr_valor_3;
        $soma_ano_vl4 +=$nr_valor_4;
        $soma_ano_vl5 +=$nr_valor_5;
        $soma_ano_vl6 +=$nr_valor_6;
        $soma_ano_vl7 +=$nr_valor_7;
        $soma_ano_vl8 +=$nr_valor_8;
        $soma_ano_vl9 +=$nr_valor_9;
        $soma_ano_tl  +=$nr_percentual_f;

        if($item['ds_beneficio'] != 'PR')
        {
            $soma_ben_vl1 +=$nr_valor_1;
            $soma_ben_vl2 +=$nr_valor_2;
            $soma_ben_vl3 +=$nr_valor_3;
            $soma_ben_vl4 +=$nr_valor_4;
            $soma_ben_vl5 +=$nr_valor_5;
            $soma_ben_vl6 +=$nr_valor_6;
            $soma_ben_vl7 +=$nr_valor_7;
            $soma_ben_vl8 +=$nr_valor_8;
            $soma_ben_vl9 +=$nr_valor_9;
            $soma_ben_tl  +=$nr_percentual_f;;
        }

        if($mes == $a_data[0])
        {
            $soma_mes[] = $nr_percentual_f;
            $soma_mes_vl1 +=$nr_valor_1;
            $soma_mes_vl2 +=$nr_valor_2;
            $soma_mes_vl3 +=$nr_valor_3;
            $soma_mes_vl4 +=$nr_valor_4;
            $soma_mes_vl5 +=$nr_valor_5;
            $soma_mes_vl6 +=$nr_valor_6;
            $soma_mes_vl7 +=$nr_valor_7;
            $soma_mes_vl8 +=$nr_valor_8;
            $soma_mes_vl9 +=$nr_valor_9;
            $soma_mes_tl  +=$nr_percentual_f;
        }
        $contador_2++;
	}

    if($mes != '')
    {
        $body[] = array(
            $contador_2
            , '<b>Total de '.$mes.'/'.$item['ano_referencia'].'</b>'
            , '<b>Benefício</b>'
            , '<b>'.$soma_ben_vl1.'</b>'
            , '<b>'.$soma_ben_vl2.'</b>'
            , '<b>'.$soma_ben_vl3.'</b>'
            , '<b>'.$soma_ben_vl4.'</b>'
            , '<b>'.$soma_ben_vl5.'</b>'
            , '<b>'.$soma_ben_vl6.'</b>'
            , '<b>'.$soma_ben_vl7.'</b>'
            , '<b>'.$soma_ben_vl8.'</b>'
            , '<b>'.$soma_ben_vl9.'</b>'
            , '<b>'.$soma_ben_tl.'</b>'
            , ''
        );
        $contador_2++;


        $body[] = array(
            $contador_2
            , '<b>Total de '.$mes.'/'.$item['ano_referencia'].'</b>'
            , ''
            , '<b>'.$soma_mes_vl1.'</b>'
            , '<b>'.$soma_mes_vl2.'</b>'
            , '<b>'.$soma_mes_vl3.'</b>'
            , '<b>'.$soma_mes_vl4.'</b>'
            , '<b>'.$soma_mes_vl5.'</b>'
            , '<b>'.$soma_mes_vl6.'</b>'
            , '<b>'.$soma_mes_vl7.'</b>'
            , '<b>'.$soma_mes_vl8.'</b>'
            , '<b>'.$soma_mes_vl9.'</b>'
            , '<b>'.$soma_mes_tl.'</b>'
            , ''
        );
    }
    

	if( sizeof($media_ano)>0 )
	{
        $body[] = array(
          0
          , '<b>Total de '.intval($tabela[0]['nr_ano_referencia']).'</b>'
          , ''
          , '<b>'.$soma_ano_vl1.'</b>'
          , '<b>'.$soma_ano_vl2.'</b>'
          , '<b>'.$soma_ano_vl3.'</b>'
          , '<b>'.$soma_ano_vl4.'</b>'
          , '<b>'.$soma_ano_vl5.'</b>'
          , '<b>'.$soma_ano_vl6.'</b>'
          , '<b>'.$soma_ano_vl7.'</b>'
          , '<b>'.$soma_ano_vl8.'</b>'
          , '<b>'.$soma_ano_vl9.'</b>'
          , '<b>'.$soma_ano_tl.'</b>'
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
/*
 if($mes != '' AND $item['beneficio'] != 'AP')
            {
                $body[] = array(
                    $contador_2
                    , '<b>Total de '.$mes.'/'.$item['ano_referencia'].'</b>'
                    , 'Benefício'
                    , '<b>'.$soma_mes_vl1.'</b>'
                    , '<b>'.$soma_mes_vl2.'</b>'
                    , '<b>'.$soma_mes_vl3.'</b>'
                    , '<b>'.$soma_mes_vl4.'</b>'
                    , '<b>'.$soma_mes_vl5.'</b>'
                    , '<b>'.$soma_mes_vl6.'</b>'
                    , '<b>'.$soma_mes_vl7.'</b>'
                    , '<b>'.$soma_mes_vl8.'</b>'
                    , '<b>'.$soma_mes_vl9.'</b>'
                    , '<b>'.$soma_mes_tl.'</b>'
                    , ''
                );

                $contador_2++;
            }
 */
?>
