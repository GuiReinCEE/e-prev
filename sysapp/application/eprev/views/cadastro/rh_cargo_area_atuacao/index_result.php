<?php
    $head = array(
        'Gerência',
        'Grupo Ocupacional',
        'Cargo',
        'Área de Atuação',
        'Conhecimentos Específicos',
        ''
    );

    $body = array();

    foreach($collection as $item)
    {
        $body[] = array(
            $item['cd_gerencia'],
            array($item['ds_grupo_ocupacional'], 'text-align:left;'),
            array($item['ds_cargo'], 'text-align:left;'),
            array($item['ds_area_atuacao'], 'text-align:left;'),
            array(nl2br($item['ds_conhecimento_especifico']), 'text-align:left;'),
            anchor('cadastro/rh_cargo_area_atuacao/cadastro/'.$item['cd_cargo_area_atuacao'], '[editar]')
        );
    }

    $this->load->helper('grid');
    $grid = new grid();
    $grid->head = $head;
    $grid->body = $body;

    echo $grid->render();