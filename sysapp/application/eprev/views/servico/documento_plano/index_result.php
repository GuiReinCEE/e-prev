<?php
	$head = array( 
		'Código',
		'Site',
		'Documento(s)'
	);

	$body = array();

	foreach($collection as $item)
	{
		$body[] = array(
			$item['cd_documento_plano'],
			array(anchor('servico/documento_plano/cadastro/'.$item['cd_documento_plano'], $item['ds_documento_plano']), 'text-align:left;'),
			array(implode(br(), $item['documento']), 'text-align:justify')
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();
?>