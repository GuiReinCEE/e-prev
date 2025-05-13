<?php
	$head = array( 
		'#', $label_0, $label_1, $label_2,  $label_3, $label_4,  $label_5, $label_6, $label_8, ''
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

	$contador_ano_atual = 0;
	$ultimo_mes         = 0;
	$contador           = sizeof($collection);
	$a_data             = array(0, 0);

	$body = array();

	$nr_desligamentos = 0;
	$nr_admissoes     = 0;
	$nr_funcionarios  = 0;
	$nr_limite_max    = 0;
	$nr_referencial   = 0;
	$nr_resultado     = 0;
	$nr_meta          = 0;

	foreach($collection as $item)
	{
		if(trim($item['fl_media']) == 'S')
		{
			$link = '';

			$referencia = 'Resultado de '.$item['ano_referencia'];
		}
		else
		{
			$link = anchor('indicador_plugin/rh_rotatividade/cadastro/'.$item['cd_rh_rotatividade'], 'editar');

			$referencia = $item['mes_ano_referencia'];
		}
		
		if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) AND trim($item['fl_media']) != 'S')
		{
			$contador_ano_atual++;

			$ultimo_mes = $item['mes_referencia'];

			$nr_desligamentos += $item['nr_desligamentos'];
			$nr_admissoes     += $item['nr_admissoes'];
			$nr_funcionarios  += $item['nr_funcionarios'];
			$nr_limite_max    += $item['nr_limite_max'];
			$nr_referencial   += $item['nr_referencial'];
			$nr_resultado     += $item['nr_resultado'];
			$nr_meta          += $item['nr_meta'];
		}
		
		$body[] = array(
			$contador--,
			$referencia,
			number_format($item['nr_desligamentos'], 2, ',', '.'),
			number_format($item['nr_admissoes'], 2, ',', '.'),
			number_format($item['nr_funcionarios'], 2, ',', '.'),
			number_format($item['nr_limite_max'], 2, ',', '.').' %',
			number_format($item['nr_referencial'], 2, ',', '.').' %',
			number_format($item['nr_resultado'], 2, ',', '.').' %',
			//number_format($item['nr_meta'], 2, ',', '.').' %',
			array(nl2br($item['ds_observacao']), 'text-align:justify'), 
			$link 
		);
	}
	
	if($contador_ano_atual > 0)
	{
		$body[] = array(
			0, 
			'<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>', 
			'<b>'.number_format($nr_desligamentos  / $contador_ano_atual, 2, ',', '.').'</b>',
			'<b>'.number_format($nr_admissoes  / $contador_ano_atual, 2, ',', '.').'</b>',
			'<b>'.number_format($nr_funcionarios  / $contador_ano_atual, 2, ',', '.').'</b>',
			'<b>'.number_format($nr_limite_max  / $contador_ano_atual, 2, ',', '.').' %</b>',
			'<b>'.number_format($nr_referencial  / $contador_ano_atual, 2, ',', '.').' %</b>',
			'<b>'.number_format($nr_resultado  / $contador_ano_atual, 2, ',', '.').' %</b>',
			//'<b>'.number_format($nr_meta  / $contador_ano_atual, 2, ',', '.').' %</b>',
			'', 
			''
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	$grid->view_count = false;
?>
	<input type="hidden" id="ultimo_mes" name="ultimo_mes" value="<?= $ultimo_mes ?>"/>
	<input type="hidden" id="contador" name="contador" value="<?= $contador_ano_atual ?>"/>
	<br/>
	<div style="text-align:center;"> 
		<?= anchor_popup('indicador/apresentacao/detalhe/'.intval($tabela[0]['cd_indicador_tabela']), '[Visualizar Apresentação]', $ar_janela) ?>
	</div>
	<br/>
<?= $grid->render(); ?>