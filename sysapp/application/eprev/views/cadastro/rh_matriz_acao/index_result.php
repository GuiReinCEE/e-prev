<?php
    $head = array(
        '#',
        'Descrição',
        'Progressão',
        'Promoção',
        '',
        ''
    );

    $body = array();

    foreach($collection as $item)
	{
        $body[] = array(
            $item['cd_matriz_acao'],
            array($item['ds_matriz_acao'], 'text-align:left'),
            $item['ds_progressao'],
            $item['ds_promocao'],
            '<div class="quadrado_matriz" style="background-color: '.$item['cor_fundo'].';">
            <span style="color:'.$item['cor_texto'].';">'.nl2br($item['ds_matriz_acao']).'</span>
            </div>',
            anchor('cadastro/rh_matriz_acao/cadastro/'.$item['cd_matriz_acao'], '[editar]')
        );
    }

    $this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();