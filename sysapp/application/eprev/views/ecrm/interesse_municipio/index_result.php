<?php
	$head = array( 
		'Cód.',
		'Nome',
		'E-mail',
		'Telefone',
		'Dt. Nascimento',
		'Município',
		'Mnesagem',
		'Data'
	);

	$body = array();

	foreach($collection as $item)
	{
		$body[] = array(
			$item['cd_interesse_municipio'],
			array($item['ds_nome'], 'text-align:left'),
			$item['ds_email'],
			$item['ds_telefone'],
			$item['dt_nascimento'],
			array($item['ds_municipio'], 'text-align:left'),
			array($item['ds_mensagem'], 'text-align:justify'),
			$item['dt_inclusao']
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();
?>