<?php
	$head = array(
		'Registro',
		'Dt Registro', 
		'Origem', 
		'Status', 
		'Descriчуo'
	);

	$body = array();

	foreach($collection as $item)
	{
		$body[] = array(
			anchor(site_url('cadastro/expediente/cadastro/'.$item['cd_expediente']), $item['nr_expediente']),
			$item['dt_inclusao'],
			array($item['ds_expediente_origem'], 'text-align:left;'),
			array($item['ds_expediente_status'], 'text-align:left;'),
			array(anchor(site_url('cadastro/expediente/cadastro/'.$item['cd_expediente']), nl2br($item['ds_descricao'])), 'text-align:left;')
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();
?>