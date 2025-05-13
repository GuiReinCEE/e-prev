<?php
	$head = array( 
		'Plano',
		'Empresa',
		'Nr. Extrato',
		'Dt. Base',
		'Dt. Gerado',
		'Usuário',
		'Qt. Extratos Eletro',
		'Dt. Envio',
		'Qt. Enviados',
		'Qt. Aguardando Envio',
		'Qt. Não enviados'
	);

	$body = array();

	foreach($collection as $item)
	{
		$body[] = array(
			array(anchor('planos/envio_extrato/extrato_enviado/'.$item['cd_plano'].'/'.$item['cd_empresa'].'/'.$item['nr_extrato'], $item['cd_plano'].' - '.$item['ds_plano']), 'text-align:left;'),
			array(anchor('planos/envio_extrato/extrato_enviado/'.$item['cd_plano'].'/'.$item['cd_empresa'].'/'.$item['nr_extrato'], $item['cd_empresa'].' - '.$item['ds_empresa']), 'text-align:left;'),
			anchor('planos/envio_extrato/extrato_enviado/'.$item['cd_plano'].'/'.$item['cd_empresa'].'/'.$item['nr_extrato'], $item['nr_extrato']),
			'<span class="label">'.$item['dt_base'].'</span>',
			'<span class="label">'.$item['dt_inclusao'].'</span>',
			$item['ds_usuario'],
			'<span class="badge badge-success">'.$item['qt_extrato_eletro'].'</span>',
			'<span class="label label-inverse">'.$item['dt_agendado'].'</span>',
			'<span class="badge badge-info">'.$item['qt_enviado'].'</span>',
			'<span class="badge badge-success">'.$item['qt_aguardando'].'</span>',
			'<span class="badge badge-important">'.$item['qt_enviado_nao'].'</span>'
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();
?>