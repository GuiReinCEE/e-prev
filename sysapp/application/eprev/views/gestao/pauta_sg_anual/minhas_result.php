<?php
	$head = array(
		'Ano',
        'Colegiado',
		'Dt. Enviado Resp.',
		'Dt. Limite',
		'Qt. Gerência',
		'Qt. Assuntos Pauta'
	);

	$body = array();

	foreach ($collection as $item)
	{
	  	$body[] = array(
			anchor('gestao/pauta_sg_anual/responder/'.$item['cd_pauta_sg_anual'], $item['nr_ano']),
            '<span class="'.$item['ds_class_colegiado'].'">'.$item['ds_colegiado'].'</span>',
		    $item['dt_envio_responsavel'],
		    $item['dt_limite'],
		    '<span class="badge badge-success">'.$item['qt_assunto_divisao'].'</span>',
		    '<span class="badge badge-success">'.$item['qt_assunto'].'</span>',
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();
?>