<?php
	$head = array( 
		'#', $label_0, $label_1, $label_2, $label_3, $label_4, $label_5, ''
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

	$contador           = sizeof($collection);
	$ultimo_mes			= 0;
	$contador_ano_atual = 0;
	$nr_ingressos       = 0;
	$nr_vendas          = 0;
	$nr_meta 			= 0;

	$body = array();
	
	foreach($collection as $item)
	{
		if(trim($item['fl_media']) == 'S')
		{
			$link = '';

			$referencia = ' Resultado de '.intval($item['ano_referencia']);
		}
		else
		{
			$referencia = $item['mes_referencia'];

			$link = anchor('indicador_plugin/exp_vendas_ingressos/cadastro/'.$item['cd_exp_vendas_ingressos'], 'editar');
		}
		
		if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && trim($item['fl_media']) != 'S')
		{
			$contador_ano_atual++;
	        $ultimo_mes = $item['mes_referencia'];

			$nr_ingressos += $item['nr_ingressos'];
			$nr_vendas    += $item['nr_vendas'];
			$nr_meta      = $item['nr_meta'];
		}
		
		$body[] = array(
			$contador--,
			$referencia,
			number_format($item['nr_vendas'], 0, ',', '.'),
			number_format($item['nr_ingressos'], 0, ',', '.'),
			number_format($item['nr_resultado'], 2, ',', '.').' %',
			number_format($item['nr_meta'], 2, ',', '.').' %',
			array(nl2br($item['ds_observacao']), 'text-align:left'), 
			$link 
		);
	}

	if(sizeof($contador_ano_atual) >0)
	{	
		$nr_resultado = 0;

		if(intval($nr_vendas) > 0)
		{
			$nr_resultado = ($nr_ingressos / $nr_vendas) * 100;
		}

		$body[] = array(
			0, 
			'<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>', 
			'<b>'.number_format($nr_vendas, 0, ',', '.').'</b>',
			'<b>'.number_format($nr_ingressos, 0, ',', '.').'</b>',
			'<b>'.number_format($nr_resultado, 2, ',', '.').' % </b>', 
			'<b>'.number_format($nr_meta, 2, ',', '.').' % </b>', 
			'', 
			''
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
?>
	<input type='hidden' id='ultimo_mes' name='ultimo_mes' value='<?= $ultimo_mes ?>'/>
	<input type='hidden' id='contador' name='contador' value='<?= $contador_ano_atual ?>'/>
	<br/>
	<div style='text-align:center;'> 
		<?= anchor_popup('indicador/apresentacao/detalhe/'.intval($tabela[0]['cd_indicador_tabela']), '[Visualizar Apresentação]', $ar_janela) ?>
	</div>
	<br/>
	<?= $grid->render() ?>