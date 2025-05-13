<?php
    $head = array( 
        'Ano/N�',
        'Dt. Inclus�o',
        'Usu�rio Inclus�o',
        'Descri��o',
        'Dt. Verificacao',
        'Usu�rio Verifica��o',
        'Descri��o Verifica��o'
    );

    $body = array();

    foreach($collection as $item)
	{
        $body[] = array(
            anchor('gestao/relato_ocorrencias/cadastro/'.$item['cd_relato_ocorrencias'], $item['nr_ano_numero_relato_ocorrencia']),
            $item['dt_inclusao'],
            $item['ds_usuario_inclusao'],
            array(nl2br($item['ds_relato_ocorrencias']), 'text-align:justify'),
            $item['dt_verificacao'],
            $item['ds_usuario_verificacao'],
            array(nl2br($item['ds_verificacao']), 'text-align:justify'),
        );
    }

    $this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();