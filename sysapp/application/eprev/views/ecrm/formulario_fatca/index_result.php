<?php
	$head = array(
		'RE',
		'Nome',
		'US Person',
		'Dt. Inclusão'
	);

	$body = array();

	foreach ($collection as $item)
	{	
		$body[] = array(
			$item['cd_empresa'].'/'.$item['cd_registro_empregado'].'/'.$item['seq_dependencia'],
			array($item['nome'],'text-align: left;'),
			$item['id_us_person'],
			$item['dt_inclusao']
		); 
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();
?>