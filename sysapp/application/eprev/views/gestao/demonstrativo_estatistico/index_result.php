<?php
	$head = array(
		'Ano/Mês',
		'PDF',
		'Planilha',
		'PDF CEEEPrev',
		'Planilha CEEEPrev',
		'Dt. Envio',
	);

	$body = array();

	foreach ($collection as $item)
	{
	  	$body[] = array(
			(trim($item['fl_editar']) == 'S' ? anchor('gestao/demonstrativo_estatistico/cadastro/'.$item['cd_demonstrativo_estatistico'], $item['dt_referencia']) : $item['dt_referencia']),
			anchor(base_url().'up/demonstrativo_estatistico/'.$item['arquivo'], $item['arquivo_nome'], array('target' => '_blank')),
			anchor(base_url().'up/demonstrativo_estatistico/'.$item['arquivo_planilha'], $item['arquivo_planilha_nome'] , array('target' => '_blank')),
			anchor(base_url().'up/demonstrativo_estatistico/'.$item['arquivo_ceeeprev'], $item['arquivo_ceeeprev_nome'], array('target' => '_blank')),
			anchor(base_url().'up/demonstrativo_estatistico/'.$item['arquivo_ceeeprev_planilha'], $item['arquivo_ceeeprev_planilha_nome'] , array('target' => '_blank')),
			$item['dt_envio']
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();
?>