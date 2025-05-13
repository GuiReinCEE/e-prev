<?php
    $head = array(
        '#',
        'Bloco',
        'Descrição',
        'Pergunta',
        'Classes',
        ''
    );

    $body = array();

    foreach($collection as $item)
	{
        $body[] = array(
            $item['cd_pergunta'],
            array($item['ds_bloco'], 'text-align:left'),
            array(nl2br($item['ds_bloco_descricao']), 'text-align:justify'),
            array(nl2br($item['ds_pergunta']), 'text-align:justify'),
            array(implode(br(), $item['classe']), 'text-align:left'),
            anchor('cadastro/rh_pergunta/cadastro/'.$item['cd_pergunta'], '[editar]')
        );
    }

    $this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();