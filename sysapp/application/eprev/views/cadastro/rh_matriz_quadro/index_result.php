<?php
    $head = array(
        '#',
        'Conceito A',
        'Conceito B',
        'Ação',
        ''
    );

    $body = array();

    foreach($collection as $item)
	{
        $body[] = array(
            $item['cd_matriz_quadro'],
            $item['ds_matriz_conceito_a'],
            $item['ds_matriz_conceito_b'],
            array($item['ds_matriz_acao'], 'text-align:left'),
            anchor('cadastro/rh_matriz_quadro/cadastro/'.$item['cd_matriz_quadro'], '[editar]')
        );
    }


    $this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();