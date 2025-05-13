<?php
    $head = array(
        '#',
        'Área de Atuação',
        ''
    );

    $body = array();

    foreach($collection as $item)
    {
        $body[] = array(
            $item['cd_area_atuacao'],
            array($item['ds_area_atuacao'], 'text-align:left;'),
            anchor('cadastro/rh_area_atuacao/cadastro/'.$item['cd_area_atuacao'], '[editar]')
        );
    }

    $this->load->helper('grid');
    $grid = new grid();
    $grid->head = $head;
    $grid->body = $body;

    echo $grid->render();