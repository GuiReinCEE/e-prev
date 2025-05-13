<?php
$body=array();
$head = array(
	'#', $label_0, $label_1, $label_2, $label_3, $label_4, $label_6, ''
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
	$nr_total_beneficio 	 = 0;
	$nr_total_beneficio_erro = 0;
	$nr_total_meta           = 0;

	foreach( $collection as $item )
	{
		$a_data = explode( "/", $item['mes_referencia'] );

		$nr_meta = $item["nr_meta"];

		$nr_percentual_f = $item['nr_percentual_f'];
        $observacao = $item["observacao"];

		if( $item['fl_media']=='S' )
		{
			$link = '';

			$referencia = " Média de " . $item['ano_referencia'];

			$nr_beneficio = '';
			$nr_beneficio_erro = '';
			$nr_percentual_f = $item['nr_percentual_f'];
		}
		else
		{
			$link = anchor("indicador_plugin/beneficio_incorrecao/detalhe/" . $item["cd_beneficio_incorrecao"], "editar");

			$referencia = $item['mes_referencia'];

			$nr_beneficio = $item["nr_beneficio"];
			$nr_beneficio_erro = $item["nr_beneficio_erro"];
			
			if($nr_percentual_f=='')
			{
				if( floatval($nr_beneficio_erro)>0 )
				{
					$nr_percentual_f = ( floatval($nr_beneficio_erro)/floatval($nr_beneficio) ) * 100;
				}
				else
				{
					$nr_percentual_f = '0';
				}
			}
		}

		if( intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && $item['fl_media']!='S' )
		{
			$contador_ano_atual++;
			$media_ano[] = $nr_percentual_f;
			$nr_total_beneficio 	 += $nr_beneficio;
			$nr_total_beneficio_erro += $nr_beneficio_erro;
			$nr_total_meta 			 += $nr_meta;
		}

		$body[] = array(
			 $contador--
			, $referencia
			, ($nr_beneficio!='')?$nr_beneficio:''
			, ($nr_beneficio_erro!='')?$nr_beneficio_erro:''
			, ($nr_percentual_f!='')?number_format($nr_percentual_f,2,',','.').' %':''
			, number_format($nr_meta,2,',','.')
            , array($observacao, 'text-align:"left"')
			, $link 
		);
	}

	if( sizeof($media_ano)>0 )
	{
		$media = 0;
		foreach( $media_ano as $valor )
		{
			$media += $valor;
		}

		$media 					 = number_format( ($media / sizeof($media_ano)), 2 );
		$nr_total_beneficio 	 = ($nr_total_beneficio / sizeof($media_ano));
		$nr_total_beneficio_erro = ($nr_total_beneficio_erro / sizeof($media_ano));
		$nr_total_meta 			 = number_format( ($nr_total_meta / sizeof($media_ano)), 2 );

		$body[] = array(
			0
			, '<b>Média de '.intval($tabela[0]['nr_ano_referencia']).'</b>'
			, '<big><b>'.intval($nr_total_beneficio).'</b></big>'
			, '<big><b>'.intval($nr_total_beneficio_erro).'</b></big>'
			, '<big><b>'.app_decimal_para_php( $media ).' %</b></big>'
			, '<big><b>'.app_decimal_para_php( $nr_total_meta ).' %</b></big>'
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
