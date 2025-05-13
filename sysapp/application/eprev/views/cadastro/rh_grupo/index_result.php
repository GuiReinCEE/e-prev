<?php
    $head = array(
        '#',
        'Nome Grupo',
        ''
    );

    $body = array();

    foreach($collection as $item)
	{
        $body[] = array(
            $item['cd_grupo'],
            array($item['ds_grupo'], 'text-align:left'),
            anchor('cadastro/rh_grupo/cadastro/'.$item['cd_grupo'], '[editar]')
        );
    }

    $this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();