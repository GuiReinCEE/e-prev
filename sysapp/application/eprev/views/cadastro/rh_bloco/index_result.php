<?php
    $head = array(
        '#',
        'Grupo',
        'Nome',
        'Descrição',
        'Info de Conhecimentos',
        ''
    );

    $body = array();

    foreach($collection as $item)
	{
        $body[] = array(
            $item['cd_bloco'],
            array($item['ds_grupo'], 'text-align:left'),
            array($item['ds_bloco'], 'text-align:left'),
            array($item['ds_bloco_descricao'], 'text-align:justify'),
            $item['ds_conhecimento'],
            anchor('cadastro/rh_bloco/cadastro/'.$item['cd_bloco'], '[editar]')
        );
    }

    $this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();