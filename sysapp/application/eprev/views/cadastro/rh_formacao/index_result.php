<?php
    $head = array(
        '#',
        'Formação',
        ''
    );

    $body = array();

    foreach($collection as $item)
    {
        $body[] = array(
            $item['cd_formacao'],
            array($item['ds_formacao'], 'text-align:left;'),
            anchor('cadastro/rh_formacao/cadastro/'.$item['cd_formacao'], '[editar]')
        );
    }

    $this->load->helper('grid');
    $grid = new grid();
    $grid->head = $head;
    $grid->body = $body;

    echo $grid->render();