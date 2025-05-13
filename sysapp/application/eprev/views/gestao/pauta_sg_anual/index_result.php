<?php
	$head = array(
		'Ano',
		'Colegiado',
		'Dt. Envio Resp.',
		'Dt. Limite Resp.',
		'Dt. Conf. do Colegiado',
		'Qt. Assuntos',
		''
	);

	$body = array();

	foreach ($collection as $item)
	{
	  	$body[] = array(
			anchor('gestao/pauta_sg_anual/assunto/'.$item['cd_pauta_sg_anual'], $item['nr_ano']),
		    '<span class="'.$item['ds_class_colegiado'].'">'.$item['ds_colegiado'].'</span>',
		    $item['dt_envio_responsavel'],
		    $item['dt_limite'],
		    $item['dt_confirmacao'],
		    '<span class="badge badge-success">'.$item['qt_assunto'].'</span>',
		    anchor('gestao/pauta_sg_anual/assunto/'.$item['cd_pauta_sg_anual'], '[assuntos]')
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();
?>