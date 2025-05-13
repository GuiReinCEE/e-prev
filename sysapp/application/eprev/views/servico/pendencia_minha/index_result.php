<?php
	$head = array( 
		'Cуdigo',
		'Descriзгo'
	);

	$body = array();
						
	foreach($collection as $item)
	{	
		$body[] = array(
			array(anchor('servico/pendencia_minha/cadastro/'.$item['cd_pendencia_minha'], $item["cd_pendencia_minha"]), 'text-align:left'),
			array(anchor('servico/pendencia_minha/cadastro/'.$item['cd_pendencia_minha'], $item['ds_pendencia_minha']), 'text-align:left')
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();
?>