<?php
	$head = array( 
		'#', $label_0, $label_1, $label_2,  $label_3, $label_4, $label_6, $label_5, ''
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

	$nr_normas_publicadas               = 0;
	$nr_normas_publicadas_fora_prazo    = 0;
	$nr_normas_respondidas_fora_prazo   = 0;
	$nr_normas_implementadas_fora_prazo = 0;
	$nr_meta                            = 0;

	foreach($collection as $item)
	{
		if(trim($item['fl_media']) == 'S')
		{
			$link = '';

			$referencia = 'Resultado de '.$item['ano_referencia'];
		}
		else
		{
			$link = anchor('indicador_plugin/controladoria_acom_legislacao/cadastro/'.$item['cd_controladoria_acom_legislacao'], 'editar');

			$referencia = $item['mes_ano_referencia'];
		}
		
		if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) AND trim($item['fl_media']) != 'S')
		{
			$contador_ano_atual++;

			$ultimo_mes = $item['mes_referencia'];

			$nr_normas_publicadas               += $item['nr_normas_publicadas'];
			$nr_normas_publicadas_fora_prazo    += $item['nr_normas_publicadas_fora_prazo'];
			$nr_normas_respondidas_fora_prazo   += $item['nr_normas_respondidas_fora_prazo'];
			$nr_normas_implementadas_fora_prazo += $item['nr_normas_implementadas_fora_prazo'];
			$nr_meta                            += $item['nr_meta'];
		}
		
		$body[] = array(
			$contador--,
			$referencia,
			number_format($item['nr_normas_publicadas'], 0, ',', '.'),
			number_format($item['nr_normas_publicadas_fora_prazo'], 0, ',', '.'),
			number_format($item['nr_normas_respondidas_fora_prazo'], 0, ',', '.'),
			number_format($item['nr_normas_implementadas_fora_prazo'], 0, ',', '.'),
			number_format($item['nr_meta'], 0, ',', '.'),
			array(nl2br($item['ds_observacao']), 'text-align:justify'), 
			$link 
		);
	}
	
	if($contador_ano_atual > 0)
	{
		$body[] = array(
			0, 
			'<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>', 
			'<b>'.number_format($nr_normas_publicadas, 0, ',', '.').'</b>',
			'<b>'.number_format($nr_normas_publicadas_fora_prazo, 0, ',', '.').'</b>', 
			'<b>'.number_format($nr_normas_respondidas_fora_prazo, 0, ',', '.').' </b>',
			'<b>'.number_format($nr_normas_implementadas_fora_prazo, 0, ',', '.').'</b>',
			'<b>'.number_format($nr_meta, 0, ',', '.').'</b>',
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