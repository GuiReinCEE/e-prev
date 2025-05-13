<?php
	$head = array( 
		'#', 
		$label_0, 
		$label_1, 
		$label_2, 
		$label_3, 
		$label_4, 
		$label_6, 
		$label_7, 
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

	foreach($collection as $item)
	{
		$ultimo_mes 	 = $item['mes_referencia'];
		$nr_percentual_f = $item['nr_percentual_f'];

		$nr_meta 	    = $item["nr_meta"];
		$nr_referencial = $item["nr_referencial"];

		if($item['fl_media']== 'S')
		{
			$link 			 = '';
			$referencia 	 = " Média de " . $item['ano_referencia'];
			$nr_valor_1 	 = '';
			$nr_valor_2 	 = '';
			$nr_percentual_f = $item['nr_percentual_f'];
            $observacao 	 = '';
		}
		else
		{
			$link 		= anchor("indicador_plugin/administrativo_absenteismo/detalhe/" . $item["cd_administrativo_absenteismo"], "editar");
			$referencia = $item['mes_referencia'];
			$nr_valor_1 = $item["nr_valor_1"];
			$nr_valor_2 = $item["nr_valor_2"];
            $observacao = $item["observacao"];
			
			if($nr_percentual_f == '')
			{
				if(floatval($nr_valor_2) > 0)
				{
					$nr_percentual_f = (floatval($nr_valor_2) / floatval($nr_valor_1)) * 100;
				}
				else
				{
					$nr_percentual_f = '0';
				}
			}
		}

		if( intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && $item['fl_media'] != 'S')
		{
			$contador_ano_atual++;
			$media_ano[] = $nr_percentual_f;
		}

		$body[] = array(
			$contador--,
			$referencia,
			($nr_valor_1 != '') ? number_format($nr_valor_1,2,',','.') : '',
			($nr_valor_2 != '') ? number_format($nr_valor_2,2,',','.') : '',
			($nr_percentual_f != '') ? number_format($nr_percentual_f,2,',','.').'%' : '',
			number_format($item["nr_meta"],2,',','.').'%',
			number_format($item['nr_referencial'],2,',','.').'%',
            array(nl2br($observacao), 'text-align : justify'),
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
			0,
			'<b>Média de '.intval($tabela[0]['nr_ano_referencia']).'</b>',
			'',
			'',
			'<big><b>'.app_decimal_para_php($media).'%</b></big>',
			'<big><b>'.app_decimal_para_php(number_format($nr_meta, 2)).'%</b></big>',
			'<big><b>'.app_decimal_para_php(number_format($nr_referencial, 2)).'%</b></big>',
            '',
            ''
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
?>
	<input type="hidden" id="ultimo_mes" name="ultimo_mes" value="<?= $ultimo_mes ?>"/>
	<input type="hidden" id="contador" name="contador" value="<?= $contador_ano_atual ?>"/>
	<br/>
    <div style="text-align:center;"> 
		<?= anchor_popup('indicador/apresentacao/detalhe/'.intval($tabela[0]['cd_indicador_tabela']), '[Visualizar Apresentação]', $ar_janela) ?>
	</div>
	<br/>
<?= $grid->render(); ?>
