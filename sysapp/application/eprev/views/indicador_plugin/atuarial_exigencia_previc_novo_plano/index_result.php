<?php
	$head = array( 
		'#', $label_0, $label_1, $label_2,  $label_3, $label_4, ''
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

	foreach($collection as $item)
	{
		if(trim($item['fl_media']) == 'S')
		{
			$link = '';

			$referencia = 'Resultado de '.$item['ano_referencia'];
		}
		else
		{
			$link = anchor('indicador_plugin/atuarial_exigencia_previc_novo_plano/cadastro/'.$item['cd_atuarial_exigencia_previc_novo_plano'], 'editar');

			$referencia = $item['mes_ano_referencia'];
		}
		
		if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) AND trim($item['fl_media']) != 'S')
		{
			$contador_ano_atual++;

			$ultimo_mes = $item['mes_referencia'];
		}
		
		$body[] = array(
			$contador--,
			array($item['ds_evento'], 'text-align:left'),
			$item['ds_houve_exigencia'],
			$item['ds_meta'],
			$item['ds_meta_resultado'],
			array(nl2br($item['ds_observacao']), 'text-align:justify'), 
			$link 
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