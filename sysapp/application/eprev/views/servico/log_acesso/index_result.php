<?php
	$head = array(
		'Dt. Acesso',
		'Usuário',
		'Menu',
		'URI'
	);
	
	$body = array();

	foreach ($collection as $item)
	{
		$body[] = array(
			$item['dt_log_acesso_menu'],
			array($item['nome_usuario'], 'text-align:left'),
			array($item['ds_menu'], 'text-align:left'),
			array($item['ds_uri'], 'text-align:left')
		);	
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();
?>