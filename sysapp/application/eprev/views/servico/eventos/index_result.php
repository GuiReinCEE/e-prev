<?php
	$head = array(
		'Cód. Evento',
		'Nome',
		'Assunto',
		'Para',
		'CC',
		'CCO'
	);

	$body = array();

	foreach ($collection as $item)
	{	
		$body[] = array(
			anchor('servico/eventos/cadastro/'.$item['cd_evento'], $item['cd_evento']),
			array(anchor('servico/eventos/cadastro/'.$item['cd_evento'], $item['nome']), 'text-align: left;'),
			array($item['assunto'], 'text-align: left;'),
			array($item['para'], 'text-align: left;'),
			array($item['cc'], 'text-align: left;'),
			array($item['cco'], 'text-align: left;')
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();
?>