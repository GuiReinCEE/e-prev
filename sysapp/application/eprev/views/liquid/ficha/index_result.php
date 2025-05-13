<?php
	$head = array( 
		'Cуd', 
		'Ficha',
		'Gerкncias',
		'Descriзгo'
	);

	$body = array();

	foreach($collection as $item)
	{
		$body[] = array(
			$item['nr_ficha'],
			array(anchor('liquid/ficha/cadastro/'.$item['cd_ficha'], $item['ds_ficha']), 'text-align:left;'),
			array(implode(', ', $item['gerencia']), 'text-align:justify'),
			array(nl2br($item['ds_caminho']), 'text-align:left;')
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();
?>