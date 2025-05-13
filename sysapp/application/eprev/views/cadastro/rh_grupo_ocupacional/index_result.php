<?php
    $head = array(
        '#',
        'Grupo Ocupacional',
        ''
    );

    $body = array();

    foreach($collection as $item)
    {
        $body[] = array(
            $item['cd_grupo_ocupacional'],
            array($item['ds_grupo_ocupacional'], 'text-align:left;'),
            anchor('cadastro/rh_grupo_ocupacional/cadastro/'.$item['cd_grupo_ocupacional'], '[editar]')
        );
    }

    $this->load->helper('grid');
    $grid = new grid();
    $grid->head = $head;
    $grid->body = $body;

    echo $grid->render();