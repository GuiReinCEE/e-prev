<?php
	$head = array( 
		'#', $label_0, $label_1, $label_2,  $label_3, $label_4,  $label_5, ''
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

	$body = array();

	$nr_etapas_previstas     = 0;
	$nr_etapas_cumpridas     = 0;
	$nr_percentual_cumpridas = 0;
	$nr_meta                 = 0;

	foreach($collection as $item)
	{
		if(trim($item['fl_media']) == 'S')
		{
			$link = '';

			$referencia = 'Resultado de '.$item['ano_referencia'];
		}
		else
		{
			$link = anchor('indicador_plugin/controladoria_cumprimento_projeto_modernidade/cadastro/'.$item['cd_controladoria_cumprimento_projeto_modernidade'], 'editar');

			$referencia = $item['mes_ano_referencia'];
		}
		
		if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) AND trim($item['fl_media']) != 'S')
		{
			$contador_ano_atual++;

			$ultimo_mes = $item['mes_referencia'];

			$nr_etapas_previstas     = $item['nr_etapas_previstas'];
			$nr_etapas_cumpridas     = $item['nr_etapas_cumpridas'];
			$nr_percentual_cumpridas = $item['nr_percentual_cumpridas'];
            $nr_meta                 = $item['nr_meta'];
            
		}
		
		$body[] = array(
			$contador--,
			$referencia,
			$item['nr_etapas_previstas'],
			$item['nr_etapas_cumpridas'],
			number_format($item['nr_percentual_cumpridas'], 2, ',', '.').' %',
			number_format($item['nr_meta'], 2, ',', '.').' %',
			array(nl2br($item['ds_observacao']), 'text-align:justify'), 
			$link 
		);
	}
	
	if($contador_ano_atual > 0)
	{
		$body[] = array(
			0, 
			'<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>', 
			'<b>'.$nr_etapas_previstas.'</b>',
			'<b>'.$nr_etapas_cumpridas.'</b>', 
			'<b>'.number_format($nr_percentual_cumpridas, 2, ',', '.').' %</b>',
			'<b>'.number_format($nr_meta, 2, ',', '.').' %</b>',
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