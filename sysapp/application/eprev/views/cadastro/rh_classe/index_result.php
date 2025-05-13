<?php
    $head = array(
        '#',
        'Cargo',
        'Classe',
        'Padrões',
        ''
    );

    $body = array();

    foreach($collection as $item)
    {
        $body[] = array(
            $item['cd_classe'],
            array($item['ds_cargo'], 'text-align:left;'),
            array($item['ds_classe'], 'text-align:left;'),
            implode(' - ', $item['padrao']),
            anchor('cadastro/rh_classe/cadastro/'.$item['cd_classe'], '[editar]')
        );
    }

    $this->load->helper('grid');
    $grid = new grid();
    $grid->head = $head;
    $grid->body = $body;

    echo $grid->render();