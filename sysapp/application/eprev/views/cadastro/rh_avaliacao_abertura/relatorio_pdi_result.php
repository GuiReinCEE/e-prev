<?php
	$head = array( 
		'Gerência',
		'Colaborador',
		'Competência',
		'Plano para Melhoria',
		'Resultado Esperado',
		'Responsável (Quem)',
		'Quando (Prazo)'
    );

    $body = array();

	foreach ($collection as $key => $item) 
	{
		$body[] = array(
			$item['cd_gerencia'],
			$item['ds_colaborador'],
			array(nl2br($item['ds_avaliacao_usuario_plando_desenvolvimento']), 'text-align:justify'),
			array(nl2br($item['ds_plano_melhoria']), 'text-align:justify'),
			array(nl2br($item['ds_resultado']), 'text-align:justify'),
			$item['ds_responsavel'],
			array($item['ds_quando'], 'text-align:left')
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->view_count = TRUE;
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();



	