<?php
	$head = array( 
		'Plano',
		'Empresa',
		'Nr. Extrato',
		'Dt. Base',
		'Dt. Liberação Eletro',
		'Qt. Extratos Eletro',
		'Qt. Extratos Internet',
		'Status'
	);

	$body = array();

	foreach($collection as $item)
	{
		$link = 'planos/envio_extrato/agendar_envio/'.$item['cd_plano'].'/'.$item['cd_empresa'].'/'.$item['nro_extrato'];

		$body[] = array(
			array(anchor($link, $item['cd_plano'].' - '.$item['ds_plano']), 'text-align:left;'),
			array(anchor($link, $item['cd_empresa'].' - '.$item['ds_empresa']), 'text-align:left;'),
			anchor($link, $item['nro_extrato']),
			$item['dt_base'],
			$item['dt_liberacao'],
			'<span class="badge badge-success">'.$item['qt_extrato'].'</span>',
			'<span class="badge badge-'.(trim($item['fl_libera_envio']) == 'S' ? 'info' : 'warning' ).'">'.$item['qt_extrato_participante'].'</span>',
			'<span class="label label-'.(trim($item['fl_libera_envio']) == 'S' ? 'info">Liberado para Envio' : 'warning">Inconsistência' ).'</span>'
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();
?>