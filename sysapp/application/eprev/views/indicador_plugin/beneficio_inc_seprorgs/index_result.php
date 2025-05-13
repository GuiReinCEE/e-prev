<?php
	$head = array( 
		'#', 
		$label_0, 
		$label_1, 
		$label_2, 
		$label_3, 
		$label_4, 
		$label_6, 
		''
	);

    $ar_janela = array(
		'width'      => '700',
		'height'     => '500',
		'scrollbars' => 'yes',
		'status'     => 'yes',
		'resizable'  => 'yes',
		'screenx'    => '0',
		'screeny'    => '0'
    );

    $body = array();

	$contador_ano_atual = 0;
	$contador 			= sizeof($collection);
	$media_ano 			= array();
	$a_data 			= array(0, 0);

	foreach( $collection as $item )
	{
		$a_data = explode( "/", $item['mes_referencia'] );

		$nr_percentual_f = $item['nr_percentual_f'];

		if(trim($item['fl_media']) == 'S')
		{
			$link 		     = '';
			$nr_valor_1      = '';
			$nr_valor_2      = '';
			$nr_percentual_f = $item['nr_percentual_f'];
			$referencia      = " Média de " . $item['ano_referencia'];
		}
		else
		{
			$link = anchor("indicador_plugin/beneficio_inc_seprorgs/cadastro/".$item["cd_beneficio_inc_seprorgs"], "[editar]");

			$referencia = $item['mes_referencia'];
			$nr_valor_1 = $item["nr_valor_1"];
			$nr_valor_2 = $item["nr_valor_2"];
			
			if($nr_percentual_f == '')
			{
				if(floatval($nr_valor_2) > 0)
				{
					$nr_percentual_f = (floatval($nr_valor_2) / (floatval($nr_valor_1) > 0 ? floatval($nr_valor_1) : 1)) * 100;
				}
				else
				{
					$nr_percentual_f = '0';
				}
			}
		}

		if( intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && trim($item['fl_media']) != 'S')
		{
			$contador_ano_atual++;
			$media_ano[] = $nr_percentual_f;
		}

		$body[] = array(
			$contador--,
			$referencia,
			$nr_valor_1,
			$nr_valor_2,
			number_format($nr_percentual_f,2,',','.').' %',
			number_format($item["nr_meta"],2,',','.').' %',
            array($item["observacao"], 'text-align:"left"'),
			$link 
		);
	}

	if(sizeof($media_ano) > 0)
	{
		$media = 0;
		foreach($media_ano as $valor)
		{
			$media += $valor;
		}

		$media = number_format(($media / sizeof($media_ano)), 2);

		$body[] = array(
			0
			, '<b>Média de '.intval($tabela[0]['nr_ano_referencia']).'</b>'
			, ''
			, ''
			, '<big><b>'.app_decimal_para_php($media).' %</b></big>'
			, ''
			, '', ''
		);
	}

	echo "<input type='hidden' id='mes_input' name='mes_input' value='".$a_data[0]."' />";
	echo "<input type='hidden' id='contador_input' name='contador_input' value='".$contador_ano_atual."' />";

	echo anchor_popup("indicador/apresentacao/detalhe/".intval($tabela[0]['cd_indicador_tabela']), 'Visualizar apresentação', $ar_janela);

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();
?>
