<?php
	$head = array( 
		'Descriчуo',
		'Dt. Inclusуo',
		'Responsсvel',
		'Substituto',
		'Qt dia(s)'
	);

	$body = array();

	foreach($collection as $item)
	{
		$body[] = array(
			array(anchor('servico/dominio_tipo/cadastro/'.$item['cd_dominio_tipo'], $item['ds_dominio_tipo']), 'text-align:left;'),
			$item['dt_inclusao'],
			array($item['ds_responsavel'], 'text-align:left;'),
			array($item['ds_substituto'], 'text-align:left;'),
			$item['nr_dias']
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();
?>