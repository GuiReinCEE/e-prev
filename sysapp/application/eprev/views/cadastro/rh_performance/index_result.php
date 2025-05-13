<?php
    $head = array( 
        '#',
        'Grupo',
        'Performance',
        'Descrição',
        'Pontos',
        ''
    );

    $body = array();

    foreach($collection as $item)
	{
        $body[] = array(
            $item['cd_performance'],
            array($item['ds_grupo'], 'text-align:left'),
            array($item['ds_performance'], 'text-align:left'),
            array(nl2br($item['ds_performance_descricao']), 'text-align:justify'),
            $item['nr_ponto'],
            anchor('cadastro/rh_performance/cadastro/'.$item['cd_performance'], '[editar]')
        );
    }

    $this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();