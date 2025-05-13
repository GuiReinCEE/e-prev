<?php
	$head = array(
		'Código',
		'Documento',
		'Acesso'
	);

	$body = array();

	foreach ($collection as $item)
	{	
		$body[] = array(
			anchor('gestao/controle_documento_controladoria/tipo_cadastro/'.$item['cd_controle_documento_controladoria_tipo'], $item['cd_controle_documento_controladoria_tipo']),
			array(anchor('gestao/controle_documento_controladoria/tipo_cadastro/'.$item['cd_controle_documento_controladoria_tipo'], $item['ds_controle_documento_controladoria_tipo']), 'text-align:left;'),
			array(implode(br(), $item['usuario']), 'text-align:left'),
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();
?>