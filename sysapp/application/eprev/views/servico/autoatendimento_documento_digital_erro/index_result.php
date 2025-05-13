<?php
	$head = array(
		'Dt. Inclusуo',
		'RE',
		'Nome',
		'Descriчуo'
	);
	
	$body = array();

	foreach ($collection as $item)
	{
		$body[] = array(
			$item['dt_inclusao'],
			$item['cd_empresa'].'/'.$item['cd_registro_empregado'].'/'.$item['seq_dependencia'],
			array($item['nome'], 'text-align:left'),
			array($item['ds_documento_digital_erro'], 'text-align:left')
		);	
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();
?>