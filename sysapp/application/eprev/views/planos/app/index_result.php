<?php
	$head = array(
		'Empresa',
		'Plano',
	    'Qt. Participantes',
	);

	$body = array();

	foreach ($collection as $item)
	{
	  	$body[] = array(
	  		array($item['ds_empresa'], 'text-align:left'),
	  		array($item['ds_plano'], 'text-align:left'),
			array($item['qt_participante'], '', 'int')
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();
?>	