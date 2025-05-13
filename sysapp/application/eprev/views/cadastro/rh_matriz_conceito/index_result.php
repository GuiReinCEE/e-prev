<?php
    $head = array(
        '#',
        'Conceito',
        'Média',
        ''
    );

    $body = array();

    foreach($collection as $item)
	{
        $body[] = array(
            $item['cd_matriz_conceito'],
            $item['ds_conceito'],
            number_format($item['nr_nota_min'], 2, ',', '.'). ' a ' .number_format($item['nr_nota_max'], 2, ',', '.'),
            anchor('cadastro/rh_matriz_conceito/cadastro/'.$item['cd_matriz_conceito'], '[editar]')
        );
    }

    $this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();