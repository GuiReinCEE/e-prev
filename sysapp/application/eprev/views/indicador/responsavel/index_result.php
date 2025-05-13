<?php
	$head = array(
		'Gerência',
		'Responsável',
		'Grupo de Indicador'
	);

	$body = array();

	foreach($collection as $item)
	{
		$body[] = array( 
			$item['cd_gerencia'], 
			array(anchor('indicador/responsavel/cadastro/'.$item['cd_indicador_administrador'], $item['ds_usuario']), 'text-align:left;'),
			array(implode(br(), $item['grupo']), 'text-align:left;')
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();
?>