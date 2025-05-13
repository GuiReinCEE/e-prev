<?php
    $head = array(
        'Ano',
		'Avaliado',
		'Avaliador',
		'Status'
    );

    $body = array();

    foreach($collection as $item)
	{
        $body[] = array(
            $item['nr_ano_avaliacao'],
            array(anchor('cadastro/rh_avaliacao/formulario/'.$item['cd_avaliacao_usuario'], $item['ds_usuario_avaliado']), 'text-align:left'),
            array($item['ds_usuario_avaliador'], 'text-align:left'),
            '<label class="'.$item['ds_class_status'].'">'.$item['ds_status'].'</label>'
        );
    }

    $this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();