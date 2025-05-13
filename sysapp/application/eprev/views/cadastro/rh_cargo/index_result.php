<?php
    $head = array(
        '#',
        'Grupo Ocupacional',
        'Cargo',
        'Formação',
        'Conhecimentos Genéricos',
        ''
    );

    $body = array();

    foreach($collection as $item)
    {
        $body[] = array(
            $item['cd_cargo'],
            array($item['ds_grupo_ocupacional'], 'text-align:left;'),
            array($item['ds_cargo'], 'text-align:left;'),
            array($item['ds_formacao'], 'text-align:left;'),
            array(nl2br($item['ds_conhecimento_generico']), 'text-align:left;'),
            anchor('cadastro/rh_cargo/cadastro/'.$item['cd_cargo'], '[editar]')
        );
    }

    $this->load->helper('grid');
    $grid = new grid();
    $grid->head = $head;
    $grid->body = $body;

    echo $grid->render();