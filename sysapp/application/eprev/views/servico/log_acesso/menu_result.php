<?php
	$head = array(
		'Qt. Acesso',
		'Menu',
	);
	
	$body = array();

	foreach ($collection as $item)
	{
		$body[] = array(
			$item['nr_acesso'],
			array($item['ds_menu'], 'text-align:left')
		);	
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();
?>